<?php

namespace DreamTeam\Page\Services;

use DreamTeam\Page\Repositories\Interfaces\PageRepositoryInterface;
use DreamTeam\Page\Services\Interfaces\PageServiceInterface;
use DreamTeam\Base\Services\CrudService;
use DreamTeam\Page\Models\Page;
use DreamTeam\Base\Enums\BaseStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\ThemeManager\Facades\ThemeManager;

class PageService extends CrudService implements PageServiceInterface
{

    public function __construct(
        PageRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    public function getPage(Request $request, object $itemSlug, string $slug, string $fullSlug, string $device)
    {
        $page = $this->getPageBySlug($itemSlug->slug);

        $lang = $this->getPageLanguage($page);

        setLanguage($lang);
        $pageUrl = $page->getUrl();
        if (str_ends_with($fullSlug, '.html') === false && preg_match('/^(.*\/)page(\d+)$/', $fullSlug, $matches)) {
            $fullSlug = rtrim($matches[1], '/');
            $paginate = $matches[2];
            $request->merge(['page' => $paginate]);
        }
        $this->checkSlugConsistency($pageUrl, $slug, $fullSlug);

        $cache = $this->getPageCache($page, $lang);
        extract($cache, EXTR_OVERWRITE);

        $admin_bar = route('admin.pages.edit', $page->id);
        $tocConfig = getOption('dreamteam_toc', null, false);

        $hasTocMenu =  $this->showTocMenu($tocConfig, $page);

        $tableOptions = getFilterOptions('FILTER_BEFORE_RENDER_PARTIAL_VIEW_PAGE', 'pages', $page);

        $compact = compact('meta_seo', 'page', 'admin_bar', 'breadcrumb', 'lang', 'switch_language', 'language', 'tocConfig', 'hasTocMenu', 'tableOptions');

        $result = $this->prepareData($page, $request, $device, $compact);

        return $result;
    }

    public function getPageRecordByLangAndOriginId(string $lang, int $originId): Page|null
    {
        return Cache::remember('page_' . $lang . '_' . $originId, now()->addDays(30), function () use ($lang, $originId) {
            $pages = app(LanguageMetaServiceInterface::class)->getMapByTableId($originId, 'pages');
            $pageId = $pages[$lang] ?? 0;
            if ($pageId) {
                return $this->repository->findOneFromArray(['id' => $pageId, 'status' => BaseStatusEnum::ACTIVE], false);
            }
            return null;
        });
    }

    private function getPageBySlug(string $slug)
    {
        $status = [
            BaseStatusEnum::ACTIVE
        ];
        if (Auth::guard('admin')->check()) {
            $status[] = BaseStatusEnum::DRAFT;
        }
        return $this->repository->findOneWithFromConditions(
            [
                'language_metas'
            ],
            [
                'slug'   => ['=' => $slug],
                'status' => ['IN' => $status]
            ],
            'id',
            'desc',
            false
        );
    }

    private function getPageLanguage($page)
    {
        return $page->language_metas->lang_locale ?? App::getLocale();;
    }

    private function checkSlugConsistency(string $pageUrl, string $slug, string $fullSlug)
    {
        $pageLink = getPerMarkLink('page_link', '');
        $pagePath = str_replace('.html', '', ltrim(parse_url($pageUrl)['path'] ?? $pageUrl, '/'));
        $pageLink = rtrim(ltrim($pageLink, '/'), '/');
        $slugNoHost = ltrim(str_replace(config('app.url'), '', $pageUrl), '/');
        if ($pagePath != $slug || $slugNoHost != $fullSlug) {
            abort(404);
        }
    }

    private function getPageCache(Page $page, string $lang)
    {
        return Cache::remember('page_' . $page->id, now()->addDays(30), function () use ($page, $lang) {
            $language = getLanguageLink(Page::class, $page->id, $page->language_metas);
            $switch_language = $language['language'] ?? [];
            $meta_seo = metaSeo('pages', $page->id, [
                'title' => $page->name ?? '',
                'description' => cutString(removeHTMLAndImages($page->detail) ?? ''),
                'url' => $page->getUrl(),
                'image' => getImage(),
                'robots' => 'Index,Follow',
            ]);
            if ($page->status != BaseStatusEnum::ACTIVE) {
                $meta_seo['robots'] = 'Noindex';
            }
            $breadcrumb = [
                [
                    'link' => route('app.home.' . $lang),
                    'name' => __('Theme::general.home')
                ],
                [
                    'name' => $page->name ?? ''
                ],
            ];
            return compact('language', 'switch_language', 'meta_seo', 'breadcrumb');
        });
    }

    private function showTocMenu($tocConfig, Page $page)
    {
        if (in_array('page', ($tocConfig['show'] ?? [])) && ($tocConfig['on_off'] ?? 0) && !$page->hide_toc) {
            \Asset::addDirectly([
                asset('assets/general/build/js/toc/main.min.js'),
            ], 'scripts', 'bottom', ['defer' => '']);
            return true;
        }
        return false;
    }

    private function prepareData(Page $page, Request $request, string $device, array $compact)
    {
        if ($page->hide_sidebar) {
            View::share('hasFullPage', true);
        }

        $filter = apply_filters(BASE_ACTION_PUBLIC_RENDER_SINGLE, PAGE_MODULE_SCREEN_NAME, $page, $compact, $device, $request);

        if ($filter instanceof Page) {
            $compact['page'] = $filter;
        } else if ($filter instanceof \DreamTeam\Shortcode\View\View || ($request->ajax() && isset($filter['total']))) {
            return ['responseAble' => $filter];
        } else if (isset($filter['viewPath'])) {
            return $filter;
        }

        $viewPath = 'Page::partials.' . $device . '.show';

        if (ThemeManager::hasView($device . '.page.show')) {
            $viewPath = ThemeManager::getView($device . '.page.show');
        }

        return compact('viewPath', 'compact');
    }
}
