<?php

namespace DreamTeam\Page\Repositories\Eloquent;

use DreamTeam\Page\Repositories\Interfaces\PageRepositoryInterface;
use DreamTeam\Page\Models\Page;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{

    protected string|null|Model $model = Page::class;


    public function getAllPages(string|null $locale): Collection
    {
        $data = $this->getModel()
            ->active();
        if($locale) {
            $data = $data->whereHas('language_metas', function($query) use ($locale) {
                return $query->where('lang_locale', $locale);
            });
        }

        return $data->get();
    }
}
