<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\LanguageMetaRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;

class LanguageMetaService extends CrudService implements LanguageMetaServiceInterface
{

    public function __construct(
        LanguageMetaRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function createLangMeta($requests, string $langTable, int $langTableId) :Model
    {
        $langLocale = $requests->lang_locale ?? \App::getLocale();
        $langReferer = $requests->lang_referer ?? null;
        // Kiểm tra đã tồn tại ngôn ngữ chưa (TH bản ghi hiện tại đã có đa ngồn ngữ)
        $checkExists = $this->repository->findOneFromArray([
            'lang_table'    => $langTable,
            'lang_table_id' => $langTableId,
        ], false);
        // TH bản ghi hiện tại chưa có đa ngồn ngữ thì mới thêm
        if (!$checkExists) {
            // Kiểm tra xem có bản ghi của bản gốc không
            $checkRefererExists = $this->repository->findOneFromArray([
                    'lang_table'    => $langTable,
                    'lang_table_id' => $langReferer,
                ], false);
            // Nếu không tồn tại referer (TH bản ghi gốc chưa có đa ngôn ngữ)
            if (!$checkRefererExists) {
                $langCode = getCodeLangMeta();
            } else {
                $langCode = $checkRefererExists->lang_code ?? getCodeLangMeta();
            }
            $langMeta = [
                'lang_table'        => $langTable,
                'lang_table_id'     => $langTableId,
                'lang_locale'       => $langLocale,
                'lang_code'         => $langCode,
            ];
            return $this->repository->createFromArray($langMeta);
        }
        return $checkExists;
    }

}
