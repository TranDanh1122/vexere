<?php
 
namespace DreamTeam\Table\Providers;
 
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider
{
	/**
     * Register bindings in the container.
     */
    public function register()
    {
        
    }

    /**
     * Boot in the container.
     */
	public function boot() 
    {
        $this->registerModule();
	}

    /*
    * Đăng ký tự động các modules
    */
    private function registerModule() 
    {
        $modulePath = __DIR__.'/../../';
        $moduleName = 'Table';

        // boot languages
        if (File::exists($modulePath . "resources/lang")) {
            $this->loadTranslationsFrom($modulePath . "resources/lang", $moduleName);
            $this->loadJSONTranslationsFrom($modulePath . 'resources/lang');
        }

        // boot views
        if (File::exists($modulePath . "resources/views")) {
            $this->loadViewsFrom($modulePath . "resources/views", $moduleName);
        }

        // boot all helpers
        if (File::exists($modulePath . "helpers")) {
            // get all file in Helpers Folder 
            $helper_dir = File::allFiles($modulePath . "helpers");
            // foreach to require file
            foreach ($helper_dir as $key => $value) {
                $file = $value->getPathName();
                require_once $file;
            }
        }
    }
}