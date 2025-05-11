<?php

namespace DreamTeam\Media\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DreamTeam\Media\Chunks\Exceptions\UploadMissingFileException;
use DreamTeam\Media\Chunks\Handler\DropZoneUploadHandler;
use DreamTeam\Media\Chunks\Receiver\FileReceiver;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class MediaController extends Controller
{
    public function uploadMedia(Request $request)
    {
        // dd($request->file('file'));
        try {
            if (! RvMedia::isChunkUploadEnabled()) {
                $result = RvMedia::handleUpload($request->file('file'), $request->input('folder_id', 0), null, false, $request->get('allow_webp', 'no'), $request->get('allow_thumb', 'no'), $request->get('module_name', null));

                return $this->handleUploadResponse($result);
            }

            // Create the file receiver
            $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // Check if the upload is success, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }
            // Receive the file
            $save = $receiver->receive();
            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                $result = RvMedia::handleUpload($save->getFile(), $request->input('folder_id', 0));

                return $this->handleUploadResponse($result);
            }
            // We are in chunk mode, lets send the current progress
            $handler = $save->handler();

            return response()->json([
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Throwable $exception) {
            return RvMedia::responseError($exception->getMessage());
        }
    }

    protected function handleUploadResponse(array $result): JsonResponse
    {
        if (! $result['error']) {
            return RvMedia::responseSuccess([
                'id' => $result['data']->id,
                'refresh_folder' => $result['refresh_folder'] ?? false,
                'folder_id' => $result['data']->folder_id ?? 0,
                'src' => RvMedia::url($result['data']->url),
            ]);
        }

        return RvMedia::responseError($result['message']);
    }
}
