<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\LanguageMeta;
use DreamTeam\Base\Repositories\Interfaces\LanguageMetaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LanguageMetaRepository extends BaseRepository implements LanguageMetaRepositoryInterface
{
    protected string|null|Model $model = LanguageMeta::class;

    public function getMapRecordsWithId(int $tableId, string $tableName): ?Collection
    {
        return \Cache::rememberForever('map_language_' . $tableName . '_' . $tableId, function() use ($tableId, $tableName) {
            $recordMeta = $this->getModel()
                ->where('lang_table', $tableName)
                ->where('lang_table_id', $tableId)
                ->first();
            if($recordMeta) {
                return $this->getModel()
                    ->where('lang_table', $tableName)
                    ->where('lang_code', $recordMeta->lang_code)
                    ->get();
            }
            return new Collection();
        });
    }
}
