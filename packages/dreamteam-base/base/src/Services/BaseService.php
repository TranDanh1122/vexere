<?php

namespace DreamTeam\Base\Services;

use Illuminate\Support\Facades\Cookie;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Base\Services\Interfaces\SeoServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Events\ClearCacheEvent;

class BaseService implements Interfaces\BaseServiceInterface
{
    protected SlugServiceInterface $slugService;
    protected SeoServiceInterface $seoService;
    protected LanguageMetaServiceInterface $languageMetaService;
    protected SystemLogServiceInterface $systemLogService;

    function __construct(
        SlugServiceInterface $slugService,
        SeoServiceInterface $seoService,
        LanguageMetaServiceInterface $languageMetaService,
        SystemLogServiceInterface $systemLogService
    ) {
        $this->slugService = $slugService;
        $this->seoService = $seoService;
        $this->languageMetaService = $languageMetaService;
        $this->systemLogService = $systemLogService;
    }

    public function handleRelatedRecord($requests, string $table, int $tableId, bool $hasSeo = false, bool $hasLocale = false, bool $hasLog = true, string $logAction = null, array $logData = [], bool $hasSlug = false): void
    {
        if ($hasLocale) {
            $this->languageMetaService->createLangMeta($requests, $table, $tableId);
        }
        if ($hasSeo) {
            $this->seoService->createMetaSeo($requests, $table, $tableId);
        }
        if ($hasSlug) {
            $this->slugService->createOrUpdateSlug($table, $tableId, $requests->slug);
        }
        if ($hasLog) {
            $this->systemLogService->saveLog($logAction, $logData, $table, $tableId);
        }

        $expired = 60 * 24 * 30; // 30 days
        Cookie::queue('typeSave', 'success', $expired);
        if (defined('ACTION_AFTER_SAVE_MODULE_DATA')) {
            $requests->merge(['tableName' => $table, 'tableID' => $tableId]);
            do_action(ACTION_AFTER_SAVE_MODULE_DATA, $requests);
        }
        event(new ClearCacheEvent());
    }
}
