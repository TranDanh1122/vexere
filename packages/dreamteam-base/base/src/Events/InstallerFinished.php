<?php

namespace DreamTeam\Base\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class InstallerFinished
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
}
