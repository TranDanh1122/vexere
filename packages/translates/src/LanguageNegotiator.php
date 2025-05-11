<?php

namespace DreamTeam\Translate;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Locale;

class LanguageNegotiator
{
    private Collection $supportedLanguages;
    private bool $useIntl = false;
    private array $cachedMatches = [];
    private bool $shouldCache;

    public function __construct(
        private readonly string $defaultLocale,
        array $supportedLanguages,
        private readonly ?Request $request = null,
        ?bool $useCache = null
    ) {
        $this->request = $request ?? request();
        $this->shouldCache = $useCache ?? config('app.debug') === false;
        $this->initializeSupportedLanguages($supportedLanguages);
    }

    private function initializeSupportedLanguages(array $supportedLanguages): void
    {
        $this->useIntl = extension_loaded('intl') && class_exists('Locale');

        $this->supportedLanguages = collect($supportedLanguages)->map(function ($lang, $key) {
            return $this->useIntl ? $this->normalizeLanguage($lang, $key) : $lang;
        });
    }

    private function normalizeLanguage(array $lang, string $key): array
    {
        $lang['lang'] = $lang['lang'] ?? Locale::canonicalize($key);

        if (isset($lang['regional'])) {
            $lang['regional'] = Locale::canonicalize($lang['regional']);
        }

        return $lang;
    }

    public function negotiateLanguage(): string
    {
        if (!$this->shouldCache) {
            return $this->determineLanguage();
        }

        $cacheKey = $this->generateCacheKey();

        return Cache::remember($cacheKey, now()->addHour(), function () {
            return $this->determineLanguage();
        });
    }

    private function determineLanguage(): string
    {
        return $this->findExactMatch()
            ?? $this->findRegionalMatch()
            ?? $this->tryIntlNegotiation()
            ?? $this->tryRemoteHostMatch()
            ?? $this->defaultLocale;
    }

    private function findExactMatch(): ?string
    {
        $matches = $this->getMatchesFromAcceptedLanguages();

        foreach ($matches as $key => $match) {
            if ($this->supportedLanguages->has($key)) {
                return $key;
            }
        }

        return null;
    }

    private function findRegionalMatch(): ?string
    {
        if (!$this->useIntl) {
            return null;
        }

        $matches = $this->getMatchesFromAcceptedLanguages();

        foreach ($matches as $key => $_) {
            $canonicalKey = Locale::canonicalize($key);

            $match = $this->supportedLanguages->first(
                fn($locale, $keySupported) => (isset($locale['regional']) && $locale['regional'] === $canonicalKey) ||
                    (isset($locale['lang']) && $locale['lang'] === $canonicalKey)
            );

            if ($match) {
                return $key;
            }
        }

        return null;
    }

    private function tryIntlNegotiation(): ?string
    {
        if (!$this->useIntl || empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        $httpAcceptLanguage = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        return $this->supportedLanguages->has($httpAcceptLanguage) ? $httpAcceptLanguage : null;
    }

    private function tryRemoteHostMatch(): ?string
    {
        $remoteHost = $this->request->server('REMOTE_HOST');
        if (!$remoteHost) {
            return null;
        }

        $lang = strtolower(end(explode('.', $remoteHost)));

        return $this->supportedLanguages->has($lang) ? $lang : null;
    }

    protected function getMatchesFromAcceptedLanguages(): array
    {
        if (!empty($this->cachedMatches)) {
            return $this->cachedMatches;
        }

        $acceptLanguages = $this->request->header('Accept-Language');
        if (!$acceptLanguages) {
            return [];
        }

        $matches = [];
        $genericMatches = [];

        foreach (explode(',', $acceptLanguages) as $option) {
            [$language, $weight] = $this->parseLanguageOption($option);
            $matches[$language] = $weight;

            // Add generic language options with slightly lower weights
            $this->addGenericMatches($language, $weight, $genericMatches);
        }

        $this->cachedMatches = $this->finalizeMatches($matches, $genericMatches);

        return $this->cachedMatches;
    }

    private function parseLanguageOption(string $option): array
    {
        $parts = array_map('trim', explode(';', $option));
        $language = $parts[0];

        if (isset($parts[1])) {
            $weight = (float) str_replace('q=', '', $parts[1]);
        } else {
            $weight = $this->calculateDefaultWeight($language);
        }

        return [$language, $weight];
    }

    private function calculateDefaultWeight(string $language): float
    {
        if ($language === '*/*') {
            return 0.01;
        }

        if (str_ends_with($language, '*')) {
            return 0.02;
        }

        return 1000 - count($this->cachedMatches);
    }

    private function addGenericMatches(string $language, float $weight, array &$genericMatches): void
    {
        $parts = explode('-', $language);
        array_pop($parts);

        while (!empty($parts)) {
            $weight -= 0.001;
            $generic = implode('-', $parts);

            if (empty($genericMatches[$generic]) || $genericMatches[$generic] > $weight) {
                $genericMatches[$generic] = $weight;
            }

            array_pop($parts);
        }
    }

    private function finalizeMatches(array $matches, array $genericMatches): array
    {
        $allMatches = array_merge($genericMatches, $matches);
        arsort($allMatches, SORT_NUMERIC);
        return $allMatches;
    }

    private function generateCacheKey(): string
    {
        return 'language_negotiation:' . md5(
            $this->request->header('Accept-Language') .
                $this->request->server('REMOTE_HOST')
        );
    }
}
