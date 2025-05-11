<?php

namespace DreamTeam\Base\Models;

use DreamTeam\Base\Models\BaseModel;

class Seo extends BaseModel
{
	protected $fillable = [
		'type', 'type_id', 'title', 'description', 'html_head', 'robots', 'social_image', 'social_title', 'social_description', 'is_custom_canonical', 'canonical', 'show_on_sitemap'
	];
	public $timestamps = false;
}
