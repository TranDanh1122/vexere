<?php

namespace DreamTeam\Base\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use DreamTeam\Base\Repositories\Interfaces\LanguageMetaRepositoryInterface;
use DreamTeam\Page\Models\Page;
use DreamTeam\Page\Services\Interfaces\PageServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\MenuServiceInterface;

class BaseHelper
{
    public function formatTime(CarbonInterface $timestamp, string|null $format = 'j M Y H:i'): string
    {
        $first = Carbon::create(0000, 0, 0, 00, 00, 00);

        if ($timestamp->lte($first)) {
            return '';
        }

        return $timestamp->format($format);
    }

    public function formatDate(CarbonInterface|int|string|null $date, string|null $format = null): string|null
    {
        if (empty($format)) {
            $format = 'Y-m-d';
        }

        if (empty($date)) {
            return $date;
        }

        if ($date instanceof CarbonInterface) {
            return $this->formatTime($date, $format);
        }

        return $this->formatTime(Carbon::parse($date), $format);
    }

    public function formatDateTimeToString(CarbonInterface|int|string|null $date): string|null
    {
        if (empty($date)) {
            return null;
        }

        $date = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        $now = Carbon::now();

        if ($date->isToday()) {
            return 'Hôm nay - ' . $date->format('H:i');
        }

        if ($date->isYesterday()) {
            return 'Hôm qua - ' . $date->format('H:i');
        }

        if ($date->diffInDays($now) < 7) {
            return $date->diffInDays($now) . ' ngày trước - ' . $date->format('H:i');
        }

        return $date->format('H:i d/m/Y');
    }

    public function formatDateTime(CarbonInterface|int|string|null $date, string $format = null): string|null
    {
        if (empty($format)) {
            $format = 'Y-m-d H:i:s';
        }

        return $this->formatDate($date, $format);
    }

    public function humanFilesize(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }

    public function getFileData(string $file, bool $convertToArray = true)
    {
        $file = File::get($file);
        if (! empty($file)) {
            if ($convertToArray) {
                return json_decode($file, true);
            }

            return $file;
        }

        if (! $convertToArray) {
            return null;
        }

        return [];
    }

    public function saveFileData(string $path, array|string|null $data, bool $json = true): bool
    {
        try {
            if ($json) {
                $data = $this->jsonEncodePrettify($data);
            }

            if (! File::isDirectory(File::dirname($path))) {
                File::makeDirectory(File::dirname($path), 493, true);
            }

            File::put($path, $data);

            return true;
        } catch (Exception $exception) {
            info($exception->getMessage());

            return false;
        }
    }

    public function jsonEncodePrettify(array|string|null $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function scanFolder(string $path, array $ignoreFiles = []): array
    {
        if (File::isDirectory($path)) {
            $data = array_diff(scandir($path), array_merge(['.', '..', '.DS_Store'], $ignoreFiles));
            natsort($data);

            return $data;
        }

        return [];
    }

    public function getAdminPrefix(): string
    {
        return config('app.admin_dir');
    }

    /**
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     */
    public function isJoined($query, string $table): bool
    {
        $joins = $query->getQuery()->joins;

        if ($joins == null) {
            return false;
        }

        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }


    public function removeQueryStringVars(?string $url, array|string $key): ?string
    {
        if (! is_array($key)) {
            $key = [$key];
        }

        foreach ($key as $item) {
            $url = preg_replace('/(.*)(?|&)' . $item . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    public function cleanEditorContent(?string $value): string
    {
        $value = str_replace('<span class="style-scope yt-formatted-string" dir="auto">', '', $value);

        return htmlentities($this->clean($value));
    }

    public function sortSearchResults(array|Collection $collection, string $searchTerms, string $column): Collection
    {
        if (! $collection instanceof Collection) {
            $collection = collect($collection);
        }

        return $collection->sortByDesc(function ($item) use ($searchTerms, $column) {
            $searchTerms = explode(' ', $searchTerms);

            // The bigger the weight, the higher the record
            $weight = 0;

            // Iterate through search terms
            foreach ($searchTerms as $term) {
                if (str_contains($item->{$column}, $term)) {
                    // Increase weight if the search term is found
                    $weight += 1;
                }
            }

            return $weight;
        });
    }

    public function getDateFormats(): array
    {
        $formats = [
            'Y-m-d',
            'Y-M-d',
            'y-m-d',
            'm-d-Y',
            'M-d-Y',
        ];

        foreach ($formats as $format) {
            $formats[] = str_replace('-', '/', $format);
        }

        $formats[] = 'M d, Y';

        return $formats;
    }

    public static function clean(array|string|null $dirty, array|string $config = null): array|string|null
    {
        if (config('app.enable_less_secure_web', false)) {
            return $dirty;
        }

        return removeScript($dirty ?: '', $config);
    }

    public function html(array|string|null $dirty, array|string $config = null): HtmlString
    {
        return new HtmlString($this->clean($dirty, $config));
    }

    public function hexToRgba(string $color, float $opacity = 1): string
    {
        $rgb = implode(',', $this->hexToRgb($color));

        if ($opacity == 1) {
            return 'rgb(' . $rgb . ')';
        }

        return 'rgba(' . $rgb . ', ' . $opacity . ')';
    }

    public function hexToRgb(string $color): array
    {
        [$red, $green, $blue] = sscanf($color, '#%02x%02x%02x');

        $blue = $blue === null ? 0 : $blue;

        return compact('red', 'green', 'blue');
    }

    public function iniSet(string $key, int|string|null $value): self
    {
        if (config('app.enable_ini_set', true)) {
            @ini_set($key, $value);
        }

        return $this;
    }

    public function maximumExecutionTimeAndMemoryLimit(): self
    {
        $this->iniSet('max_execution_time', -1);
        $this->iniSet('memory_limit', -1);

        return $this;
    }

    public function removeSpecialCharacters(?string $string): array|string|null
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public function getInputValueFromQueryString(string $name): string
    {
        $value = request()->input($name);

        if (! is_string($value)) {
            return '';
        }

        return $value;
    }


    public function stringify($content): ?string
    {
        if (empty($content)) {
            return null;
        }

        if (is_string($content) || is_numeric($content) || is_bool($content)) {
            return $content;
        }

        if (is_array($content)) {
            return json_encode($content);
        }

        return null;
    }

    /**
     * @deprecated
     */
    public function routeIdRegex(): ?string
    {
        return '[0-9]+';
    }

    /**
     * @deprecated
     */
    public function getCurrentPageName(): ?string
    {
        $routeName = Route::current() ? Route::current()->getName() : '';
        if ($routeName && Str::startsWith($routeName, 'app.handle')) {
            $routeName = request()->get('page_route_name');
        } else if ($routeName) {
            $routeName = Str::substr(Str::replace(['app.', '.'], ['', '-'], $routeName), 0, -3);
        }
        if ($routeName) {
            $routeName = 'page-' . Str::slug($routeName, '-');
        }
        return $routeName;
    }

    public function getThemeName(): string
    {
        $listThemeActive = get_active_themes();
        return array_shift($listThemeActive);
    }

    public function getMenuByID($menuId)
    {
        return Cache::remember('menu_item_' . $menuId, now()->addHour(), function () use ($menuId) {
            $menu = app(MenuServiceInterface::class)->findOne(['id' => $menuId, 'status' => BaseStatusEnum::ACTIVE], false);
            if (!$menu || !$menu->value) {
                return [];
            }
            return json_decode(base64_decode($menu->value), true) ?? [];
        });
    }

    public function getMenuWithLocation($location, $lang)
    {
        return Cache::remember('menu_item_' . $location . '_' . $lang, '3600', function () use ($location, $lang) {
            $menu = \DreamTeam\Base\Models\Menu::active()
                ->whereHas('language_metas', function ($query) use ($lang) {
                    return $query->where('lang_locale', $lang ?? App::getLocale());
                })
                ->where('location', $location)
                ->first();
            return json_decode(base64_decode($menu->value ?? ''), 1);
        });
    }

    public function joinPaths(array $paths): string
    {
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public function injectionBreadcrumbSetting(array $breabcrumbs): array
    {
        $currentRoute = Route::current();
        $routeName = $currentRoute ? $currentRoute->getName() : '';
        $routes = admin_menu()->getAllRouteSetting('group_setting');
        $routeInterfaces = admin_menu()->getAllRouteSetting('group_interface');
        if (in_array($routeName, $routes)) {
            $insert[] = [
                'name' => trans('Core::admin.admin_menu.config'),
                'url' => route('admin.settings.groupConfig')
            ];
        } else if (in_array($routeName, $routeInterfaces)) {
            $insert[] = [
                'name' => trans('Core::admin.admin_menu.interface_2'),
                'url' => route('admin.settings.groupInterface')
            ];
        }
        if (isset($insert)) {
            $key = null;
            foreach ($breabcrumbs as $index => $item) {
                if (isset($item['url']) && $item['url'] === route($routeName)) {
                    $key = $index;
                    break;
                }
            }
            array_splice($breabcrumbs, $key, 0, $insert);
        }
        return $breabcrumbs;
    }
}
