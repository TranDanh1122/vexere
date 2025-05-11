<?php

namespace DreamTeam\Media\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController as BaseController;
use DreamTeam\Media\Chunks\Exceptions\UploadMissingFileException;
use DreamTeam\Media\Chunks\Handler\DropZoneUploadHandler;
use DreamTeam\Media\Chunks\Receiver\FileReceiver;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * @since 24/06/2024 07:50 AM
 */
class MediaFileController extends BaseController
{
    public function postUpload(Request $request)
    {
        try {
            if (! RvMedia::isChunkUploadEnabled()) {
                if (!empty($request->custom_upload)) {
                    $results = [];
                    $files = $request->file('file');
                    foreach ($files as $file) {
                        $result = RvMedia::handleUpload($file, $request->input('folder_id', 0), null, false, $request->get('allow_webp', 'no'), $request->get('allow_thumb', 'no'), $request->get('module_name', null));
                        if (! $result['error']) {
                            $results[] = [
                                'id' => $result['data']->id,
                                'refresh_folder' => $result['refresh_folder'] ?? false,
                                'folder_id' => $result['data']->folder_id ?? 0,
                                'src' => RvMedia::url($result['data']->url),
                                'url' => $result['data']->url,
                            ];
                        }
                    }
                    if (! $results) {
                        return RvMedia::responseError('Không có file nào hợp lệ');
                    }

                    return RvMedia::responseSuccess($results);
                }
                $result = RvMedia::handleUpload(Arr::first($request->file('file')), $request->input('folder_id', 0), null, false, $request->get('allow_webp', 'no'), $request->get('allow_thumb', 'no'), $request->get('module_name', null));

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
                'url' => $result['data']->url,
            ]);
        }

        return RvMedia::responseError($result['message']);
    }

    public function postUploadFromEditor(Request $request)
    {
        return RvMedia::uploadFromEditor($request);
    }

    public function postDownloadUrl(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'url' => 'required|url',
            'folderId' => 'nullable|integer',
            'makeRealPath' => 'nullable|string|in:yes,no',
            'allow_webp' => 'nullable|string|in:yes,no',
            'allow_thumb' => 'nullable|in:1,yes,no',
            'module_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return RvMedia::responseError($validator->messages()->first());
        }
        $folderSlug = null;
        if ($request->get('makeRealPath', 'no') == 'yes') {
            $filePath = RvMedia::getRelativePathFromUrl($request->input('url'));
            $paths = array_filter(explode('/', $filePath));
            array_pop($paths);
            $folderSlug = implode('/', $paths);
        }
        $result = RvMedia::uploadFromUrl($request->input('url'), $request->input('folderId', 0), $folderSlug, null, $request->get('allow_webp', 'no'), $request->get('allow_thumb', 'no'), $request->get('module_name', null));

        if (! $result['error']) {
            return RvMedia::responseSuccess([
                'id' => $result['data']->id,
                'src' => Storage::url($result['data']->url),
                'url' => $result['data']->url,
                'refresh_folder' => $result['refresh_folder'] ?? false,
                'folder_id' => $result['data']->folder_id ?? 0,
                'message' => trans('media::media.javascript.message.success_header'),
            ]);
        }

        return RvMedia::responseError($result['message']);
    }
}
