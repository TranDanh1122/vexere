<?php

namespace DreamTeam\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use DreamTeam\Media\Facades\RvMedia;

class UpdateAttributeImageInContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $tries = 2;
    public $failOnTimeout = true;

    protected $tableName;
    protected $id;
    protected $field;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tableName, $id, $field = 'detail')
    {
        $this->tableName = $tableName;
        $this->id = $id;
        $this->field = $field;
    }

    public function handle()
    {
        Log::info('Starting update attribute img in content');
        try {
            if (!Schema::hasTable($this->tableName)) {
                throw new Exception("{$this->tableName} doesn't exists");
            }

            if (!Schema::hasColumn($this->tableName, $this->field)) {
                throw new Exception("Field {$this->field} doesn't exists in {$this->tableName}");
            }

            $post = DB::table($this->tableName)->where('id', $this->id)->first();

            if (!$post) {
                throw new Exception("{$this->tableName} doesn't exists id {$this->id}");
            }

            $content = $post->{$this->field};

            if (empty($content)) {
                return;
            }

            // update width height alt cho img
            $doc = new \DOMDocument();
            @$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $content);
            libxml_use_internal_errors(true);
            $hasChanged = false;

            foreach ($doc->getElementsByTagName('img') as $image) {
                $src = $image->getAttribute('src');
                $alt = $image->getAttribute('alt');
                $width = $image->getAttribute('width');
                $height = $image->getAttribute('height');

                if (empty($alt)) {
                    $image->setAttribute('alt', getAlt($src));
                    $hasChanged = true;
                }

                if ($width && $height) {
                    continue;
                }


                if (strpos($src, '.gif') === false) {
                    $imageObject = null;
                    try {
                        if (Storage::exists(RvMedia::getRelativePathFromUrl($src))) {
                            $imageObject = RvMedia::imageManager()->read(RvMedia::getRealPath(RvMedia::getRelativePathFromUrl($src)));
                        }
                    } catch (Exception $ex) {
                        Log::error($ex);
                    }
                    // set width height
                    if ($imageObject) {
                        $w = $imageObject->width();
                        $h = $imageObject->height();
                        if ($w && $h) {
                            $image->setAttribute('width', $w);
                            $image->setAttribute('height', $h);
                            $hasChanged = true;
                        }
                    }
                }
            }

            if ($hasChanged) {
                $content = $doc->saveHTML();
                $content = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body|meta))[^>]*>\s*~i', '', $content);

                DB::table($this->tableName)->where('id', $this->id)->update([$this->field => $content]);
            }
        } catch (Exception $e) {
            Log::error('Demo content failed update attribute img in content');
            Log::error($e->getMessage());
        }
    }
}
