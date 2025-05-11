<?php

namespace DreamTeam\Translate;

use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Translate\Models\Language;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Facades\Html;
use DreamTeam\PluginManagement\PluginManifest;
use DreamTeam\PluginManagement\ThemeManifest;
use Throwable;
use Symfony\Component\VarExporter\VarExporter;

class LanguageManager
{
    public const ENV_ROUTE_KEY = 'ROUTING_LOCALE';

    protected Translator $translator;

    protected Router $router;

    protected Application $app;

    protected string|null $baseUrl = null;

    protected string|null $defaultLocale = null;

    protected array $supportedLocales = [];

    protected string|false $currentLocale = false;

    /**
     * An array that contains all routes that should be translated
     */
    protected array $translatedRoutes = [];

    /**
     * Name of the translation key of the current route, it is used for url translations
     */
    protected string|null $routeName = null;

    protected string|null $currentAdminLocaleCode = null;

    protected string|null $currentLocaleCode = null;

    protected array|Collection $activeLanguages = [];

    protected array|Collection $activeLanguageSelect = ['*'];

    protected BaseModel|Model|Language|null $defaultLanguage = null;

    protected array|Collection $defaultLanguageSelect = ['*'];

    protected array $switcherURLs = [];

    protected HttpRequest $request;

    protected UrlGenerator $url;

    protected array $localesMapping = [];

    public function __construct(protected Filesystem $files)
    {
        $this->app = app();

        $this->translator = $this->app['translator'];
        $this->router = $this->app['router'];
        $this->request = $this->app['request'];
        $this->url = $this->app['url'];

        $refLang = $this->getRefLang();

        if ($refLang) {
            $this->currentAdminLocaleCode = $refLang;
        }
    }

    public function getSupportedLocales(): array
    {
        return cache()->remember('supported_locales', 3600, function () {
            if (! empty($this->supportedLocales)) {
                return $this->supportedLocales;
            }

            $languages = $this->getActiveLanguage();

            $locales = [];
            foreach ($languages as $language) {
                if (
                    is_in_admin()
                ) {
                    $locales[$language->locale] = [
                        'name' => $language->name,
                        'locale' => $language->locale,
                        'code' => $language->code,
                        'flag' => $language->flag,
                        'is_rtl' => $language->is_rtl,
                        'is_default' => $language->is_default,
                    ];
                }
            }

            if (empty($locales)) {
                $locales = [
                    'en' => [
                        'name' => 'English',
                        'locale' => 'en',
                        'code' => 'en_US',
                        'flag' => 'us',
                        'is_rtl' => false,
                        'is_default' => true,
                    ],
                    'vi' => [
                        'name' => 'Việt Nam',
                        'locale' => 'vi',
                        'code' => 'vi_VN',
                        'flag' => 'vi',
                        'is_rtl' => false,
                        'is_default' => false,
                    ],
                ];
            }

            $this->supportedLocales = $locales;

            return $locales;
        });
    }

    public function setSupportedLocales(array $locales): void
    {
        $this->supportedLocales = $locales;
    }

    public function getActiveLanguage(array $select = ['*']): array|Collection
    {
        $cacheKey = 'active_languages_' . implode('_', $select);
        return cache()->remember($cacheKey, 3600, function () use ($select) {
            if ($this->activeLanguages && $this->activeLanguageSelect === $select) {
                return $this->activeLanguages;
            }

            $this->activeLanguages = Language::query()
                ->where('status', BaseStatusEnum::ACTIVE)
                ->orderBy('order', 'asc')
                ->select($select)
                ->get();

            $this->activeLanguageSelect = $select;

            return $this->activeLanguages;
        });
    }

    public function getDefaultLocale(): string|null
    {
        if (! $this->defaultLocale) {
            $this->setDefaultLocale();
        }

        return $this->defaultLocale;
    }

    public function getAminpannelLocale(): string|null
    {
        return getLanguageSetting('show_pannel_language', $this->getDefaultLocale());
    }

    public function setDefaultLocale(): void
    {

        $this->defaultLocale = getLanguageSetting('default_language');
        if (empty($this->defaultLocale)) {
            $this->defaultLocale = config('app.locale', 'vi');
        }
    }

    public function getCurrentLagRecord()
    {
        return Language::where('locale', request()->lang_locale ?? $this->getDefaultLocale())->first()->toArray();
    }

    public function isHasMultipleLanguage(): bool
    {
        return (bool) getLanguageSetting('multiple_language', 0);
    }

    public function getTimezone(): string
    {
        return getLanguageSetting('timezone', 'Asia/Ho_Chi_Minh');
    }

    public function setLanguageToConfig(): array
    {
        $languages = [];
        if ($this->isHasMultipleLanguage()) {
            $activeLanguages = $this->getActiveLanguage();
            foreach ($activeLanguages as $language) {
                $languages[$language->locale] = [
                    'name' => $language->name,
                    'flag' => getLanguageFlagSrc($language->flag),
                    'faker_locale' => $language->code,
                    'currency'     => 'VND',
                ];
            }
        } else {
            $languages[$this->getDefaultLocale()] = $this->getDefaultLocale();
        }
        return $languages;
    }

    /**
     * Returns a URL adapted to $locale or current locale
     *
     * @param string|null $url URL to adapt. If not passed, the current url would be taken.
     * @param null $locale Locale to adapt, false to remove locale
     * @return string URL translated
     */
    public function localizeURL(string|null $url = null, $locale = null): string
    {
        return $this->getLocalizedURL($locale, $url, [], false);
    }

    /**
     * Returns a URL adapted to $locale
     *
     * @param string|bool $locale Locale to adapt, false to remove locale
     * @param string|false $url URL to adapt in the current language. If not passed, the current url would be taken.
     * @param array $attributes Attributes to add to the route,
     * if empty, the system would try to extract them from the url.
     *
     * @return string URL translated, False if url does not exist
     */
    public function getLocalizedURL($locale = null, $url = null, array $attributes = [], $forceDefaultLocation = true): string
    {
        if ($locale === null) {
            $locale = $this->getCurrentLocale();
        }

        if (! $this->checkLocaleInSupportedLocales($locale)) {
            $locale = $this->getCurrentLocale();
        }

        if (empty($attributes)) {
            $attributes = $this->extractAttributes($url, $locale);
        }

        $urlQuery = $url ? parse_url($url, PHP_URL_QUERY) : null;
        $urlQuery = $urlQuery ? '?' . $urlQuery : '';

        if (empty($url)) {
            $url = $this->request->fullUrl();
            $urlQuery = parse_url($url, PHP_URL_QUERY);
            $urlQuery = $urlQuery ? '?' . $urlQuery : '';

            if (! empty($this->routeName)) {
                return $this->getURLFromRouteNameTranslated(
                    $locale,
                    $this->routeName,
                    $attributes,
                    $forceDefaultLocation
                ) . $urlQuery;
            }
        } else {
            $url = $this->url->to($url);
        }

        $url = preg_replace('/' . preg_quote($urlQuery, '/') . '$/', '', $url);

        if ($locale && $translatedRoute = $this->findTranslatedRouteByUrl($url, $attributes, $this->currentLocale)) {
            return $this->getURLFromRouteNameTranslated(
                $locale,
                $translatedRoute,
                $attributes,
                $forceDefaultLocation
            ) . $urlQuery;
        }

        $basePath = $this->request->getBaseUrl();
        $parsedUrl = parse_url($url);
        $urlLocale = $this->getDefaultLocale();

        if (! $parsedUrl || empty($parsedUrl['path'])) {
            $path = $parsedUrl['path'] = '';
        } else {
            $parsedUrl['path'] = str_replace($basePath, '', '/' . ltrim($parsedUrl['path'], '/'));
            $path = $parsedUrl['path'];
            foreach ($this->getSupportedLocales() as $localeCode => $lang) {
                $localeCode = $this->getLocaleFromMapping($localeCode);

                $parsedUrl['path'] = preg_replace('%^/?' . $localeCode . '/%', '$1', $parsedUrl['path']);
                if ($parsedUrl['path'] !== $path) {
                    $urlLocale = $localeCode;

                    break;
                }

                $parsedUrl['path'] = preg_replace('%^/?' . $localeCode . '$%', '$1', $parsedUrl['path']);
                if ($parsedUrl['path'] !== $path) {
                    $urlLocale = $localeCode;

                    break;
                }
            }
        }

        $parsedUrl['path'] = ltrim($parsedUrl['path'], '/');

        if ($translatedRoute = $this->findTranslatedRouteByPath($parsedUrl['path'], $urlLocale)) {
            return $this->getURLFromRouteNameTranslated(
                $locale,
                $translatedRoute,
                $attributes,
                $forceDefaultLocation
            ) . $urlQuery;
        }

        $locale = $this->getLocaleFromMapping($locale);

        if (! empty($locale)) {
            if ($forceDefaultLocation || $locale != $this->getDefaultLocale()) {
                $parsedUrl['path'] = $locale . '/' . ltrim($parsedUrl['path'], '/');
            }
        }

        $parsedUrl['path'] = ltrim(ltrim($basePath, '/') . '/' . $parsedUrl['path'], '/');

        // Make sure that the pass path is returned with a leading slash only if it comes in with one.
        if (Str::startsWith($path, '/') === true) {
            $parsedUrl['path'] = '/' . $parsedUrl['path'];
        }

        $parsedUrl['path'] = rtrim($parsedUrl['path'], '/');

        $url = $this->unparseUrl($parsedUrl);

        if ($this->checkUrl($url)) {
            return $url . $urlQuery;
        }

        return $this->createUrlFromUri($url) . $urlQuery;
    }

    public function getCurrentLocale(): bool|string|null
    {
        if ($this->currentLocale) {
            return $this->currentLocale;
        }
        // or get application default language
        return $this->getDefaultLocale();
    }

    public function checkLocaleInSupportedLocales(string|bool|null $locale): bool
    {
        $locales = $this->getSupportedLocales();

        if ($locale !== false && empty($locales[$locale])) {
            return false;
        }

        return true;
    }

    /**
     * Extract attributes for current url
     *
     * @param bool|null|string $url to extract attributes,
     * if not present, the system will look for attributes in the current call
     *
     * @param string|null $locale
     * @return array Array with attributes
     */
    protected function extractAttributes(bool|null|string $url = false, string|null $locale = ''): array
    {
        if (! empty($url)) {
            $attributes = [];
            $parse = parse_url($url);

            if (isset($parse['path'])) {
                $parse = explode('/', $parse['path']);
            } else {
                $parse = [];
            }

            $url = [];
            foreach ($parse as $segment) {
                if (! empty($segment)) {
                    $url[] = $segment;
                }
            }

            foreach ($this->router->getRoutes()->getRoutes() as $route) {
                $path = $route->uri();
                if (! preg_match('/{[\w]+}/', $path)) {
                    continue;
                }

                $path = explode('/', $path);
                $index = 0;

                $match = true;
                foreach ($path as $key => $segment) {
                    if (isset($url[$index])) {
                        if ($segment === $url[$index]) {
                            $index++;

                            continue;
                        }
                        if (preg_match('/{[\w]+}/', $segment)) {
                            // must-have parameters
                            $attribute_name = preg_replace(['/}/', '/{/', '/\?/'], '', $segment);
                            $attributes[$attribute_name] = $url[$index];
                            $index++;

                            continue;
                        }
                        if (preg_match('/{[\w]+\?}/', $segment)) {
                            // optional parameters
                            if (! isset($path[$key + 1]) || $path[$key + 1] !== $url[$index]) {
                                // optional parameter taken
                                $attribute_name = preg_replace(['/}/', '/{/', '/\?/'], '', $segment);
                                $attributes[$attribute_name] = $url[$index];
                                $index++;

                                continue;
                            }
                        }
                    } elseif (! preg_match('/{[\w]+\?}/', $segment)) {
                        // no optional parameters but no more $url given
                        // this route does not match the url
                        $match = false;

                        break;
                    }
                }

                if (isset($url[$index + 1])) {
                    $match = false;
                }

                if ($match) {
                    return $attributes;
                }
            }
        } else {
            if (! $this->router->current()) {
                return [];
            }

            $attributes = $this->normalizeAttributes($this->router->current()->parameters());
            $response = event('routes.translation', [$locale, $attributes]);

            if (! empty($response)) {
                $response = array_shift($response);
            }

            if (is_array($response)) {
                $attributes = array_merge($attributes, $response);
            }
        }

        return $attributes;
    }

    /**
     * Normalize attributes gotten from request parameters.
     *
     * @param array $attributes The attributes
     * @return array  The normalized attributes
     */
    protected function normalizeAttributes(array $attributes): array
    {
        if (array_key_exists('data', $attributes) && is_array($attributes['data']) && ! count($attributes['data'])) {
            $attributes['data'] = null;
        }

        return $attributes;
    }

    /**
     * Returns a URL adapted to the route name and the locale given
     */
    public function getURLFromRouteNameTranslated(
        string|false|null $locale,
        string $transKeyName,
        array $attributes = [],
        bool $forceDefaultLocation = false
    ): bool|string {
        if (! $this->checkLocaleInSupportedLocales($locale)) {
            return false;
        }

        if (! is_string($locale)) {
            $locale = $this->getDefaultLocale();
        }

        $route = '';

        if ($forceDefaultLocation || ! ($locale === $this->getDefaultLocale())) {
            $route = '/' . $locale;
        }
        if (is_string($locale) && $this->translator->has($transKeyName, $locale)) {
            $translation = $this->translator->get($transKeyName, [], $locale);
            $route .= '/' . $translation;

            $route = $this->substituteAttributesInRoute($attributes, $route, $locale);
        }

        if (empty($route)) {
            // This locale does not have any key for this route name
            return false;
        }

        return rtrim($this->createUrlFromUri($route), '/');
    }

    /**
     * Change route attributes for the ones in the $attributes array
     *
     * @param $attributes array Array of attributes
     * @param string|null $route string route to substitute
     * @param string|null $locale
     * @return string route with attributes changed
     */
    protected function substituteAttributesInRoute(array $attributes, string|null $route, string $locale = null): string
    {
        foreach ($attributes as $key => $value) {
            if ($value instanceof Interfaces\LocalizedUrlRoutable) {
                $value = $value->getLocalizedRouteKey($locale);
            } elseif ($value instanceof UrlRoutable) {
                $value = $value->getRouteKey();
            }

            $route = str_replace(['{' . $key . '}', '{' . $key . '?}'], $value, $route);
        }

        // delete empty optional arguments that are not in the $attributes array
        return preg_replace('/\/{[^)]+\?}/', '', $route);
    }

    /**
     * Create an url from the uri
     * @param string|null $uri Uri
     *
     * @return  string Url for the given uri
     */
    public function createUrlFromUri(string|null $uri): string
    {
        $uri = ltrim($uri, '/');

        if (empty($this->baseUrl)) {
            return app('url')->to($uri);
        }

        return $this->baseUrl . $uri;
    }

    /**
     * Returns the translated route for an url and the attributes given and a locale
     *
     * @param string|false|null $url Url to check if it is a translated route
     * @param array $attributes Attributes to check if the url exists in the translated routes array
     * @param string $locale Language to check if the url exists
     *
     * @return string|false Key for translation, false if not exist
     */
    protected function findTranslatedRouteByUrl(string|false|null $url, array $attributes, string $locale): bool|string
    {
        if (empty($url)) {
            return false;
        }

        // Check if this url is a translated url
        foreach ($this->translatedRoutes as $translatedRoute) {
            $routeName = $this->getURLFromRouteNameTranslated($locale, $translatedRoute, $attributes);

            if ($this->getNonLocalizedURL($routeName) == $this->getNonLocalizedURL($url)) {
                return $translatedRoute;
            }
        }

        return false;
    }

    /**
     * It returns a URL without locale (if it has it)
     * Convenience function wrapping getLocalizedURL(false)
     *
     * @param string|false $url URL to clean, if false, current url would be taken
     *
     * @return string URL with no locale in path
     */
    public function getNonLocalizedURL(string|false|null $url = null): string
    {
        return $this->getLocalizedURL(false, $url, [], false);
    }

    public function getLocaleFromMapping(string|null $locale): string|null
    {
        return $this->getLocalesMapping()[$locale] ?? $locale;
    }

    /**
     * Return locales mapping.
     */
    public function getLocalesMapping(): array
    {
        if (empty($this->localesMapping)) {
            $this->localesMapping = [];
        }

        return $this->localesMapping;
    }

    /**
     * Returns the translated route for the path and the url given
     *
     * @param string $path Path to check if it is a translated route
     * @param string $urlLocale Language to check if the path exists
     *
     * @return string|false Key for translation, false if not exist
     */
    protected function findTranslatedRouteByPath(string $path, string $urlLocale): bool|string
    {
        // Check if this url is a translated url
        foreach ($this->translatedRoutes as $translatedRoute) {
            if ($this->translator->get($translatedRoute, [], $urlLocale) == rawurldecode($path)) {
                return $translatedRoute;
            }
        }

        return false;
    }

    /**
     * Build URL using array data from parse_url
     *
     * @param array|false $parsedUrl Array of data from parse_url function
     *
     * @return string Returns URL as string.
     */
    protected function unparseUrl($parsedUrl): string
    {
        if (empty($parsedUrl)) {
            return '';
        }

        $url = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $url .= $parsedUrl['host'] ?? '';
        $url .= isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = $parsedUrl['user'] ?? '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $url .= $user . (($user || $pass) ? $pass . '@' : '');

        if (! empty($url)) {
            $url .= isset($parsedUrl['path']) ? '/' . ltrim($parsedUrl['path'], '/') : '';
        } else {
            $url .= $parsedUrl['path'] ?? '';
        }

        $url .= isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $url .= isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return $url;
    }

    protected function checkUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function getInversedLocaleFromMapping(string|null $locale): string|null
    {
        return array_flip($this->getLocalesMapping())[$locale] ?? $locale;
    }

    public function getCurrentLocaleName(): string|null
    {
        $supportedLocales = $this->getSupportedLocales();

        if (empty($supportedLocales)) {
            return null;
        }

        return Arr::get($supportedLocales, $this->getCurrentLocale() . '.name');
    }

    public function getCurrentLocaleRTL()
    {
        $supportedLocales = $this->getSupportedLocales();

        if (empty($supportedLocales)) {
            return false;
        }

        return Arr::get($supportedLocales, $this->getCurrentLocale() . '.is_rtl');
    }

    public function getCurrentLocaleCode(): string|null
    {
        if ($this->currentLocaleCode) {
            return $this->currentLocaleCode;
        }

        $supportedLocales = $this->getSupportedLocales();

        if (empty($supportedLocales)) {
            return null;
        }

        return Arr::get($supportedLocales, $this->getCurrentLocale() . '.code');
    }

    public function getLocaleByLocaleCode(string $localeCode): string|null
    {
        $language = collect($this->getSupportedLocales())->where('code', $localeCode)->first();

        if ($language) {
            return $language['locale'];
        }

        return null;
    }

    public function setCurrentLocale(string|null $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function setCurrentLocaleCode(string|null $code): void
    {
        $this->currentLocaleCode = $code;
    }

    public function getDefaultLocaleCode(): string|null
    {
        $supportedLocales = $this->getSupportedLocales();

        if (empty($supportedLocales)) {
            return null;
        }

        return Arr::get($supportedLocales, $this->getDefaultLocale() . '.code');
    }

    public function getCurrentLocaleFlag(): string|null
    {
        $supportedLocales = $this->getSupportedLocales();

        if (empty($supportedLocales)) {
            return null;
        }

        return Arr::get($supportedLocales, $this->getCurrentLocale() . '.flag');
    }

    public function getSupportedLanguagesKeys(): array
    {
        return array_keys($this->getSupportedLocales());
    }

    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    /**
     * Translate routes and save them to the translated routes array (used in the localized route filter)
     */
    public function transRoute(string $routeName): string
    {
        if (! in_array($routeName, $this->translatedRoutes)) {
            $this->translatedRoutes[] = $routeName;
        }
        return $this->translator->get($routeName);
    }

    /**
     * Returns the translation key for a given path
     */
    public function getRouteNameFromAPath(string $path): bool|string
    {
        $attributes = $this->extractAttributes($path);

        $path = str_replace(BaseHelper::getHomepageUrl(), '', $path);

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $path = str_replace('/' . $this->currentLocale . '/', '', $path);
        $path = trim($path, '/');

        foreach ($this->translatedRoutes as $route) {
            if ($this->substituteAttributesInRoute($attributes, $this->translator->get($route)) === $path) {
                return $route;
            }
        }

        return false;
    }

    public function setBaseUrl(string $url): void
    {
        if (! str_ends_with($url, '/')) {
            $url .= '/';
        }

        $this->baseUrl = $url;
    }

    public function getDefaultLanguage(array $select = ['*']): Language|BaseModel|Model|null
    {
        if ($this->defaultLanguage && $this->defaultLanguageSelect === $select) {
            return $this->defaultLanguage;
        }

        $this->defaultLanguage = Language::query()
            ->where('locale', config('app.locale'))
            ->select($select)
            ->first();
        $this->defaultLanguageSelect = $select;

        return $this->defaultLanguage;
    }

    /**
     * Set and return current locale
     *
     * @param string|null $locale Locale to set the App to (optional)
     * @return string|null Returns locale (if route has any) or null (if route does not have a locale)
     */
    public function setLocale(string|null $locale = null): string|null
    {
        $supportedLocales = $this->getSupportedLocales();

        if (empty($locale) || ! is_string($locale)) {
            // If the locale has not been passed through the function
            // it tries to get it from the first segment of the url
            $locale = $this->request->segment(1);

            $localeFromRequest = $this->request->input('language');

            if ($localeFromRequest && is_string($localeFromRequest) && array_key_exists($localeFromRequest, $supportedLocales)) {
                $locale = $localeFromRequest;
            }

            if (! $locale) {
                $locale = $this->getForcedLocale();
            }
        }

        if (array_key_exists($locale, $supportedLocales)) {
            $this->currentLocale = $locale;
        } else {
            // if the first segment/locale passed is not valid
            // the system would ask which locale have to take
            // it could be taken by the browser
            // depending on your configuration

            $locale = null;

            $this->currentLocale = $this->getCurrentLocale();
        }

        return $locale;
    }

    /**
     * Returns the forced environment set route locale.
     */
    public function getForcedLocale(): string|null
    {
        return Env::get(static::ENV_ROUTE_KEY, function () {
            if (! function_exists('getenv')) {
                return null;
            }

            $value = getenv(static::ENV_ROUTE_KEY);

            if ($value !== false) {
                return $value;
            }

            return null;
        });
    }

    /**
     * Returns the translation key for a given path
     *
     * @return bool Returns value of useAcceptLanguageHeader in config.
     */
    public function useAcceptLanguageHeader(): bool
    {
        return true; //(bool)setting('language_auto_detect_user_language', false);
    }

    public function setSwitcherURLs(array $urls): self
    {
        $this->switcherURLs = $urls;

        return $this;
    }

    public function getSwitcherUrl(string $localeCode, string $languageCode): string|null
    {
        if (! empty($this->switcherURLs)) {
            $url = collect($this->switcherURLs)->where('code', $languageCode)->first();

            if ($url) {
                return rtrim($url['url'], '/') == rtrim(url(''), '/') ? url($localeCode) : $url['url'];
            }
        }

        $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);

        return $showRelated ? $this->getLocalizedURL($localeCode) : url($localeCode);
    }

    /**
     * Returns serialized translated routes for caching purposes.
     */
    public function getSerializedTranslatedRoutes(): string
    {
        return base64_encode(serialize($this->translatedRoutes));
    }

    /**
     * Sets the translated routes list.
     * Only useful from a cached routes context.
     */
    public function setSerializedTranslatedRoutes(string|null $serializedRoutes): void
    {
        if (! $serializedRoutes) {
            return;
        }

        $this->translatedRoutes = unserialize(base64_decode($serializedRoutes));
    }

    public function refLangKey(): string
    {
        return 'lang_locale';
    }

    public function getRefLang(): string|null
    {
        return BaseHelper::stringify(request()->input($this->refLangKey()));
    }

    public function getAllLang()
    {
        $langLoader = Lang::getLoader();
        $groupWithNameSpace = [];
        [$pluginNamespace, $themeNamespace] = $this->getAllNameSpace();
        foreach ($this->getGroups() as $group) {
            if (! str_contains($group, DIRECTORY_SEPARATOR)) {
                $trans = $langLoader->load('en', $group);
                $groupWithNameSpace[$group] = Str::ucfirst($group) . ' (Core)';
            } else {
                $nameSpace = Str::beforeLast($group, DIRECTORY_SEPARATOR);
                $trans = $langLoader->load('en', Str::afterLast($group, DIRECTORY_SEPARATOR), $nameSpace);
                $name = Str::replace(DIRECTORY_SEPARATOR, ' ', $group);
                if(in_array($name, $pluginNamespace)) {
                    $groupDisplay = $name . ' (Plugin)';
                } else if(in_array($name, $themeNamespace)) {
                    $groupDisplay = $name . ' (Theme)';
                } else {
                    $groupDisplay = $name . ' (Core)';
                }
                $groupWithNameSpace[$group] = $groupDisplay;
            }

            if ($trans && is_array($trans)) {
                foreach (Arr::dot($trans) as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }

                    $translations[$group][$key] = $value;
                }
            }
        }

        $translationsCollection = collect();

        foreach ($translations as $group => $items) {
            foreach (Arr::dot($items) as $key => $value) {
                $translationsCollection->push([
                    'group' => $group,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
        if (request()->get('group', null)) {
            $translationsCollection = $translationsCollection->filter(function ($item) {
                return $item['group'] === request()->query('group');
            });
        }

        if ($keyword = request()->input('keyword')) {
            $translationsCollection = $translationsCollection->filter(function ($item) use ($keyword) {
                return str_contains(Str::lower($item['value']), Str::lower($keyword));
            });
        }

        $page = request()->page ?: 1;

        $translationsCollection = new LengthAwarePaginator($translationsCollection->forPage($page, 100),
                           $translationsCollection->count(),
                           100, $page);
        $translationsCollection->appends(request()->all());
        $translationsCollection->setPath(route('admin.translations.index'));
        return [$translationsCollection, $groupWithNameSpace];
    }
    public function getAllNameSpace()
    {
        return Cache::rememberForever('all_site_namspace', function() {
            $manifest = (new PluginManifest())->getManifest();
            $themeManifest = (new ThemeManifest())->getManifest();
            $pluginNamespace = $themeNamespace = [];
            foreach ($manifest['namespaces'] as $key => $namespace) {
                $arrayName = explode('\\', rtrim($namespace, '\\'));
                $nameSpace = array_pop($arrayName);
                $pluginNamespace[$key] = $nameSpace;
            }
            foreach ($themeManifest['namespaces'] as $key => $namespace) {
                $arrayName = explode('\\', rtrim($namespace, '\\'));
                $nameSpace = array_pop($arrayName);
                $themeNamespace[$key] = $nameSpace;
            }
            return [$pluginNamespace, $themeNamespace];
        });
    }
    public function formatGroupLang(string $group)
    {
        $group = Str::replace('\\', '/', $group);
        $groupDisplay = $group;
        [$pluginNamespace, $themeNamespace] = $this->getAllNameSpace();
        $name = Str::beforeLast($group, '/');
        if(in_array($name, $pluginNamespace)) {
            $groupDisplay = $name . ' (Plugin)';
        } else if(in_array($name, $themeNamespace)) {
            $groupDisplay = $name . ' (Theme)';
        } else {
            $groupDisplay = Str::ucfirst($name) . ' (Core)';
        }

        return Html::tag(
            'code',
            $groupDisplay,
            [
                'data-bs-toggle' => 'tooltip',
                'data-bs-original-title' => $group,
            ]
        );
    }

    public function formatKeyLang(array $item)
    {
        $item = json_decode(json_encode($item));
        $item->group = Str::replace('\\', '/', $item->group);
        $trans = trans(Str::of($item->group)->replaceLast('/', '::')->append(".$item->key")->toString(), [], 'en');

        return Html::decode($this->formatKeyAndValue(is_array($trans) ? $item->key : $trans));
    }

    public function formatValueLang(array $item)
    {
        $item = json_decode(json_encode($item));
        $item->group = Str::replace('\\', '/', $item->group);
        $trans = trans(Str::of($item->group)->replaceLast('/', '::')->append(".$item->key")->toString(), [], request()->lang_locale ?? getLocale());
        $value = $this->formatKeyAndValue(is_array($trans) ? $item->value : $trans);

        return Html::link('#edit', $value, [
            'class' => sprintf('editable locale-%s', request()->lang_locale ?? getLocale()),
            'data-locale' => request()->lang_locale ?? getLocale(),
            'data-name' => sprintf('%s|%s', request()->lang_locale ?? getLocale(), $item->key),
            'data-type' => 'textarea',
            'data-title' => trans('Translate::translation.edit_title'),
            'data-url' => route('admin.translations.group.edit', ['group' => $item->group, 'page' => request()->page ?: 1]),
            'data-bs-toggle' => 'tooltip',
            'data-bs-original-title' => trans('Translate::translation.edit_title'),
        ]);
    }

    public function updateTranslation(string $locale, string $group, string $key, string|null $value): void
    {
        $loader = Lang::getLoader();

        if (str_contains($group, '/')) {
            $englishTranslations = $loader->load('en', Str::afterLast($group, '/'), Str::beforeLast($group, '/'));
            $translations = $loader->load($locale, Str::afterLast($group, '/'), Str::beforeLast($group, '/'));
        } else {
            $englishTranslations = $loader->load('en', $group);
            $translations = $loader->load($locale, $group);
        }

        Arr::set($translations, $key, $value);

        $translations = array_merge($englishTranslations, $translations);

        $file = $locale . '/' . $group;

        if (! File::isDirectory(lang_path($locale))) {
            File::makeDirectory(lang_path($locale), 755, true);
        }

        $groups = explode('/', $group);
        if (count($groups) > 1) {
            $folderName = Arr::last($groups);
            Arr::forget($groups, count($groups) - 1);

            $dir = 'vendor/' . implode('/', $groups) . '/' . $locale;
            if (! File::isDirectory(lang_path($dir))) {
                File::makeDirectory(lang_path($dir), 755, true);
            }

            $file = $dir . '/' . $folderName;
        }

        $path = lang_path($file . '.php');
        $output = "<?php\n\nreturn " . VarExporter::export($translations) . ";\n";

        File::put($path, $output);
    }

    public function createLocaleInPath(string $path, string $locale): int
    {
        $folders = File::directories($path);

        foreach ($folders as $module) {
            foreach (File::directories($module) as $item) {
                if (File::name($item) == 'en') {
                    File::copyDirectory($item, $module . '/' . $locale);
                }
            }
        }
        if (! File::isDirectory(lang_path($locale))) {
            File::copyDirectory(lang_path('en'), lang_path($locale));
        }
        return count($folders);
    }

    public function isShowDropdown(): bool
    {
        return true;
    }

    protected function formatKeyAndValue(string|null $value): string|null
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    protected function getGroups(): array
    {
        $groups = [];

        $langPaths = File::glob(lang_path(BaseHelper::joinPaths(['vendor', '*', 'en'])));
        $langPaths[] = lang_path('en');
        foreach ($langPaths as $langPath) {
            if (! File::isWritable($langPath)) {
                continue;
            }

            try {
                foreach (File::allFiles($langPath) as $file) {
                    $group = str_replace(lang_path(), '', dirname($file));

                    if ($group) {
                        $group = str_replace('vendor' . DIRECTORY_SEPARATOR, '', $group);
                    }

                    $group = str_replace(DIRECTORY_SEPARATOR . 'en', '', $group);

                    if (! $group) {
                        $group = null;
                    } else {
                        $group = ltrim($group, DIRECTORY_SEPARATOR);
                    }

                    $fileName = File::name($file);

                    if ($group) {
                        $group .= DIRECTORY_SEPARATOR . $fileName;
                    } else {
                        $group = $fileName;
                    }

                    $groups[$group] = $group;
                }
            } catch (Throwable $exception) {
                BaseHelper::logError($exception);

                continue;
            }
        }
        return $groups;
    }
}
