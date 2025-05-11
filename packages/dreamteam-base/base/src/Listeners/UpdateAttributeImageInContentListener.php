<?php

namespace DreamTeam\Base\Listeners;

use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Base\Jobs\UpdateAttributeImageInContent;

class UpdateAttributeImageInContentListener
{
    public function __construct()
    {
    }

    public function handle(UpdateAttributeImageInContentEvent $event): void
    {
        UpdateAttributeImageInContent::dispatch($event->tableName, $event->id, $event->field);
    }
}
