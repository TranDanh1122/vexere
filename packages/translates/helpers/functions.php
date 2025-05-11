<?php

use DreamTeam\Base\Facades\Html;

if (! function_exists('languageFlag(')) {
    function languageFlag(string|null $flag, string|null $name = null, int $width = 16): string
    {
        if (! $flag) {
            return '';
        }

        return Html::image(asset(BASE_LANGUAGE_FLAG_PATH . $flag . '.svg'), sprintf('%s flag', $name), [
            'title' => $name,
            'class' => 'flag',
            'style' => "height: {$width}px",
            'loading' => 'lazy',
        ]);
    }
}

if (! function_exists('getLanguageFlagSrc(')) {
    function getLanguageFlagSrc(string|null $flag): string
    {
        if (! $flag) {
            return '';
        }

        return asset(BASE_LANGUAGE_FLAG_PATH . $flag . '.svg');
    }
}

if (! function_exists('getLanguageSetting(')) {
    function getLanguageSetting(string $key, string|array|null $default = ''): string|array
    {
        $setting = getOption('siteLanguage', '', false);
        return $setting[$key] ?? $default ?? '';
    }
}
