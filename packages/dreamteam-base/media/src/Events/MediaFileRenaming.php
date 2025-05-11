<?php

namespace DreamTeam\Media\Events;

use DreamTeam\Media\Models\Media;
use Illuminate\Foundation\Events\Dispatchable;

class MediaFileRenaming
{
    use Dispatchable;

    public function __construct(public Media $file, public string $newName, public bool $renameOnDisk)
    {
    }
}
