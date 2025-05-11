<?php

namespace DreamTeam\Base\Models;

use DreamTeam\Base\Models\BaseModel;

class LanguageMeta extends BaseModel
{
	protected $fillable = [
		'lang_table', 'lang_table_id', 'lang_locale', 'lang_code'
	];
	public $timestamps = false;
}