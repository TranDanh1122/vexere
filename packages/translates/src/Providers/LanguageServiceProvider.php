<?php

namespace DreamTeam\Translate\Providers;

use Illuminate\Support\ServiceProvider;
use DreamTeam\Translate\Facades\Language;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Supports\Language as SupportsLanguage;

class LanguageServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        AliasLoader::getInstance()->alias('Language', Language::class);

        $this->setConfigLanguage();
    }

    public function setConfigLanguage()
    {
        $config = $this->app['config']->get('app', []);
        $config['language'] = $this->setLanguageToConfig();
        $defaultLocale = $this->getLanguageSetting('default_language', config('app.locale', 'vi'));
        if (!array_key_exists($defaultLocale, $config['language'])) {
            $defaultLocale = array_key_first($config['language']);
        }
        $config['locale'] = $defaultLocale;
        $config['fallback_locale'] = $config['locale'];
        $config['timezone'] = $this->getLanguageSetting('timezone', 'Asia/Ho_Chi_Minh');
        date_default_timezone_set($config['timezone']);
        $this->app['config']->set('app', $config);
    }

    protected function getLanguageSetting(string $key, string|array $default = '')
    {
        $setting = null;
        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->where('key', 'siteLanguage')->first();
        }
        if ($setting) {
            $setting = json_decode(base64_decode($setting->value), 1);
        }
        return $setting[$key] ?? $default ?? '';
    }

    public function setLanguageToConfig(): array
    {
        $languages = [];
        if ((bool) $this->getLanguageSetting('multiple_language', 0)) {
            if (Schema::hasTable('languages')) {
                $activeLanguages = DB::table('languages')
                    ->where('status', BaseStatusEnum::ACTIVE)
                    ->select('name', 'code', 'locale', 'flag', 'order')
                    ->orderBy('order', 'asc')
                    ->get();
                foreach ($activeLanguages as $language) {
                    $languages[$language->locale] = [
                        'name' => $language->name,
                        'locale' => $language->locale,
                        'flag' => '/vendor/core/core/translates/img/flags/' . $language->flag . '.svg',
                        'faker_locale' => $language->code,
                        'currency'     => SupportsLanguage::getCurencyLanguageCodes()[$language->code] ?? '',
                    ];
                }
            }
        }
        if (!$languages) {
            $default = $this->getLanguageSetting('default_language', config('app.locale', 'vi'));
            $languages[$default] = [
                'locale' => $default,
                'name' => $default,
                'flag' => '/vendor/core/core/translates/img/flags/' . $default . '.svg',
            ];
        }
        return $languages;
    }
}
