<?php

namespace DreamTeam\Translate\Models;

use DreamTeam\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use DreamTeam\Base\Models\LanguageMeta;

class Language extends BaseModel
{
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'languages';

    protected $fillable = [
        'name',
        'locale',
        'is_default',
        'code',
        'is_rtl',
        'flag',
        'order',
        'status'
    ];

    protected $casts = [
        'is_rtl' => 'bool',
        'is_default' => 'bool',
        'order' => 'int',
    ];

    public function meta(): HasMany
    {
        return $this->hasMany(LanguageMeta::class, 'lang_locale', 'locale');
    }
}
