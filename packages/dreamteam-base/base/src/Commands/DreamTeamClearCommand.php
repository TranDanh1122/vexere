<?php

namespace DreamTeam\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DB;

class DreamTeamClearCommand extends Command {

    protected $signature = 'dreamteam:clear';

    protected $description = 'Xóa dữ liệu cache';

    public function handle() {
    	\Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        $this->echoLog('Delete cache success');
    }

    public function echoLog($string) {
        $this->info($string);
        Log::info($string);
    }

}