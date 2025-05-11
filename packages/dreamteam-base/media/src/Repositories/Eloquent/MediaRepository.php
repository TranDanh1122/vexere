<?php

namespace DreamTeam\Media\Repositories\Eloquent;

use Closure;
use Exception;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Media\Models\Media;
use DreamTeam\Media\Repositories\Interfaces\MediaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Media\Models\MediaFolder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;
use DreamTeam\Media\Facades\RvMedia;

class MediaRepository extends BaseRepository implements MediaRepositoryInterface
{
    protected string|null|Model $model = Media::class;
    protected $data;

    public function createName(string $name, int|string|null $folder): string
    {
        return Media::createName($name, $folder);
    }

    public function createSlug(string $name, string $extension, string|null $folderPath): string
    {
        return Media::createSlug($name, $extension, $folderPath);
    }

    public function getFilesByFolderId(
        int|string $folderId,
        array $params = [],
        bool $withFolders = true,
        array $folderParams = []
    ) {
        $params = array_merge([
            'order_by' => [
                'name' => 'ASC',
            ],
            'select' => [
                'medias.id as id',
                'medias.name as name',
                'medias.title as alt',
                'medias.url as url',
                'medias.mime_type as mime_type',
                'medias.size as size',
                'medias.created_at as created_at',
                'medias.updated_at as updated_at',
                'medias.options as options',
                'medias.folder_id as folder_id',
                DB::raw('0 as is_folder'),
                DB::raw('NULL as slug'),
                DB::raw('NULL as parent_id'),
                DB::raw('NULL as color'),
            ],
            'condition' => [],
            'recent_items' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'selected_file_id' => null,
            'is_popup' => false,
            'filter' => 'everything',
            'take' => null,
            'with' => [],
        ], $params);
        $this->data = $this->getModel();
        if ($withFolders) {
            $folderParams = array_merge([
                'condition' => [],
                'select' => [
                    'media_folders.id as id',
                    'media_folders.name as name',
                    DB::raw('NULL as url'),
                    DB::raw('NULL as mime_type'),
                    DB::raw('NULL as size'),
                    DB::raw('NULL as alt'),
                    'media_folders.created_at as created_at',
                    'media_folders.updated_at as updated_at',
                    DB::raw('NULL as options'),
                    DB::raw('NULL as folder_id'),
                    DB::raw('1 as is_folder'),
                    'media_folders.slug as slug',
                    'media_folders.parent_id as parent_id',
                    'media_folders.color as color',
                ],
            ], $folderParams);

            $folder = new MediaFolder();

            $folder = $folder
                ->where('parent_id', $folderId)
                ->select($folderParams['select']);

            $this->applyConditions($folderParams['condition'], $folder);
            $this->data = $this->data
                ->union($folder);
        }

        if (empty($folderId)) {
            $this->data = $this->data
                ->leftJoin('media_folders', 'media_folders.id', '=', 'medias.folder_id')
                ->where(function ($query) use ($folderId) {
                    /**
                     * @var Builder $query
                     */
                    $query
                        ->where(function ($sub) use ($folderId) {
                            /**
                             * @var Builder $sub
                             */
                            $sub
                                ->where('medias.folder_id', $folderId)
                                ->whereNull('medias.deleted_at');
                        })
                        ->orWhere(function ($sub) {
                            /**
                             * @var Builder $sub
                             */
                            $sub
                                ->whereNull('medias.deleted_at')
                                ->whereNotNull('media_folders.deleted_at');
                        })
                        ->orWhere(function ($sub) {
                            /**
                             * @var Builder $sub
                             */
                            $sub
                                ->whereNull('medias.deleted_at')
                                ->whereNull('media_folders.id');
                        });
                })
                ->withTrashed();
        } else if ($folderId != 'all') {
            $this->data = $this->data->where('medias.folder_id', $folderId);
        }

        if (isset($params['recent_items']) && is_array($params['recent_items']) && $params['recent_items']) {
            $this->data = $this->data->whereIn('medias.id', Arr::get($params, 'recent_items', []));
        }

        if ($params['selected_file_id'] && $params['is_popup']) {
            $this->data = $this->data->where('medias.id', '<>', $params['selected_file_id']);
        }

        $result = $this->getFile($params);

        if ($params['selected_file_id']) {
            if (!$params['paginate']['current_paged'] || $params['paginate']['current_paged'] == 1) {
                $currentFile = $this->getModel();

                $currentFile = $currentFile
                    ->where('medias.folder_id', $folderId)
                    ->where('id', $params['selected_file_id'])
                    ->select($params['select'])
                    ->first();
            }
        }

        if (isset($currentFile) && $params['is_popup']) {
            try {
                $result->prepend($currentFile);
            } catch (Exception $exception) {
                Log::error($exception);
            }
        }

        return $result;
    }

    protected function getFile(array $params)
    {
        $this->applyConditions($params['condition']);
        if ($params['filter'] != 'everything') {
            $this->data = $this->data->where(function (EloquentBuilder $query) use ($params) {
                /**
                 * @var EloquentBuilder $query
                 */
                $allMimes = [];
                foreach (RvMedia::getConfig('mime_types') as $key => $value) {
                    if ($key == $params['filter']) {
                        return $query->whereIn('medias.mime_type', $value);
                    }
                    $allMimes = array_unique(array_merge($allMimes, $value));
                }

                return $query->whereNotIn('medias.mime_type', $allMimes);
            });
        }

        if ($params['select']) {
            $this->data = $this->data->select($params['select']);
        }

        foreach ($params['order_by'] as $column => $direction) {
            $this->data = $this->data->orderBy($column, $direction);
        }

        foreach ($params['with'] as $with) {
            $this->data = $this->data->with($with);
        }

        if ($params['take'] == 1) {
            $result = $this->data->first();
        } elseif ($params['take']) {
            $result = $this->data->take($params['take'])->get();
        } elseif ($params['paginate']['per_page']) {
            $paged = $params['paginate']['current_paged'] ?: 1;
            $result = $this->data
                ->skip(($paged - 1) * $params['paginate']['per_page'])
                ->limit($params['paginate']['per_page'])
                ->get();
        } else {
            $result = $this->data->get();
        }

        if (
            !empty($params['selected_file_id'])
            && !$params['paginate']['current_paged']
            || $params['paginate']['current_paged'] == 1
        ) {
            $currentFile = $this->getModel()
                ->where('id', $params['selected_file_id'])
                ->select($params['select'])
                ->first();
        }

        if (isset($currentFile) && $params['is_popup']) {
            try {
                /** @var BaseModel $currentFile */
                $result->prepend($currentFile);
            } catch (Exception $exception) {
                Log::error($exception);
            }
        }

        $this->data = $this->getModel();

        return $result;
    }

    public function getTrashed(
        int|string $folderId,
        array $params = [],
        bool $withFolders = true,
        array $folderParams = []
    ): Collection {
        $params = array_merge([
            'order_by' => [
                'name' => 'ASC',
            ],
            'select' => [
                'medias.id as id',
                'medias.name as name',
                'medias.url as url',
                'medias.mime_type as mime_type',
                'medias.size as size',
                'medias.created_at as created_at',
                'medias.updated_at as updated_at',
                'medias.options as options',
                'medias.folder_id as folder_id',
                DB::raw('0 as is_folder'),
                DB::raw('NULL as slug'),
                DB::raw('NULL as parent_id'),
            ],
            'condition' => [],
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'filter' => 'everything',
            'take' => null,
            'with' => [],
        ], $params);

        $this->data = $this->getModel()->onlyTrashed();

        if ($withFolders) {
            $folderParams = array_merge([
                'condition' => [],
                'select' => [
                    'media_folders.id as id',
                    'media_folders.name as name',
                    DB::raw('NULL as url'),
                    DB::raw('NULL as mime_type'),
                    DB::raw('NULL as size'),
                    'media_folders.created_at as created_at',
                    'media_folders.updated_at as updated_at',
                    DB::raw('NULL as options'),
                    DB::raw('NULL as folder_id'),
                    DB::raw('1 as is_folder'),
                    'media_folders.slug as slug',
                    'media_folders.parent_id as parent_id',
                ],
            ], $folderParams);

            $folder = new MediaFolder();

            $folder = $folder
                ->withTrashed()
                ->whereNotNull('media_folders.deleted_at')
                ->select($folderParams['select']);

            if (empty($folderId)) {
                /**
                 * @var Builder $folder
                 */
                $folder = $folder->leftJoin(
                    'media_folders as mf_parent',
                    'mf_parent.id',
                    '=',
                    'media_folders.parent_id'
                )
                    ->where(function ($query) {
                        /**
                         * @var Builder $query
                         */
                        $query
                            ->orWhere('media_folders.parent_id', 0)
                            ->orWhereNull('mf_parent.deleted_at');
                    })
                    ->withTrashed();
            } else {
                $folder = $folder->where('media_folders.parent_id', $folderId);
            }

            $this->applyConditions($folderParams['condition'], $folder);

            $this->data = $this->data
                ->union($folder);
        }

        if (empty($folderId)) {
            $this->data = $this->data
                ->leftJoin('media_folders', 'media_folders.id', '=', 'medias.folder_id')
                ->where(function ($query) {
                    $query
                        ->where('medias.folder_id', 0)
                        ->orWhereNull('media_folders.deleted_at');
                });
        } else {
            $this->data = $this->data->where('medias.folder_id', $folderId);
        }

        return $this->getFile($params);
    }

    public function emptyTrash(): bool
    {
        $this->getModel()->onlyTrashed()->each(fn (Media $file) => $file->forceDelete());

        return true;
    }

    public function deleteBy(array $condition = []): bool
    {
        $this->applyConditions($condition);

        $data = $this->data->get();

        if ($data->isEmpty()) {
            return false;
        }

        foreach ($data as $item) {
            $item->delete();
        }

        $this->data = $this->getModel();

        return true;
    }

    public function forceDelete(array $condition = [])
    {
        $this->applyConditions($condition);

        $item = $this->data->withTrashed()->first();
        if (! empty($item)) {
            $item->forceDelete();
        }
    }

    public function restoreBy(array $condition = [])
    {
        $this->applyConditions($condition);

        $item = $this->data->withTrashed()->first();
        if (! empty($item)) {
            $item->restore();
        }
    }

    protected function applyConditions(array $where, &$model = null)
    {
        if (!$model) {
            $newModel = $this->data ? $this->data : $this->getModel();
        } else {
            $newModel = $model;
        }

        foreach ($where as $field => $value) {
            if ($value instanceof Closure) {
                $newModel = $value($newModel);

                continue;
            }

            if (is_array($value)) {
                [$field, $condition, $val] = $value;

                $newModel = match (strtoupper($condition)) {
                    'IN' => $newModel->whereIn($field, $val),
                    'NOT_IN' => $newModel->whereNotIn($field, $val),
                    default => $newModel->where($field, $condition, $val),
                };
            } else {
                $newModel = $newModel->where($field, $value);
            }
        }

        if (!$model) {
            $this->data = $newModel;
        } else {
            $data = $newModel;
        }
    }
}
