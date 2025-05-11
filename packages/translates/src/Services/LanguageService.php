<?php

namespace DreamTeam\Translate\Services;

use DreamTeam\Translate\Repositories\Interfaces\LanguageRepositoryInterface;
use DreamTeam\Translate\Services\Interfaces\LanguageServiceInterface;
use DreamTeam\Base\Services\CrudService;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Translate\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LanguageService extends CrudService implements LanguageServiceInterface
{

    public function __construct(
        LanguageRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    public function getActiveLanguage(array $select = ['*']): Collection
    {
        return $this->repository->getActiveLanguage($select);
    }

    public function getDefaultLanguage(array $select = ['*']): BaseModel|Model|Language|null
    {
        return $this->repository->getDefaultLanguage($select);
    }
}
