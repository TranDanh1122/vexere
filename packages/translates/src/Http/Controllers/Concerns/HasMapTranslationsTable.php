<?php

namespace DreamTeam\Translate\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use DreamTeam\Translate\Facades\Language as FacadesLanguage;

trait HasMapTranslationsTable
{
    protected function mapTranslationsTable(Request $request): array
    {
        $locales = FacadesLanguage::getActiveLanguage();
        $defaultLanguage = FacadesLanguage::getDefaultLanguage(['locale', 'name', 'flag'])->toArray();

        if (! count($locales)) {
            $locales = [
                'en' => $defaultLanguage,
            ];
        } else {
            $newLocales = [];
            foreach($locales as $item) {
                $newLocales[$item->locale] = $item->toArray();
            }
            $locales = $newLocales;
        }

        $currentLocale = $request->has('lang_locale') ? $request->input('lang_locale') : app()->getLocale();

        $group = Arr::first($locales, fn ($item) => $item['locale'] == $currentLocale);

        if (! $group) {
            $group = $defaultLanguage;
        }

        $translateLocale = $group['locale'];

        return [
            $locales,
            $group,
            $defaultLanguage,
            $translateLocale,
        ];
    }
}
