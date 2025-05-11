<?php

namespace DreamTeam\Media\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface MediaFolderInterface extends BaseRepositoryInterface
{
    public function getFolderByParentId(int|string|null $folderId, array $params = [], bool $withTrash = false);

    public function createSlug(string $name, int|string|null $parentId): string;

    public function createName(string $name, int|string|null $parentId): string;

    public function getBreadcrumbs(int|string|null $parentId, array $breadcrumbs = []);

    public function getTrashed(int|string|null $parentId, array $params = []);

    public function deleteFolder(int|string|null $folderId, bool $force = false);

    public function getAllChildFolders(int|string|null $parentId, array $child = []);

    public function getFullPath(int|string|null $folderId, string|null $path = ''): string|null;

    public function restoreFolder(int|string|null $folderId);

    public function emptyTrash(): bool;
}
