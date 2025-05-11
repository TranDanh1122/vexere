<?php

namespace DreamTeam\AdminUser\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DB;

class LicenseSeedCommand extends Command {

    protected $signature = 'license:seeds';

    protected $description = 'Khởi tạo dữ liệu license';

    public function handle() {
        DB::table('settings')->where('key', 'dreamteam')->delete();
        $domain = md5(config('app.url', env('APP_URL')));

        DB::table('settings')->insert(['key'=>'dreamteam', 'value'=>$domain]);
        $this->echoLog('Khởi tạo dữ liệu license thành công');
    }

    public function echoLog($string) {
        $this->info($string);
        Log::info($string);
    }

}
