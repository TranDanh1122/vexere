<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\SeoRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\SeoServiceInterface;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Base\Services\CrudService;

class SeoService extends CrudService implements SeoServiceInterface
{

    public function __construct(
        SeoRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function createMetaSeo($requests, string $type, int|string $typeId): Model
    {
        $isCustomCanonical = intval($requests->get('is_custom_canonical', 0));
        $canonical = '';
        if ($isCustomCanonical) {
            $canonical = $requests->get('canonical', '');
        }
        $showOnSitemap = intval($requests->get('show_on_sitemap', 0));
        $dataSeo = [
            'type'               => $type,
            'type_id'            => $typeId,
            'title'              => $requests->meta_title ?: '',
            'description'        => $requests->meta_description ?: '',
            'html_head'          => $requests->html_head ?: '',
            'robots'             => $requests->meta_robots ?: '',
            'social_image'       => $requests->social_image ?: '',
            'social_title'       => $requests->social_title ?: '',
            'social_description' => $requests->social_description ?: '',
            'is_custom_canonical' => $isCustomCanonical,
            'canonical'          => $canonical,
            'show_on_sitemap'    => $showOnSitemap,
        ];
        return $this->repository->createMetaSeo($type, $typeId, $dataSeo);
    }
}
