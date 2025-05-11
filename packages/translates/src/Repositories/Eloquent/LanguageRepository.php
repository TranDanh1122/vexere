<?php

namespace DreamTeam\Translate\Repositories\Eloquent;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Translate\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Translate\Repositories\Interfaces\LanguageRepositoryInterface;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{

    protected string|null|Model $model = Language::class;

    public function getActiveLanguage(array $select = ['*']): Collection
    {
        $data = $this->getModel()->orderBy('order')->select($select)->get();

        return $data;
    }

    public function getDefaultLanguage(array $select = ['*']): BaseModel|Model|Language|null
    {
        $data = $this->getModel()->where('is_default', 1)->select($select)->first();

        return $data;
    }
}
