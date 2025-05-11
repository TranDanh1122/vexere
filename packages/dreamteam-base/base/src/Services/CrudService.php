<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class CrudService implements Interfaces\CrudServiceInterface
{
    protected BaseRepositoryInterface $repository;

    public function create($data = [], bool $saveMissingModelFillableAttributesToNull = true): Model
    {
        return $this->repository->createOrUpdateFromArray($data, $saveMissingModelFillableAttributesToNull);
    }

    public function createOrUpdateUniqueData(array $uniqueData, array $data): Model
    {
        return $this->repository->createOrUpdateFromArrayByConditions($uniqueData, $data);
    }

    public function firstOrCreate(array $uniqueData, array $data): Model
    {
        return $this->repository->firstOrCreate($uniqueData, $data);
    }

    public function insert(array $data = [])
    {
        return $this->repository->insertMultipleFromArray($data);
    }

    public function read($id): ?Model
    {
        return $this->repository->findOneByPrimary($id);
    }

    public function update($id, $data = []): Model
    {
        return $this->repository->updateByPrimary((int)$id, $data, false);
    }

    public function updateFromConditions(array $conditions, array $data): int
    {
        return $this->repository->updateFromWhereConditions($conditions, $data);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function deleteFromWhereCondition(array $conditions)
    {
        return $this->repository->deleteFromWhereCondition($conditions);
    }

    public function search($conditions = [], bool $hasLocale = false, string $select = '*'): Collection
    {
        return $this->repository->findMultipleFromArray($conditions, $hasLocale, $select);
    }

    public function findOne($conditions = [], $throwsExceptionIfNotFound = false, bool $hasLocale = false, string|null $locale = null): ?Model
    {
        return $this->repository->findOneFromArray($conditions, $throwsExceptionIfNotFound, $hasLocale, $locale);
    }
    public function getMultipleWithFromConditions($with, $conditions, $orderByColumn, $orderByValue, bool $hasLocale = false, string|null $locale = null, int $limit = -1): ?Collection
    {
        return $this->repository->getMultipleFromConditions($with, $conditions, $orderByColumn, $orderByValue, $hasLocale, $locale, $limit);
    }
    public function getWithMultiFromConditions($with, $conditions, $orderByColumn, $orderByValue, bool $hasLocale = false, string $select = '*', string|null $locale = null): ?Collection
    {
        return $this->repository->getWithMultiFromConditions($with, $conditions, $orderByColumn, $orderByValue, $hasLocale, $select, $locale);
    }

    public function findOneWith($with = [], $conditions = [], $throwsExceptionIfNotFound = false, bool $hasLocale = false, string|null $locale = null): ?Model
    {
        return $this->repository->findOneWithFromArray($with, $conditions, $throwsExceptionIfNotFound, $hasLocale, $locale);
    }

    public function findOneWhereFromConditions(array $with, array $conditions, string $orderByName = 'id', string $orderByValue = 'desc', bool $hasLocale = true, string $select = '*', bool $throwsExceptionIfNotFound = true): ?Model
    {
        return $this->repository->findOneWithFromConditions($with, $conditions, $orderByName, $orderByValue, $hasLocale, $select, $throwsExceptionIfNotFound);
    }

    public function getListData(Request $request, array $with, array $conditions, array $customConditions, array $orders, int $pageSize, bool $hasLocale = false): LengthAwarePaginator|Collection
    {
        return $this->repository->getDataTable($request, $with, $conditions, $customConditions, $orders, $pageSize, $hasLocale);
    }

    public function getListDataCategory(Request $request, array $with, array $conditions, array $customConditions, array $orders, bool $hasLocale = false): Collection
    {
        return $this->repository->getListDataCategoryTable($request, $with, $conditions, $customConditions, $orders, $hasLocale);
    }

    public function getIndexData(array $with, array $conditions, array $orders, string $select = '*', int $pageSize = 16, bool $hasLocale = false, null|string $locale = null): LengthAwarePaginator
    {
        return $this->repository->getIndexData($with, $conditions, $orders, $select, $pageSize, $hasLocale, $locale);
    }
    public function getTotalByStatus(): Collection
    {
        return $this->repository->countRecordWithStatus();
    }

    public function getTotalByConditions(array $conditions, array $select = []): Collection
    {
        return $this->repository->countRecordWithConditions($conditions, $select);
    }

    public function getThumbnailImages(): array
    {
        return $this->repository->findMultipleFromArray([], false, 'image')->pluck('image')->toArray();
    }
}
