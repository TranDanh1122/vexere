<?php

namespace DreamTeam\Page\Models;

use DreamTeam\Base\Models\BaseModel;

class Page extends BaseModel
{

    protected $casts = [];
    protected $fillable = [
        'name', 'slug', 'detail', 'status', 'hide_title', 'hide_sidebar', 'hide_breadcrumb', 'hide_toc', 'seo_point', 'primary_keyword', 'secondary_keyword', 'google_index'
    ];

	public function getUrl($device='app')
    {
        return route('app.pages.show', $this->slug);
    }
    
}
