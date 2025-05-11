<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The repository associated main model.
     *
     * @var Model|null|string
     */
    protected string|null|Model $model;
    /**
     * The repository associated request.
     *
     * @var Request|null
     */
    protected ?Request $request;
    /**
     * Default attributes to automatically except from request treatments.
     *
     * @var array
     */
    protected array $defaultAttributesToExcept = ['_token', '_method'];
    /**
     * Automatically except defined $defaultAttributesToExcept from the request treatments.
     *
     * @var boolean
     */
    protected bool $exceptDefaultAttributes = true;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        if ($this->model) {
            $this->setModel($this->model);
        }
        $this->setRequest(request());
    }

    public function setModel(string $modelClass): BaseRepositoryInterface
    {
        $this->model = app($modelClass);

        return $this;
    }

    public function setRequest(Request $request): BaseRepositoryInterface
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Create multiple model instances from the request data.
     * The use of this method suppose that your request is correctly formatted.
     * If not, you can use the $exceptFromSaving and $addToSaving attributes to do so.
     *
     * @param array $attributesToAddOrReplace (dot notation accepted)
     * @param array $attributesToExcept       (dot notation accepted)
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Collection
     */
    public function createOrUpdateMultipleFromRequest(
        array $attributesToAddOrReplace = [],
        array $attributesToExcept = [],
        bool $saveMissingModelFillableAttributesToNull = true
    ): Collection {
        $this->exceptAttributesFromRequest($attributesToExcept);
        $this->addOrReplaceAttributesInRequest($attributesToAddOrReplace);

        return $this->createOrUpdateMultipleFromArray($this->request->all(), $saveMissingModelFillableAttributesToNull);
    }

    /**
     * Except attributes from request.
     *
     * @param array $attributesToExcept (dot notation accepted)
     *
     * @return void
     */
    protected function exceptAttributesFromRequest(array $attributesToExcept = []): void
    {
        if ($this->exceptDefaultAttributes) {
            $attributesToExcept = array_merge($this->defaultAttributesToExcept, $attributesToExcept);
        }
        $this->request->replace($this->request->except($attributesToExcept));
    }

    /**
     * Add or replace attributes in request.
     *
     * @param array $attributesToAddOrReplace (dot notation accepted)
     *
     * @return void
     */
    protected function addOrReplaceAttributesInRequest(array $attributesToAddOrReplace = []): void
    {
        $attributesToAddOrReplaceArray = [];
        foreach ($attributesToAddOrReplace as $key => $value) {
            Arr::set($attributesToAddOrReplaceArray, $key, $value);
        }
        $newRequestAttributes = array_replace_recursive($this->request->all(), $attributesToAddOrReplaceArray);
        $this->request->replace($newRequestAttributes);
    }

    /**
     * Create one or more model instances from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Collection
     */
    public function createOrUpdateMultipleFromArray(
        array $data,
        bool $saveMissingModelFillableAttributesToNull = true
    ): Collection {
        $models = new Collection();
        foreach ($data as $instanceData) {
            $models->push($this->createOrUpdateFromArray($instanceData, $saveMissingModelFillableAttributesToNull));
        }

        return $models;
    }

    /**
     * Create or update a model instance from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Model
     */
    public function createOrUpdateFromArray(array $data, bool $saveMissingModelFillableAttributesToNull = true): Model
    {
        $primary = $this->getModelPrimaryFromArray($data);
        return $primary
            ? $this->updateByPrimary($primary, $data, $saveMissingModelFillableAttributesToNull)
            : $this->getModel()->create($data);
    }

    /**
     * Create or update a model instance from data array by conditions.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $conditions
     * @param array $data
     *
     * @return Model
     */
    public function createOrUpdateFromArrayByConditions(array $conditions, array $data = []): Model
    {
        return $conditions
            ? $this->getModel()->updateOrCreate($conditions, $data)
            : $this->getModel()->create(array_merge($conditions, $data));
    }

    /**
     * Create or get First a model instance from data array by conditions.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param array $conditions
     *
     * @return Model
     */
    public function firstOrCreate(array $uniqueData, array $data): Model
    {
        return $this->getModel()->firstOrCreate($uniqueData, $data);
    }

    /**
     * Create a model instance from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Model
     */
    public function createFromArray(array $data, bool $saveMissingModelFillableAttributesToNull = true): Model
    {
        $primary = $this->getModelPrimaryFromArray($data);
        if ($primary) {
            $instance = $this->getModel()->find($primary);
            if ($instance) {
                $data = $saveMissingModelFillableAttributesToNull ? $this->setMissingFillableAttributesToNull($data) : $data;
                $instance->update($data);
            }
        }
        $instance = $this->getModel()->create($data);
        return $instance;
    }

    /**
     * Insert Multiple a model instance from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @return bool
     */
    public function insertMultipleFromArray(array $data): bool
    {
        return $this->getModel()->insert($data);
    }

    /**
     * Get model primary value from a data array.
     *
     * @param array $data
     *
     * @return mixed
     * @throws ModelNotFoundException
     */
    protected function getModelPrimaryFromArray(array $data): mixed
    {
        $primaryKey = $this->getModel()->getKeyName();
        if (!is_array($primaryKey)) {
            $primaryKey = [$primaryKey];
        }
        if (count($primaryKey) == 1) {
            return Arr::get($data, $primaryKey[0]);
        }
        $primaryKeyValue = [];
        foreach ($primaryKey as $key) {
            $primaryKeyValue[$key] = Arr::get($data, $key);
        }
        return $primaryKeyValue;
    }

    /**
     * Get the repository model.
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    protected function getModel(): Model
    {
        if ($this->model instanceof Model) {
            return $this->model;
        }
        throw new ModelNotFoundException(
            'You must declare your repository $model attribute with an Illuminate\Database\Eloquent\Model '
                . 'namespace to use this feature.'
        );
    }

    /**
     * Get the table name of repository model.
     *
     * @return Table name
     */
    protected function getTableName(): string
    {
        return $this->getModel()->getTable();
    }



    /**
     * Update a model instance from its primary key.
     *
     * @param int   $primary
     * @param array $data
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Model
     */
    public function updateByPrimary(
        int $primary,
        array $data,
        bool $saveMissingModelFillableAttributesToNull = true
    ): Model {
        $instance = $this->getModel()->findOrFail($primary);
        $data = $saveMissingModelFillableAttributesToNull ? $this->setMissingFillableAttributesToNull($data) : $data;
        $instance->update($data);

        return $instance;
    }

    /**
     * Update a model instance from where conditions.
     *
     * @param array   $conditions
     * @param array $data
     *
     * @return Model
     */
    public function updateFromWhereConditions(array $conditions, array $data): int
    {
        $collection = $this->getModel()->where($conditions)->update($data);
        return $collection;
    }

    /**
     * Add the missing model fillable attributes with a null value.
     *
     * @param array $data
     *
     * @return array
     */
    public function setMissingFillableAttributesToNull(array $data): array
    {
        $fillableAttributes = $this->getModel()->getFillable();
        $dataWithMissingAttributesToNull = [];
        foreach ($fillableAttributes as $fillableAttribute) {
            $dataWithMissingAttributesToNull[$fillableAttribute] =
                isset($data[$fillableAttribute]) ? $data[$fillableAttribute] : null;
        }

        return $dataWithMissingAttributesToNull;
    }

    /**
     * Create or update a model instance from the request data.
     * The use of this method suppose that your request is correctly formatted.
     * If not, you can use the $exceptFromSaving and $addToSaving attributes to do so.
     *
     * @param array $attributesToAddOrReplace (dot notation accepted)
     * @param array $attributesToExcept       (dot notation accepted)
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Model
     */
    public function createOrUpdateFromRequest(
        array $attributesToAddOrReplace = [],
        array $attributesToExcept = [],
        bool $saveMissingModelFillableAttributesToNull = true
    ): Model {
        $this->exceptAttributesFromRequest($attributesToExcept);
        $this->addOrReplaceAttributesInRequest($attributesToAddOrReplace);

        return $this->createOrUpdateFromArray($this->request->all(), $saveMissingModelFillableAttributesToNull);
    }

    /**
     * Delete a model instance from the request data.
     *
     * @param array $attributesToAddOrReplace (dot notation accepted)
     * @param array $attributesToExcept       (dot notation accepted)
     *
     * @return bool|null
     * @throws ModelNotFoundException
     */
    public function deleteFromRequest(array $attributesToAddOrReplace = [], array $attributesToExcept = []): ?bool
    {
        $this->exceptAttributesFromRequest($attributesToExcept);
        $this->addOrReplaceAttributesInRequest($attributesToAddOrReplace);

        return $this->deleteFromArray($this->request->all());
    }

    /**
     * Delete a model instance from a data array.
     *
     * @param array $data
     *
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteFromArray(array $data): bool
    {
        $primary = $this->getModelPrimaryFromArray($data);

        return $this->getModel()->findOrFail($primary)->delete();
    }

    public function deleteFromWhereCondition(array $condition): bool
    {
        return $this->getModel()->where($condition)->delete();
    }


    /**
     * Delete a model instance from its primary key.
     *
     * @param int $primary
     *
     * @return bool|null
     * @throws ModelNotFoundException
     */
    public function deleteByPrimary(int $primary): ?bool
    {
        return $this->getModel()->findOrFail($primary)->delete();
    }

    /**
     * Delete multiple model instances from their primary keys.
     *
     * @param array $instancePrimaries
     *
     * @return int
     * @throws ModelNotFoundException
     */
    public function deleteMultipleFromPrimaries(array $instancePrimaries): int
    {
        return $this->getModel()->destroy($instancePrimaries);
    }

    /**
     * Paginate array results.
     *
     * @param array $data
     * @param int   $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateArrayResults(array $data, int $perPage = 20): LengthAwarePaginator
    {
        $page = $this->request->input('page', 1);
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($data, $offset, $perPage, false),
            count($data),
            $perPage,
            $page,
            [
                'path'  => $this->request->url(),
                'query' => $this->request->query(),
            ]
        );
    }

    /**
     * Find one model instance from its primary key value.
     *
     * @param int  $primary
     * @param bool $throwsExceptionIfNotFound
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public function findOneByPrimary(int $primary, $throwsExceptionIfNotFound = true): ?Model
    {
        return $throwsExceptionIfNotFound
            ? $this->getModel()->findOrFail($primary)
            : $this->getModel()->find($primary);
    }

    /**
     * Find one model instance from an associative array.
     *
     * @param array $data
     * @param bool $throwsExceptionIfNotFound
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public function findOneFromArray(array $data, bool $throwsExceptionIfNotFound = true, bool $hasLocale = false, string|null $locale = null): ?Model
    {
        $item = $this->getModel()->where($data);
        if ($hasLocale) {
            $item = $item->whereHas('language_metas', function ($query) use ($locale) {
                return $query->where('lang_locale', $locale ?: App::getLocale());
            });
        }
        if ($throwsExceptionIfNotFound) {
            $item = $item->firstOrFail();
        } else {
            $item = $item->first();
        }
        return $item;
    }

    /**
     * Find multiple model instances from a « where » parameters array.
     *
     * @param array $data
     *
     * @return Collection
     * @throws ModelNotFoundException
     */
    public function findMultipleFromArray(array $data, bool $hasLocale = false, string $select = '*'): Collection
    {
        $items = $this->getModel()->where($data);
        if ($hasLocale) {
            $items = $items->whereHas('language_metas', function ($query) {
                return $query->where('lang_locale', App::getLocale());
            });
        }
        return $items->selectRaw($select)->get();
    }

    /**
     * Get all model instances from database.
     *
     * @param array $columns
     * @param string $orderBy
     * @param string $orderByDirection
     *
     * @return Collection
     * @throws ModelNotFoundException
     */
    public function getAll(array $columns = ['*'], string $orderBy = 'default', string $orderByDirection = 'asc'): Collection
    {
        $orderBy = $orderBy === 'default' ? $this->getModel()->getKeyName() : $orderBy;

        return $this->getModel()->orderBy($orderBy, $orderByDirection)->get($columns);
    }

    /**
     * Instantiate a model instance with an attributes array.
     *
     * @param array $data
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    public function make(array $data): Model
    {
        return app($this->getModel()->getMorphClass())->fill($data);
    }

    /**
     * Get the model unique storage instance or create one.
     *
     * @return Model
     */
    public function modelUniqueInstance(): Model
    {
        $modelInstance = $this->getModel()->first();
        if (! $modelInstance) {
            $modelInstance = $this->getModel()->create([]);
        }

        return $modelInstance;
    }

    /**
     * Find multiple model instances from an array of ids.
     *
     * @param array $primaries
     *
     * @return Collection
     */
    public function findMultipleFromPrimaries(array $primaries): Collection
    {
        return $this->getModel()->findMany($primaries);
    }

    /**
     * Find multiple model instances from an array of ids.
     *
     * @param array $conditions
     *
     * @return Collection
     */
    public function getMultipleFromConditions(array $with, array $conditions, string $orderByName = 'id', string $orderByValue = 'asc', bool $hasLocale = false, string|null $locale = null, int $limit = -1): Collection
    {
        $data = $this->getModel();
        if (count($with)) {
            $data = $data->with($with);
        }
        if ($hasLocale) {
            $data = $data->whereHas('language_metas', function ($query) use ($locale) {
                return $query->where('lang_locale', $locale ?: App::getLocale());
            });
        }
        $data = $data->where($conditions)->orderBy($orderByName, $orderByValue);
        if ($limit > 0) {
            $data = $data->limit($limit);
        }
        return $data->get();
    }

    /**
     * Find multiple model instances from an array of ids.
     *
     * @param array $conditions
     *
     * @return Collection
     */
    public function getWithMultiFromConditions(array $with, array $conditions, string $orderByName, string $orderByValue, bool $hasLocale = false, string $select = '*', string|null $locale = null): Collection
    {
        $q = $this->getModel();
        if (count($with)) {
            $q = $q->with($with);
        }
        if ($hasLocale) {
            $q = $q->whereHas('language_metas', function ($query) use ($locale) {
                return $query->where('lang_locale', $locale ?: App::getLocale());
            });
        }
        return $this->applyWhereConditions($q, $conditions)->selectRaw($select)->orderBy($orderByName, $orderByValue)->get();
    }

    /**
     * Find one model instance from an associative array.
     *
     * @param array $with
     * @param array $data
     * @param bool $throwsExceptionIfNotFound
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public function findOneWithFromArray(array $with, array $data, bool $throwsExceptionIfNotFound = true, bool $hasLocale = false, string|null $locale = null): ?Model
    {
        $item = $this->getModel();
        if (count($with)) {
            $item = $item->with($with);
        }
        if ($hasLocale) {
            $item = $item->whereHas('language_metas', function ($query) use ($locale) {
                return $query->where('lang_locale', $locale ?: App::getLocale());
            });
        }
        if ($throwsExceptionIfNotFound) {
            return $item->where($data)->firstOrFail();
        }
        return $item->where($data)->first();
    }

    /**
     * Find one model instance from an associative array.
     *
     * @param array $with
     * @param array $conditions
     * @param bool $throwsExceptionIfNotFound
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public function findOneWithFromConditions(array $with, array $conditions, string $orderByName = 'id', string $orderByValue = 'desc', bool $hasLocale = true, string $select = '*', bool $throwsExceptionIfNotFound = true): ?Model
    {
        $q = $this->getModel();
        if (count($with)) {
            $q = $q->with($with);
        }
        if ($hasLocale) {
            $q = $q->whereHas('language_metas', function ($query) {
                return $query->where('lang_locale', App::getLocale());
            });
        }
        $q = $this->applyWhereConditions($q, $conditions)
            ->selectRaw($select)->orderBy($orderByName, $orderByValue);
        return $throwsExceptionIfNotFound ? $q->firstOrFail() : $q->first();
    }

    /**
     * Find data for ListData table
     * 
     * @param array $with with relationship
     * @param array $conditions where
     * @param array $customConditions where custom call action
     * @param bool  $hasLocale have join language metas
     * @param array $orders sort data
     * 
     * @return LengthAwarePaginator
     */
    public function getDataTable(Request $request, array $with, array $conditions, array $customConditions, array $orders, int $pageSize, bool $hasLocale = false): LengthAwarePaginator|Collection
    {
        $tableName = $this->getTableName();
        $datas = $this->getModel()
            ->select($tableName . '.*');
        if (count($with)) {
            $datas = $datas->with($with);
        }
        if ($hasLocale) {
            $datas = $datas->join('language_metas', 'language_metas.lang_table_id', $tableName . '.id')
                ->where('lang_table', $tableName)
                ->where('lang_locale', $request->lang_locale ?? $_COOKIE['table_locale'] ?? App::getLocale())
                ->addSelect('language_metas.*');
        }
        $datas = $this->applyWhereConditions($datas, $conditions);
        if (defined('FILTER_LIST_DATA_TABLE_QUERY')) {
            $datas = apply_filters(FILTER_LIST_DATA_TABLE_QUERY, $datas, $tableName, $conditions, $customConditions, $request);
        }
        if (count($orders)) {
            foreach ($orders as $orderByKey => $orderByValue) {
                $datas = $datas->orderBy($orderByKey, $orderByValue);
            }
        }
        if($pageSize == -1) return $datas->get();
        return $datas->paginate($_COOKIE['dreamteam_page_size'] ?? $pageSize);
    }

    /**
     * Find data for ListData table
     * 
     * @param array $with with relationship
     * @param array $conditions where
     * @param array $customConditions where custom call action
     * @param bool  $hasLocale have join language metas
     * @param array $orders sort data
     * 
     * @return Collection
     */
    public function getListDataCategoryTable(Request $request, array $with, array $conditions, array $customConditions, array $orders, bool $hasLocale = false): Collection
    {
        $tableName = $this->getTableName();
        $datas = $this->getModel()
            ->select($tableName . '.*');
        if (count($with)) {
            $datas = $datas->with($with);
        }
        if ($hasLocale) {
            $datas = $datas->join('language_metas', 'language_metas.lang_table_id', $tableName . '.id')
                ->where('lang_table', $tableName)
                ->where('lang_locale', $request->lang_locale ?? $_COOKIE['table_locale'] ?? App::getLocale())
                ->addSelect('language_metas.*');
        }
        $datas = $this->applyWhereConditions($datas, $conditions);
        if (defined('FILTER_LIST_DATA_CATEGORY_TABLE_QUERY')) {
            $datas = apply_filters(FILTER_LIST_DATA_CATEGORY_TABLE_QUERY, $datas, $tableName, $conditions, $customConditions, $request);
        }
        if (count($orders)) {
            foreach ($orders as $orderByKey => $orderByValue) {
                $datas = $datas->orderBy($orderByKey, $orderByValue);
            }
        }
        return $datas->get();
    }



    /**
     * Find list data
     * 
     * @param array $with with relationship
     * @param array $conditions where
     * @param bool  $hasLocale have join language metas
     * @param array $orders sort data
     * @param int   $pageSize per page number
     * @param string $select select data
     * 
     * @return LengthAwarePaginator
     */
    public function getIndexData(array $with, array $conditions, array $orders, string $select = '*', int $pageSize = 16, bool $hasLocale = false, null|string $locale = null): LengthAwarePaginator
    {
        $datas = $this->getModel();
        if (count($with)) {
            $datas = $datas->with($with);
        }
        if ($hasLocale) {
            $datas = $datas->whereHas('language_metas', function ($query) use ($locale) {
                return $query->where('lang_locale', $locale ?? App::getLocale());
            });
        }
        $datas = $this->applyWhereConditions($datas, $conditions);
        if (count($orders)) {
            foreach ($orders as $orderByKey => $orderByValue) {
                $datas = $datas->orderBy($orderByKey, $orderByValue);
            }
        }
        return $datas->selectRaw($select)->paginate($pageSize);
    }

    public function countRecordWithStatus(): Collection
    {
        return $this->getModel()
            ->whereHas('language_metas', function ($query) {
                return $query->where('lang_locale', config('app.fallback_locale'));
            })
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();
    }

    public function countRecordWithConditions(array $conditions, array $select = []): Collection
    {
        $data = $this->getModel();
        $data = $this->applyWhereConditions($data, $conditions);
        $data = $data->select(array_merge($select, [DB::raw('COUNT(*) as total')]));
        if($select) {
            $data = $data->groupBy($select);
        }
        return $data->get();
    }

    protected function applyWhereConditions($data, $conditions)
    {
        if (count($conditions)) {
            foreach ($conditions as $field => $where) {
                $condition = array_keys($where)[0];
                $value = array_values($where)[0];
                switch ($condition) {
                    case 'LIKE':
                        $data = $data->where($field, 'LIKE', "%" . str_replace(' ', '%', $value) . "%");
                        break;
                    case '=':
                        $data = $data->where($field, $value);
                        break;
                    case 'IN':
                        $data = $data->whereIn($field, $value);
                        break;
                    case 'NOTIN':
                        $data = $data->whereNotIn($field, $value);
                        break;
                    case 'DFF':
                        $data = $data->where($field, '<>', $value);
                        break;
                    case 'BETWEEN':
                        $data = $data->whereBetween($field, $value);
                        break;
                    case 'COLUMN':
                        $where = array_values($where);
                        $fieldCompare = array_shift($where);
                        $data = $data->whereColumn($field, $fieldCompare[0], $fieldCompare[1]);
                        break;
                    case 'CALLBACK':
                        $data = $data->where(function($query) use ($value) {
                            return $value($query);
                        });
                        break;
                    default:
                        $data = $data->where($field, $condition, $value);
                }
            }
        }
        return $data;
    }
}
