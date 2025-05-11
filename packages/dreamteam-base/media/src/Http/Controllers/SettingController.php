<?php

namespace DreamTeam\Media\Http\Controllers;

use Exception;
use DreamTeam\Base\Http\Controllers\AdminController;
use Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Base\Http\Responses\BaseHttpResponse;
use DreamTeam\Base\Models\Setting as SettingModel;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Media\Facades\RvMedia;
use DreamTeam\Media\Models\MediaFolder;
use DreamTeam\Media\Http\Requests\MediaSettingRequest;
use DreamTeam\Media\Models\Media;
use DreamTeam\Base\Facades\Setting;
use DreamTeam\Media\Jobs\GenerateThumbnail;
use DreamTeam\Base\Events\ClearCacheEvent;

class SettingController extends AdminController
{

    protected SettingServiceInterface $settingService;

    function __construct(
        SettingServiceInterface $settingService
    ) {
        parent::__construct();
        $this->table_name = (new SettingModel)->getTable();
        $this->settingService = $settingService;
    }

    public function edit(Request $requests)
    {
        \Asset::addDirectly([asset('vendor/core/core/media/js/media-setting.js')], 'scripts', 'bottom');
        $setting_name   = 'media_config';
        $module_name    = "Core::admin.setting.media.name";
        $note           = "Translate::form.require_text";
        $breadcrumbs = [['name' => $module_name]];
        $folders = MediaFolder::query()->where('parent_id', 0)->pluck('name', 'id')->all();
        $folderIds = old($key = 'media_folders_can_add_watermark', json_decode((string) setting($key), true));

        $dataPositions = [
            'top-left' => __('Core::admin.setting.media.top_left'),
            'top-right' => __('Core::admin.setting.media.top_right'),
            'center' => __('Core::admin.setting.media.center'),
            'bottom-left' => __('Core::admin.setting.media.bottom_left'),
            'bottom-right' => __('Core::admin.setting.media.bottom_right'),
        ];
        // Khởi tạo form
        $form = new Form;
        $form->html('div', ['class' => 'row tab']);
        $form->custom('media::partials.setting-tab-head');
        $form->html('div', ['class' => 'col-md-9']);
        $form->card();
        $form->html('div', ['class' => 'tab-content tab-content__driver active']);
        $form->head(trans('media::media.setting.driver'));
        $form->custom('media::partials.media-driver-field');
        $form->hidden('media_use_original_name_for_file_path', 0);
        $form->checkbox('media_use_original_name_for_file_path', setting('media_use_original_name_for_file_path', 0), 1, __('media::media.setting.use_original_name_for_file_path'));
        $form->image('media_default_placeholder_image', setting('media_default_placeholder_image'), 0, trans('media::media.setting.default_placeholder_image'));
        $form->number('max_upload_filesize', setting('max_upload_filesize'), 0, trans('media::media.setting.max_upload_filesize'), trans('media::media.setting.max_upload_filesize_placeholder', [
            'size' => ($maxSize = BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize())),
        ]), false, '', false, false);
        $form->note(trans('media::media.setting.max_upload_filesize_helper', ['size' => $maxSize]));
        $form->html('/div');
        $form->html('div', ['class' => 'tab-content tab-content__optimize_image']);
        $form->head(__('media::media.setting.optimize_image'));
        $form->card('toggle-on-off-checkbox');
        $form->hidden('media_optimize_image_enabled', 0);
        $form->checkbox('media_optimize_image_enabled', setting('media_optimize_image_enabled', 0), 1, __('media::media.setting.optimize_image_on_off'));
        $form->endCard();
        $classCheck = (setting('media_optimize_image_enabled', 0)) == 1 ? 'on-of-checkbox-config show' : 'on-of-checkbox-config hide';
        $form->card($classCheck);
        $form->number('media_optimize_image_quality', setting('media_optimize_image_quality'), 0, trans('media::media.setting.optimize_image_quality'), trans('media::media.setting.optimize_image_quality'), false, '', false, false);
        $form->note(trans('media::media.setting.optimize_image_help'));
        $form->endCard();
        $form->html('/div');
        $form->html('div', ['class' => 'tab-content tab-content__watermark']);

        $form->head(__('Core::admin.setting.media.watermark'));
        $form->card('toggle-on-off-checkbox');
        $form->hidden('media_watermark_enabled', 0);
        $form->checkbox('media_watermark_enabled', setting('media_watermark_enabled', 0), 1, __('Core::admin.setting.media.watermark_on_off'));
        $form->endCard();
        $classCheck = (setting('media_watermark_enabled', 0)) == 1 ? 'on-of-checkbox-config show' : 'on-of-checkbox-config hide';
        $form->card($classCheck);
        $form->alert('warning', trans('media::media.setting.watermark_description'));
        $form->custom(
            'media::partials.media-folders-can-add-watermark-field',
            compact('folders', 'folderIds')
        );
        $form->image('media_watermark_source', setting('media_watermark_source'), 0, __('Core::admin.general.pick_image'), '', __('Theme::admin.setting.size_image', ['size' => '50x50']));
        $form->row();
        $form->col('col-lg-6');
        $form->number('media_watermark_size', setting('media_watermark_size', RvMedia::getConfig('watermark.size')), 0, trans('media::media.setting.watermark_size'), trans('media::media.setting.watermark_size_placeholder'), false, '', false, false);
        $form->note(trans('media::media.setting.watermark_size_note'));
        $form->endCol();
        $form->col('col-lg-6');
        $form->number('watermark_opacity', setting('watermark_opacity', RvMedia::getConfig('watermark.opacity')), 0, trans('media::media.setting.watermark_opacity'), trans('media::media.setting.watermark_opacity_placeholder'), false, '', false, false);
        $form->endCol();
        $form->endRow();
        $form->row();
        $form->col('col-lg-4');
        $form->select('media_watermark_position', setting('media_watermark_position', RvMedia::getConfig('watermark.position')), 0, __('Core::admin.setting.media.watermark_position'), $dataPositions);
        $form->endCol();
        $form->col('col-lg-4');
        $form->number('watermark_position_x', setting('watermark_position_x', RvMedia::getConfig('watermark.x')), 0, trans('media::media.setting.watermark_position_x'), trans('media::media.setting.watermark_position_x'), false, '', false, false);
        $form->endCol();
        $form->col('col-lg-4');
        $form->number('watermark_position_y', setting('watermark_position_y', RvMedia::getConfig('watermark.y')), 0, trans('media::media.setting.watermark_position_y'), trans('media::media.setting.watermark_position_y'), false, '', false, false);
        $form->endCol();
        $form->endRow();
        $form->endCard();
        $form->html('/div');
        $form->html('div', ['class' => 'tab-content tab-content__display_config']);
        $form->head(__('Core::admin.setting.media.display_config'));
        $form->hidden('media_show_webp', 0); // default option
        $form->checkbox('media_show_webp', setting('media_show_webp', 1), 1, __('Core::admin.setting.media.show_webp'));
        $form->html('/div');
        $form->html('div', ['class' => 'tab-content tab-content__thumbnail']);
        $form->head(trans('media::media.setting.sizes'));
        $form->hidden('media_compressed_size', 0); // default option
        $form->checkbox('media_compressed_size', setting('media_compressed_size', 1), 1, __('Core::admin.setting.media.compressed_size'));
        $class = setting('media_compressed_size', 1) ? '' : 'd-none';
        $form->col('thumbnail-wraper ' . $class);
        foreach (RvMedia::getSizes() as $thumbModuleName => $sizes) {
            $form->head('Thumbnail of ' . $thumbModuleName);
            foreach ($sizes as $name => $size) {
                $sizeExploded = explode('x', $size);
                $form->custom('media::partials.form-media-size-label', compact('name'));
                $form->row();
                $form->col('col-lg-6');
                $form->number($nameWidth = sprintf('media_%s_sizes_%s_width', $thumbModuleName, $name), setting($nameWidth, $sizeExploded[0]), 0, '', '', false, '', false, false);
                $form->endCol();
                $form->col('col-lg-6');
                $form->number($nameHeight = sprintf('media_%s_sizes_%s_height', $thumbModuleName, $name), setting($nameHeight, $sizeExploded[1]), 0, '', '', false, '', false, false);
                $form->endCol();
                $form->endRow();
            }
        }
        $form->alert('info', trans('media::media.setting.media_sizes_helper'));
        $form->endCol();
        $form->html('/div');
        $form->endCard();
        $form->html('/div');
        $form->html('/div');
        $form->custom('media::partials.media-action');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name',
            'breadcrumbs'
        ), 'media::partials.media-setting');
    }

    public function update(MediaSettingRequest $request): BaseHttpResponse
    {

        return $this->updateSetting([
            ...$request->validated(),
            'media_folders_can_add_watermark' => $request->boolean('media_folders_can_add_watermark_all')
                ? []
                : $request->input('media_folders_can_add_watermark', []),
        ]);
    }

    public function generateThumbnails(): BaseHttpResponse
    {
        if (!(bool)getMediaConfig('media_compressed_size', Rvmedia::getConfig('generate_thumbnails_enabled'))) {
            return BaseHttpResponse::make()
                ->setError()
                ->setMessage(trans('media::media.setting.cant_generate_thumbnail'));
        }
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $moduleRegister = RvMedia::getThumbnailModules();
        $count = 0;
        if (count($moduleRegister)) {
            foreach ($moduleRegister as $moduleName => $serviceClass) {
                $images = [];
                if ($serviceClass && interface_exists($serviceClass)) {
                    if (method_exists($serviceClass, 'getThumbnailImages')) {
                        try {
                            $images = app($serviceClass)->getThumbnailImages();
                        } catch (Exception $e) {
                            Log::error("Get thumbnail images error" . $e->getMessage());
                        }
                    }
                }
                if (count($images)) {
                    $count += count($images);
                    Log::debug('Add job generate thumbnail of ' . $moduleName . ' success ' . count($images) . ' images!');
                    foreach (array_chunk($images, 10) as $file) {
                        GenerateThumbnail::dispatch($file, $moduleName);
                    }
                }
            }
        }

        return BaseHttpResponse::make()
            ->setMessage(trans('media::media.setting.generate_thumbnails_success', ['count' => $count]));
    }

    protected function saveSettings(array $data, string $prefix = ''): void
    {
        BaseHelper::iniSet('max_input_vars', 20000);
        foreach ($data as $settingKey => $settingValue) {
            if (is_array($settingValue)) {
                $settingValue = json_encode(array_filter($settingValue));
            }
            Setting::set($prefix . $settingKey, (string) $settingValue, false);
        }
        Setting::save();
    }

    protected function updateSetting(array $data, string $prefix = ''): BaseHttpResponse
    {
        $this->saveSettings($data, $prefix);
        event(new ClearCacheEvent());
        return BaseHttpResponse::make()
            ->setMessage(trans('Core::admin.update_success'));
    }
}
