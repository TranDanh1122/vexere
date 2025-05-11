<?php

namespace DreamTeam\Base\Commands;

use Illuminate\Console\Command;
use DB;
use Log;
use DreamTeam\Base\Jobs\UpdateAttributeImageInContent;

class UpdateAttributeImageInContentCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_attribute_image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật thuộc tính ảnh trong bài viết';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function echoLog($string, $type = 'info') {
        $this->info($string);
        switch ($type) {
            case 'info':
                Log::info($string);
            break;
            case 'warning':
                Log::warning($string);
            break;
            case 'error':
                Log::error($string);
            break;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	// Bắt đầu Job
        ini_set('memory_limit', '300M');
        set_time_limit(0);

        $this->echoLog('Starting create job update attribute image');

        $tables = [
            'posts'                     => 'detail',
            'post_categories'           => 'detail',
            'product_categories'        => 'detail',
            'products'                  => 'detail',
            'pages'                     => 'detail',
        ];

        foreach ($tables as $tableName => $field) {
            if(!\Schema::hasTable($tableName)){
                $this->echoLog("$tableName doesn't exists");
            }

            if(!\Schema::hasColumn($tableName, $field)){
                $this->echoLog("Field $field doesn't exists in $tableName");
            }

            DB::table($tableName)->orderBy('id', 'asc')
                            ->select('id')
                            ->chunk(500, function ($items) use($tableName, $field) {
                                foreach ($items as $item){
                                    $id = $item->id;

                                    UpdateAttributeImageInContent::dispatch($tableName, $id, $field);

                                }

                            });

        }

        $this->echoLog('Done create job update attribute image');

    }
}
