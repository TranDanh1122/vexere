<?php

namespace DreamTeam\Base\Supports;

use DreamTeam\Base\Facades\BaseHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RuntimeException;
use Illuminate\Support\Facades\URL;

class DashboardMenu
{
    protected array $links = [];

    public function registerItem(array $options): self
    {
        if (!is_in_admin()) {
            return $this;
        }

        if (isset($options['childs'])) {
            unset($options['childs']);
        }

        $defaultOptions = [
            'id' => '',
            'priority' => 99,
            'parent_id' => null,
            'name' => '',
            'icon' => null,
            'type' => '',
            'role' => [],
            'route' => '',
            'childs' => [],
            'permissions' => [],
            'active' => [],
            'description' => null,
        ];

        $options = array_merge($defaultOptions, $options);
        $id = $options['id'];

        if (!$id && !app()->runningInConsole() && app()->isLocal()) {
            $calledClass = isset(debug_backtrace()[1]) ?
                debug_backtrace()[1]['class'] . '@' . debug_backtrace()[1]['function']
                :
                null;

            throw new RuntimeException('Menu id not specified: ' . $calledClass);
        }

        if (isset($this->links[$id]) && $this->links[$id]['name'] && !app()->runningInConsole() && app()->isLocal()) {
            $calledClass = isset(debug_backtrace()[1]) ?
                debug_backtrace()[1]['class'] . '@' . debug_backtrace()[1]['function']
                :
                null;

            throw new RuntimeException('Menu id already exists: ' . $id . ' on class ' . $calledClass);
        }

        if (isset($this->links[$id])) {
            $options['childs'] = array_merge($options['childs'], $this->links[$id]['childs']);
            $options['permissions'] = array_merge($options['permissions'], $this->links[$id]['permissions']);

            $this->links[$id] = array_replace($this->links[$id], $options);

            return $this;
        }

        if ($options['parent_id']) {
            if (!isset($this->links[$options['parent_id']])) {
                $this->links[$options['parent_id']] = ['id' => $options['parent_id']] + $defaultOptions;
            }

            $this->links[$options['parent_id']]['childs'][] = $options;

            $permissions = array_merge($this->links[$options['parent_id']]['permissions'], $options['permissions']);
            $this->links[$options['parent_id']]['permissions'] = $permissions;
        } else {
            $this->links[$id] = $options;
        }

        return $this;
    }

    public function removeItem(string|array $id, $parentId = null): self
    {
        if ($parentId && !isset($this->links[$parentId])) {
            return $this;
        }

        $id = is_array($id) ? $id : func_get_args();
        foreach ($id as $item) {
            if (!$parentId) {
                Arr::forget($this->links, $item);

                break;
            }

            foreach ($this->links[$parentId]['childs'] as $key => $child) {
                if ($child['id'] === $item) {
                    Arr::forget($this->links[$parentId]['childs'], $key);

                    break;
                }
            }
        }

        return $this;
    }

    public function hasItem(string $id, ?string $parentId = null): bool
    {
        if ($parentId) {
            if (!isset($this->links[$parentId])) {
                return false;
            }

            $id = $parentId . '.childs.' . $id;
        }

        return Arr::has($this->links, $id . '.name');
    }

    public function getAll(): Collection
    {

        if (setting('cache_admin_menu_enable', true) && Auth::guard('admin')->check()) {
            $cacheKey = md5('cache-dashboard-menu-' . Auth::guard('admin')->user()->id);
            if (!cache()->has($cacheKey)) {
                $links = $this->links;
                cache()->forever($cacheKey, $links);
            } else {
                $links = cache()->get($cacheKey);
            }
        } else {
            $links = $this->links;
        }

        foreach ($links as $key => &$link) {
            if ($link['permissions'] && !Auth::guard('admin')->user()->hasAnyPermission($link['permissions'])) {
                Arr::forget($links, $key);

                continue;
            }
            if (!count($link['childs'])) {
                continue;
            }
            if ($link['parent_id'] && count($link['childs']) && $link['type'] == 'multiple') {
                $copyLink = $link;
                $copyLink['childs'] = [];
                $link['type']  = '';
                $links[$link['parent_id']]['childs'][] = $copyLink;
                $links[$link['parent_id']]['childs'] = collect($links[$link['parent_id']]['childs'])
                    ->unique(fn($item) => $item['id'])
                    ->sortBy('priority')
                    ->toArray();
            }

            $link['childs'] = collect($link['childs'])
                ->unique(fn($item) => $item['id'])
                ->sortBy('priority')
                ->toArray();

            foreach ($link['childs'] as $subKey => $subMenu) {
                if ($subMenu['permissions'] && !Auth::guard('admin')->user()->hasAnyPermission($subMenu['permissions'])) {
                    Arr::forget($link['childs'], $subKey);

                    continue;
                }
            }
        }

        return collect($links)->sortBy('priority');
    }

    public function getMenuSetting(string $key): array
    {
        $menu = $this->getAll();
        if(!isset($menu[$key])) return [];
        $menuConfigs = $menu[$key];
        $newConfig[$menuConfigs['id']] = $menuConfigs;
        $newConfig[$menuConfigs['id']]['childs'] = [];
        foreach ($menuConfigs['childs'] as $item) {
            if ($item['type'] == 'multiple') {
                $newConfig[$item['id']] = $item;
                $newConfig[$item['id']]['childs'] = (($menu[$item['id']] ?? [])['childs'] ?? []);
            } else {
                $newConfig[$item['parent_id']]['childs'][] = $item;
            }
        }
        return $newConfig;
    }

    public function getAllRouteSetting(string $key): array
    {
        $menus = $this->getMenuSetting($key);
        $routes = [];
        foreach($menus as $items) {
            foreach ($items['childs'] as $item) {
                if($item['route']) {
                    $routes[] = $item['route'];
                }
            }
        }
        return $routes;
    }
}
