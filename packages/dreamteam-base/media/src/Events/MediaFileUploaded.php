<?php

namespace DreamTeam\Media\Events;

use DreamTeam\Media\Models\Media;
use Illuminate\Foundation\Events\Dispatchable;

class MediaFileUploaded
{
    use Dispatchable;

    public function __construct(public Media $file)
    {
    }
}
