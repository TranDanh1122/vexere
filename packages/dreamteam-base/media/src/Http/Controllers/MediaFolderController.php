<?php

namespace DreamTeam\Media\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController as BaseController;
use DreamTeam\Media\Facades\RvMedia;
use DreamTeam\Media\Http\Requests\MediaFolderRequest;
use DreamTeam\Media\Models\MediaFolder;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * @since 24/06/2024 07:50 AM
 */
class MediaFolderController extends BaseController
{
    public function store(MediaFolderRequest $request)
    {
        try {
            $name = $request->input('name');
            $parentId = $request->input('parent_id');

            MediaFolder::query()->create([
                'name' => MediaFolder::createName($name, $parentId),
                'slug' => MediaFolder::createSlug($name, $parentId),
                'parent_id' => $parentId,
                'user_id' => Auth::guard('admin')->id(),
            ]);

            return RvMedia::responseSuccess([], trans('media::media.folder_created'));
        } catch (Exception $exception) {
            return RvMedia::responseError($exception->getMessage());
        }
    }
}
