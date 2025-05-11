<?php

namespace DreamTeam\Media\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use DreamTeam\Base\Supports\SettingStore;
use DreamTeam\Media\Jobs\ConvertMakeMediaFolder;
use DreamTeam\Media\Models\Media;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:media:convert:old:to:new:media', 'Convert old table to new media with folder.')]
class ConvertMediaCommand extends Command
{
    public function handle(SettingStore $settingStore): int
    {
        if(!$this->confirm('Are you sure?')) {
            $this->components->warn('Policy is not approve');
            return self::SUCCESS;
        }
        $oldSetting = getOption('media_config', '', false);
        if (isset($oldSetting['watermark_on']) && $oldSetting['watermark_on'] == 1) {
            $watermarkPosition = match ($oldSetting['watermark_position']) {
                'upperLeft' => 'top-left',
                'upperRight' => 'top-right',
                'center' => 'center',
                'lowerLeft' => 'bottom-left',
                'lowerRight' => 'bottom-right',
                default      => 'bottom-right'
            };
            $settingStore->set('media_watermark_enabled', 1)
                ->set('media_watermark_source', $oldSetting['watermark_logo'])
                ->set('media_watermark_position', $watermarkPosition);
        }
        $settingStore->set('media_show_webp', $oldSetting['show_webp'] ?? 1);
        $settingStore->set('media_compressed_size', $oldSetting['compressed_size'] ?? 1);
        $settingStore->save();

        $medias = Media::all();

        if (!count($medias)) {
            $this->components->warn('No have any media file');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Found %d %s', count($medias), Str::plural('file', count($medias))));
        $converted = $medias->count();

        foreach ($medias->chunk(100) as $files) {
            ConvertMakeMediaFolder::dispatch($files);
        }

        $this->components->info(sprintf('Add job convert media %d %s', $converted, Str::plural('file', $converted)));

        return self::SUCCESS;
    }
}
