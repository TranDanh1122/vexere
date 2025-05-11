<?php

namespace DreamTeam\Base\Events;

use DreamTeam\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class ClearCacheEvent extends Event
{
    use SerializesModels;

}
