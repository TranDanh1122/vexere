<?php

namespace DreamTeam\Base\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CrudServiceInterface
{
    public function create($data = [], bool $saveMissingModelFillableAttributesToNull = true);
    public function createOrUpdateUniqueData(array $uniqueData, array $data): Model;
    public function firstOrCreate(array $uniqueData, array $data): Model;
    public function insert(array $data = []);
    public function read($id);
    public function update($id, $data = []);
    public function updateFromConditions(array $conditions, array $data): int;
    public function delete($id);
    public function deleteFromWhereCondition(array $conditions);
    public function search($conditions = [], bool $hasLocale = false, string $select = '*');
    public function findOne($conditions = [], $throwsExceptionIfNotFound = false, bool $hasLocale = false, string|null $locale = null);
    public function getMultipleWithFromConditions($with, $conditions, $orderByColumn, $orderByValue, bool $hasLocale = false, string|null $locale = null, int $limit = -1);
    public function getWithMultiFromConditions($with, $conditions, $orderByColumn, $orderByValue, bool $hasLocale = false, string $select = '*', string|null $locale = null);
    public function findOneWith($with, $conditions, $throwsExceptionIfNotFound = false, bool $hasLocale = false, string|null $locale = null);
    public function findOneWhereFromConditions(array $with, array $conditions, string $orderByName = 'id', string $orderByValue = 'desc', bool $hasLocale = true, string $select = '*', bool $throwsExceptionIfNotFound = true): ?Model;
    public function getListData(Request $request, array $with, array $conditions, array $customConditions, array $orders, int $pageSize, bool $hasLocale = false): LengthAwarePaginator|Collection;
    public function getListDataCategory(Request $request, array $with, array $conditions, array $customConditions, array $orders, bool $hasLocale = false): Collection;
    public function getIndexData(array $with, array $conditions, array $orders, string $select = '*', int $pageSize = 16, bool $hasLocale = false, null|string $locale = null): LengthAwarePaginator;
    public function getTotalByStatus(): Collection;
    public function getTotalByConditions(array $conditions, array $select = []): Collection;
    public function getThumbnailImages(): array;
}
