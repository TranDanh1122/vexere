<?php

namespace DreamTeam\Media\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface MediaRepositoryInterface extends BaseRepositoryInterface
{
    public function createName(string $name, int|string|null $folder): string;

    public function createSlug(string $name, string $extension, string|null $folderPath): string;

    public function getFilesByFolderId(int|string $folderId, array $params = [], bool $withFolders = true, array $folderParams = []);

    public function getTrashed(int|string $folderId, array $params = [], bool $withFolders = true, array $folderParams = []): Collection;

    public function emptyTrash(): bool;
}
