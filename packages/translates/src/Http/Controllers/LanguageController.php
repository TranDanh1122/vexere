<?php

namespace DreamTeam\Translate\Http\Controllers;

use DreamTeam\Base\Supports\Language;
use DreamTeam\Translate\Facades\Language as LanguageFacade;
use DreamTeam\Translate\Http\Requests\LanguageRequest;
use DreamTeam\Translate\LanguageManager;
use DreamTeam\Translate\Models\Language as LanguageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use DreamTeam\Base\Enums\BaseStatusEnum;
use Throwable;
use DreamTeam\Base\Http\Controllers\AdminController;
use DreamTeam\Base\Http\Responses\BaseHttpResponse;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Base\Events\ClearCacheEvent;
use DreamTeam\Translate\Services\Interfaces\LanguageServiceInterface;

class LanguageController extends AdminController
{
    protected LanguageServiceInterface $languageService;
    protected SettingServiceInterface $settingService;

    public function __construct(
        LanguageServiceInterface $languageService,
        SettingServiceInterface $settingService
    ) {
        $this->languageService = $languageService;
        $this->settingService = $settingService;
        parent::__construct();
    }

    public function index()
    {
        \Asset::addDirectly([
            asset('vendor/core/core/translates/build/js/language.js')
        ], 'scripts', 'bottom');

        $module_name    = trans('Core::tables.title_setting', ['name' => trans("Translate::language.name")]);;
        $breadcrumbs = [['name' => $module_name]];
        $languages = Language::getListLanguages();
        $flags = Language::getListlanguageFlags();
        $activeLanguages = $this->languageService->getMultipleWithFromConditions([], [], 'order', 'asc');
        $setting_name   = 'siteLanguage';
        $settingLanguages = $this->settingService->getData($setting_name, false);
        return view('Translate::index', compact('languages', 'flags', 'activeLanguages', 'settingLanguages', 'module_name', 'breadcrumbs'));
    }

    public function store(LanguageRequest $request, BaseHttpResponse $response)
    {
        try {
            $language = $this->languageService->findOne(['code' => $request->input('lang_code')]);

            if ($language) {
                return $response
                    ->setError()
                    ->setMessage(trans('Translate::language.added_already'));
            }

            if (! LanguageModel::query()->exists()) {
                $request->merge(['is_default' => 1]);
            }

            File::ensureDirectoryExists(lang_path('vendor'));

            if (! File::isWritable(lang_path()) || ! File::isWritable(lang_path('vendor'))) {
                return $response
                    ->setError()
                    ->setMessage(
                        trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()])
                    );
            }

            $locale = $request->input('lang_locale');

            if (! File::isDirectory(lang_path($locale))) {
                $importedLocale = false;

                if (! $importedLocale) {
                    $defaultLocale = lang_path('en');
                    if (File::exists($defaultLocale)) {
                        File::copyDirectory($defaultLocale, lang_path($locale));
                    }

                    LanguageFacade::createLocaleInPath(lang_path('vendor'), $locale);
                }
            }
            event(new ClearCacheEvent());
            $language = $this->languageService->create([
                'name' => $request->input('lang_name'),
                'locale' => $request->input('lang_locale'),
                'code' => $request->input('lang_code'),
                'flag' => $request->input('lang_flag'),
                'order' => $request->input('lang_order'),
                'is_rtl' => $request->input('lang_is_rtl'),
                'is_default' => $request->is_default ?? 0,
                'status'  => BaseStatusEnum::ACTIVE
            ]);

            return $response
                ->setData(view('Translate::partials.language-item', ['item' => $language])->render())
                ->setMessage(trans('Translate::admin.create_success'));
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function update(Request $request, BaseHttpResponse $response)
    {
        try {
            $language = $this->languageService->findOne(['id' => $request->input('lang_id')]);
            if (empty($language)) {
                abort(404);
            }
            event(new ClearCacheEvent());
            $result = $this->languageService->update($language->id, [
                'name' => $request->input('lang_name'),
                'flag' => $request->input('lang_flag'),
                'order' => $request->input('lang_order'),
            ]);

            return $response
                ->setData(view('Translate::partials.language-item', ['item' => $result])->render())
                ->setMessage(trans('Translate::admin.update_success'));
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function changeStatus(Request $request, BaseHttpResponse $response)
    {
        $newLanguageId = $request->input('lang_id');

        $newLanguage = $this->languageService->findOne(['id' => $newLanguageId]);

        if (! $newLanguage) {
            abort(404);
        }
        event(new ClearCacheEvent());
        $language = $this->languageService->update($newLanguage->id, ['status' => $newLanguage->status ? BaseStatusEnum::DEACTIVE : BaseStatusEnum::ACTIVE]);
        return $response
            ->setData($language)
            ->setMessage(trans('Translate::admin.update_success'));
    }

    public function getLanguage(Request $request, BaseHttpResponse $response)
    {
        $language = $this->languageService->findOne(['id' => $request->input('lang_id')]);
        return $response
            ->setData($language);
    }

    public function getChangeDataLanguage($code, LanguageManager $language)
    {
        $previousUrl = strtok(URL::previous(), '?');

        $queryString = null;
        if ($code !== $language->getDefaultLocaleCode()) {
            $queryString = '?' . http_build_query([LanguageFacade::refLangKey() => $code]);
        }

        return redirect()->to($previousUrl . $queryString);
    }

    public function updateSetting(Request $request, BaseHttpResponse $response)
    {
        $settingName   = 'siteLanguage';
        $this->settingService->postData($request, $settingName, false);
        setLanguage($request->default_language ?? 'vi');
        event(new ClearCacheEvent());
        return $response
            ->setMessage(trans('Translate::admin.update_success'));
    }
}
