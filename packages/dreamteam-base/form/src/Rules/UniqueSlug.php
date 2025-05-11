<?php

namespace DreamTeam\Form\Rules;

use Illuminate\Contracts\Validation\Rule;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;

class UniqueSlug implements Rule
{
    protected $ignoreId;
    protected $ignoreTable;

    public function __construct($ignoreId = null, $ignoreTable = null)
    {
        $this->ignoreId = $ignoreId;
        $this->ignoreTable = $ignoreTable;
    }

    public function passes($attribute, $value)
    {
        $conditions['slug'] = ['=' => $value];

        if ($this->ignoreId !== null && $this->ignoreTable !== null) {
            $currentSlug = app(SlugServiceInterface::class)
                ->findOne([
                    'table' => $this->ignoreTable,
                    'table_id' => $this->ignoreId
                ], false);
            if($currentSlug) {
                $conditions['id'] = ['DFF' => $currentSlug->id];
            }
        }
        $checkSlug = app(SlugServiceInterface::class)->findOneWhereFromConditions([], $conditions, 'id', 'desc', false, '*', false);
        return $checkSlug ? false : true;
    }

    public function message()
    {
        return __('Core::admin.general.unique', ['name' => __('Core::admin.general.slug')]);
    }
}
