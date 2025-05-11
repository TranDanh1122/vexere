<?php

namespace DreamTeam\Media\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use DreamTeam\Media\Facades\RvMedia;
use DreamTeam\Media\Jobs\ConvertThumbnailMediaV2;
use DreamTeam\Media\Jobs\RemoveOtherThumbnail;
use DreamTeam\Media\Models\Media;
use DreamTeam\Post\Models\Post;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:media:convert-thumbnail-media-v2', 'Convert thumbnail to module thumbnail.')]
class ConvertThumbnailMediaCommand extends Command
{
    public function handle()
    {
        if(!$this->confirm('Are you sure?')) {
            $this->components->warn('Policy is not approve');
            return self::SUCCESS;
        }
        
        $postMedias = Post::select('image')->whereNotNull('image')->pluck('image')->toArray();

        if (!count($postMedias)) {
            $this->components->warn('No have any media file post');
        }
        $this->components->info(sprintf('Found media posts %d %s', count($postMedias), Str::plural('file', count($postMedias))));

        foreach(array_chunk($postMedias, 5) as $postMediaLists) {
            ConvertThumbnailMediaV2::dispatch($postMediaLists, 'posts');
        }

        $this->components->info(sprintf('Add job convert media posts %d %s', count($postMedias), Str::plural('file', count($postMedias))));

        if (is_plugin_active('ecommerce')) {
            $productMedias = \DreamTeam\Ecommerce\Models\Product::select('image', 'slide')->get();
            $productVrMedias = \DreamTeam\Ecommerce\Models\ProductVariant::select('image')->whereNotNull('image')->pluck('image')->toArray();
            $productImages = [];
            foreach($productMedias as $productMd) {
                if (!empty($productMd->slide)) {
                    $slides = array_filter(explode(',', $productMd->slide));
                    $productImages = array_merge($productImages, $slides);
                }
                if (!empty($productMd->image)) {
                    $productImages[] = $productMd->image;
                }
            }
            $productImages = array_merge($productImages, $productVrMedias);
            if (!count($productImages)) {
                $this->components->warn('No have any media file products');
            }
            $this->components->info(sprintf('Found media products %d %s', count($productImages), Str::plural('file', count($productImages))));

            foreach(array_chunk($productImages, 5) as $productMediaLists) {
                ConvertThumbnailMediaV2::dispatch($productMediaLists, 'products');
            }

            $this->components->info(sprintf('Add job convert media products %d %s', count($productImages), Str::plural('file', count($productImages))));
        }

        // remove other thumbnail
        $medias = Media::query()->get();
        foreach($medias->chunk(10) as $media) {
            RemoveOtherThumbnail::dispatch($media);
        }

        return self::SUCCESS;
    }
}
