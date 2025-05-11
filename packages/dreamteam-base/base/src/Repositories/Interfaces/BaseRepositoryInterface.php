<?php

namespace DreamTeam\Base\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function setModel(string $modelClass): BaseRepositoryInterface;
    public function setRequest(Request $request): BaseRepositoryInterface;
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
    ): Collection;

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
    ): Collection;

    /**
     * Create or update a model instance from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Model
     */
    public function createOrUpdateFromArray(array $data, bool $saveMissingModelFillableAttributesToNull = true): Model;

    /**
     * Create or update a model instance from data array by conditions.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param array $conditions
     *
     * @return Model
     */
    public function createOrUpdateFromArrayByConditions(array $conditions, array $data = []): Model;

    /**
     * Create or get First a model instance from data array by conditions.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param array $conditions
     *
     * @return Model
     */
    public function firstOrCreate(array $uniqueData, array $data): Model;

    /**
     * Create a model instance from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @param bool  $saveMissingModelFillableAttributesToNull
     *
     * @return Model
     */
    public function createFromArray(array $data, bool $saveMissingModelFillableAttributesToNull = true): Model;

    /**
     * Insert Multiple a model instance from data array.
     * The use of this method suppose that your array is correctly formatted.
     *
     * @param array $data
     * @return bool
     */
    public function insertMultipleFromArray(array $data): bool;

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
    ): Model;

    /**
     * Update a model instance from where conditions.
     *
     * @param array   $conditions
     * @param array $data
     *
     * @return Model
     */
    public function updateFromWhereConditions(array $conditions, array $data): int;

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
    ): Model;

    /**
     * Delete a model instance from the request data.
     *
     * @param array $attributesToAddOrReplace (dot notation accepted)
     * @param array $attributesToExcept       (dot notation accepted)
     *
     * @return bool|null
     * @throws ModelNotFoundException
     */
    public function deleteFromRequest(array $attributesToAddOrReplace = [], array $attributesToExcept = []): ?bool;

    /**
     * Delete a model instance from a data array.
     *
     * @param array $data
     *
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteFromArray(array $data): bool;

    /**
     * @param array $condition
     * @return bool
     */
    public function deleteFromWhereCondition(array $condition): bool;

    /**
     * Delete a model instance from its primary key.
     *
     * @param int $primary
     *
     * @return bool|null
     * @throws ModelNotFoundException
     */
    public function deleteByPrimary(int $primary): ?bool;

    /**
     * Delete multiple model instances from their primary keys.
     *
     * @param array $instancePrimaries
     *
     * @return int
     * @throws ModelNotFoundException
     */
    public function deleteMultipleFromPrimaries(array $instancePrimaries): int;

    /**
     * Paginate array results.
     *
     * @param array $data
     * @param int   $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginateArrayResults(array $data, int $perPage = 20): LengthAwarePaginator;

    /**
     * Find one model instance from its primary key value.
     *
     * @param int  $primary
     * @param bool $throwsExceptionIfNotFound
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public function findOneByPrimary(int $primary, bool $throwsExceptionIfNotFound = true): ?Model;

    /**
     * Find one model instance from an associative array.
     *
     * @param array $data
     * @param bool $throwsExceptionIfNotFound
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public function findOneFromArray(array $data, bool $throwsExceptionIfNotFound = true, bool $hasLocale = false, string|null $locale = null): ?Model;

    /**
     * Find multiple model instances from a « where » parameters array.
     *
     * @param array $data
     *
     * @return Collection
     * @throws ModelNotFoundException
     */
    public function findMultipleFromArray(array $data, bool $hasLocale = false, string $select = '*'): Collection;

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
    public function getAll(array $columns = ['*'], string $orderBy = 'default', string $orderByDirection = 'asc'): Collection;

    /**
     * Instantiate a model instance with an attributes array.
     *
     * @param array $data
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    public function make(array $data): Model;

    /**
     * Get the model unique storage instance or create one.
     *
     * @return Model
     */
    public function modelUniqueInstance(): Model;

    /**
     * Add the missing model fillable attributes with a null value.
     *
     * @param array $data
     *
     * @return array
     */
    public function setMissingFillableAttributesToNull(array $data): array;

    /**
     * Find multiple model instances from an array of ids.
     *
     * @param array $primaries
     *
     * @return Collection
     */
    public function findMultipleFromPrimaries(array $primaries): Collection;

    /**
     * Find multiple model instances from an array of ids.
     *
     * @param array $conditions
     *
     * @return Collection
     */
    public function getMultipleFromConditions(array $with, array $conditions, string $orderByName = 'id', string $orderByValue = 'asc', bool $hasLocale = false, string|null $locale = null, int $limit = -1): Collection;

    public function findOneWithFromArray(array $with, array $data, bool $throwsExceptionIfNotFound = true, bool $hasLocale = false, string|null $locale = null): ?Model;

    public function getWithMultiFromConditions(array $with, array $conditions, string $orderByName, string $orderByValue, bool $hasLocale = false, string $select = '*', string|null $locale = null): Collection;

    public function findOneWithFromConditions(array $with, array $conditions, string $orderByName = 'id', string $orderByValue = 'desc', bool $hasLocale = true, string $select = '*', bool $throwsExceptionIfNotFound = true): ?Model;
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
    public function getDataTable(Request $request, array $with, array $conditions, array $customConditions, array $orders, int $pageSize, bool $hasLocale = false): LengthAwarePaginator|Collection;

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
    public function getListDataCategoryTable(Request $request, array $with, array $conditions, array $customConditions, array $orders, bool $hasLocale = false): Collection;

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
    public function getIndexData(array $with, array $conditions, array $orders, string $select = '*', int $pageSize = 16, bool $hasLocale = false, null|string $locale = null): LengthAwarePaginator;

    public function countRecordWithStatus(): Collection;
    public function countRecordWithConditions(array $conditions, array $select = []): Collection;
}
