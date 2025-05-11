<?php

namespace DreamTeam\Translate\Http\Controllers;

use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Base\Http\Controllers\AdminController;
use DreamTeam\Translate\Http\Controllers\Concerns\HasMapTranslationsTable;
use DreamTeam\Translate\Http\Requests\TranslationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use DreamTeam\Base\Http\Responses\BaseHttpResponse;
use DreamTeam\Translate\Facades\Language;
use Throwable;

class TranslationController extends AdminController
{
    use HasMapTranslationsTable;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        \Asset::addDirectly([
            asset('vendor/core/core/translates/build/js/translation.js'),
        ], 'scripts', 'bottom')
            ->addDirectly([
                asset('vendor/core/core/translates/build/css/translation.css')
            ], 'styles', 'bottom');

        [$locales, $locale, $defaultLanguage, $translateLocale] = $this->mapTranslationsTable($request);

        $exists = File::isDirectory(lang_path($locale['locale'])) && File::exists(lang_path('vendor'));

        [$translationsCollection, $groupWithNameSpace] = Language::getAllLang();
        $module_name    = trans('Core::tables.title_setting', ['name' => trans("Translate::translation.translations")]);;
        $breadcrumbs = [['name' => $module_name]];
        return view(
            'Translate::translations',
            compact('locales', 'locale', 'defaultLanguage', 'translateLocale', 'exists', 'translationsCollection', 'groupWithNameSpace', 'module_name', 'breadcrumbs')
        );
    }

    public function update(TranslationRequest $request, BaseHttpResponse $response)
    {
        $group = $request->input('group');

        $name = $request->input('name');
        $value = $request->input('value');

        [$locale, $key] = explode('|', $name, 2);

        Language::updateTranslation($locale, $group, $key, $value);
        [$translationsCollection, $groupWithNameSpace] = Language::getAllLang();
        $compact = view('Translate::translation-item', compact('translationsCollection'))->render();
        return $response->setData($compact)->setMessage(trans('Translate::admin.update_success'));;
    }

    public function import(Request $request, BaseHttpResponse $response)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();
        if (!empty($request->locale)) {
            Language::createLocaleInPath(lang_path('vendor'), $request->locale);
        } else {
            Artisan::call('cms:plugin:theme:clear-lang');
        }
        return $response
            ->setMessage(trans('Translate::translation.import_success_message'));
    }
}
