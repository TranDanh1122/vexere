<?php

namespace DreamTeam\Base\Events;

class UpdateAttributeImageInContentEvent extends Event
{
    public function __construct(
        public $tableName,
        public $id,
        public $field = 'detail'

    ) {}
}
