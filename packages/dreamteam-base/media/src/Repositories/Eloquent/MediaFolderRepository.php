<?php

namespace DreamTeam\Media\Repositories\Eloquent;

use Closure;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Media\Models\MediaFolder;
use DreamTeam\Media\Repositories\Interfaces\MediaFolderInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;

/**
 * @since 19/08/2015 07:45 AM
 */
class MediaFolderRepository extends BaseRepository implements MediaFolderInterface
{
    protected string|null|Model $model = MediaFolder::class;

    protected $data;
    
    public function getFolderByParentId(int|string|null $folderId, array $params = [], bool $withTrash = false)
    {
        $params = array_merge([
            'condition' => [],
        ], $params);

        if (! $folderId) {
            $folderId = null;
        }

        $this->data = $this->getModel()->where('parent_id', $folderId);

        if ($withTrash) {
            $this->data = $this->data->withTrashed();
        }

        return $this->advancedGet($params);
    }

    public function createSlug(string $name, int|string|null $parentId): string
    {
        return MediaFolder::createSlug($name, $parentId);
    }

    public function createName(string $name, int|string|null $parentId): string
    {
        return MediaFolder::createName($name, $parentId);
    }

    public function getBreadcrumbs(int|string|null $parentId, array $breadcrumbs = [])
    {
        if (! $parentId) {
            return $breadcrumbs;
        }

        $folder = $this->getFirstByWithTrash(['id' => $parentId]);

        if (empty($folder)) {
            return $breadcrumbs;
        }

        $child = $this->getBreadcrumbs($folder->parent_id, $breadcrumbs);

        return array_merge($child, [
            [
                'id' => $folder->id,
                'name' => $folder->name,
            ],
        ]);
    }

    public function advancedGet(array $params = [])
    {
        $params = array_merge([
            'condition' => [],
            'order_by' => [],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => ['*'],
            'with' => [],
            'withCount' => [],
            'withAvg' => [],
        ], $params);

        $this->applyConditions($params['condition']);

        $data = $this->data;

        if ($params['select']) {
            $data = $data->select($params['select']);
        }

        foreach ($params['order_by'] as $column => $direction) {
            if (! in_array(strtolower($direction), ['asc', 'desc'])) {
                continue;
            }

            if ($direction !== null) {
                $data = $data->orderBy($column, $direction);
            }
        }

        if (! empty($params['with'])) {
            $data = $data->with($params['with']);
        }

        if (! empty($params['withCount'])) {
            $data = $data->withCount($params['withCount']);
        }

        if (! empty($params['withAvg'])) {
            $data = $data->withAvg($params['withAvg'][0], $params['withAvg'][1]);
        }

        if ($params['take'] == 1) {
            $result = $this->applyBeforeExecuteQuery($data, true)->first();
        } elseif ($params['take'] && $params['take'] > 0) {
            $result = $this->applyBeforeExecuteQuery($data)->take((int)$params['take'])->get();
        } elseif ($params['paginate']['per_page']) {
            $paginateType = 'paginate';

            if (Arr::get($params, 'paginate.type') && method_exists($data, Arr::get($params, 'paginate.type'))) {
                $paginateType = Arr::get($params, 'paginate.type');
            }

            $originalModel = $this->getModel();

            $perPage = (int)Arr::get($params, 'paginate.per_page') ?: 10;

            $currentPage = (int)Arr::get($params, 'paginate.current_paged', 1) ?: 1;

            $result = $this->applyBeforeExecuteQuery($data)
                ->$paginateType(
                    $perPage > 0 ? $perPage : 10,
                    [$originalModel->getTable() . '.' . $originalModel->getKeyName()],
                    'page',
                    $currentPage > 0 ? $currentPage : 1
                );
        } else {
            $result = $this->applyBeforeExecuteQuery($data)->get();
        }

        return $result;
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

    public function getTrashed(int|string|null $parentId, array $params = [])
    {
        $params = array_merge([
            'where' => [],
        ], $params);
        $data = $this->getModel()
            ->select('media_folders.*')
            ->where($params['where'])
            ->orderBy('media_folders.name')
            ->onlyTrashed();

        /**
         * @var Builder $data
         */
        if (! $parentId) {
            $data->leftJoin('media_folders as mf_parent', 'mf_parent.id', '=', 'media_folders.parent_id')
                ->where(function ($query) {
                    /**
                     * @var Builder $query
                     */
                    $query
                        ->orWhere('media_folders.parent_id', 0)
                        ->orWhere('mf_parent.deleted_at', null);
                })
                ->withTrashed();
        } else {
            $data->where('media_folders.parent_id', $parentId);
        }

        return $data->get();
    }

    public function deleteFolder(int|string|null $folderId, bool $force = false)
    {
        $child = $this->getFolderByParentId($folderId, [], $force);
        foreach ($child as $item) {
            $this->deleteFolder($item->id, $force);
        }

        if ($force) {
            $this->forceDelete(['id' => $folderId]);
        } else {
            $this->deleteBy(['id' => $folderId]);
        }
    }

    public function getAllChildFolders(int|string|null $parentId, array $child = [])
    {
        if (! $parentId) {
            return $child;
        }

        $folders = $this->allBy(['parent_id' => $parentId]);

        if (! empty($folders)) {
            foreach ($folders as $folder) {
                $child[$parentId][] = $folder;

                return $this->getAllChildFolders($folder->id, $child);
            }
        }

        return $child;
    }

    public function getFullPath(int|string|null $folderId, string|null $path = ''): string|null
    {
        return MediaFolder::getFullPath($folderId, $path);
    }

    public function restoreFolder(int|string|null $folderId)
    {
        $child = $this->getFolderByParentId($folderId, [], true);
        foreach ($child as $item) {
            $this->restoreFolder($item->id);
        }

        $this->restoreBy(['id' => $folderId]);
    }

    public function emptyTrash(): bool
    {
        $this->model->onlyTrashed()->each(fn (MediaFolder $folder) => $folder->forceDelete());

        return true;
    }

    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        $this->applyConditions($condition);

        $query = $this->data->withTrashed();

        if (! empty($select)) {
            return $query->select($select)->first();
        }

        return $this->applyBeforeExecuteQuery($query, true)->first();
    }

    public function allBy(array $condition, array $with = [], array $select = ['*'])
    {
        $this->applyConditions($condition);

        $data = $this->make($with)->select($select);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    protected function applyConditions(array $where, &$model = null)
    {
        if (! $model) {
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

        if (! $model) {
            $this->data = $newModel;
        } else {
            $data = $newModel;
        }
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

    public function applyBeforeExecuteQuery($data, bool $isSingle = false)
    {
        $data = $this->runApplyBeforeExecuteQuery($data, $this->getModel(), $isSingle);

        $this->data = $this->getModel();

        return $data;
    }

    protected function runApplyBeforeExecuteQuery($data, $model, bool $isSingle = false)
    {
        $filter = $isSingle ? BASE_FILTER_BEFORE_GET_SINGLE : BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM;

        if (is_in_admin()) {
            $filter = $isSingle ? BASE_FILTER_BEFORE_GET_ADMIN_SINGLE_ITEM : BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM;
        }

        return apply_filters($filter, $data, $model);
    }

}
