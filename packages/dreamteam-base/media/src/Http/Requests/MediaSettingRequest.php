<?php

namespace DreamTeam\Media\Http\Requests;

use DreamTeam\Base\Rules\OnOffRule;
use DreamTeam\Media\Facades\RvMedia;
use DreamTeam\Form\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MediaSettingRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'media_driver' => ['required', 'string', 'in:local,s3,r2,do_spaces,wasabi,bunnycdn'],
            'media_aws_access_key_id' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_secret_key' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_default_region' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_bucket' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_url' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_endpoint' => ['nullable', 'string'],

            'media_r2_access_key_id' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_secret_key' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_bucket' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_endpoint' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_url' => ['nullable', 'string', 'required_if:media_driver,r2'],

            'media_wasabi_access_key_id' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_secret_key' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_default_region' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_bucket' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_root' => ['nullable', 'string'],

            'media_do_spaces_access_key_id' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_secret_key' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_default_region' => ['nullable', 'string', 'size:4', 'required_if:media_driver,do_spaces,in:NYC1,NYC2,NYC3,SFO1,SFO2,SFO3,TOR1,LON1,AMS2,AMS3,FRA1,SGP1,BLR1,SYD1'],
            'media_do_spaces_bucket' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_endpoint' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],

            'media_bunnycdn_hostname' => ['nullable', 'string', 'required_if:media_driver,bunnycdn'],
            'media_bunnycdn_zone' => ['nullable', 'string', 'required_if:media_driver,bunnycdn'],
            'media_bunnycdn_key' => ['nullable', 'string', 'required_if:media_driver,bunnycdn'],
            'media_bunnycdn_region' => ['nullable', 'string', 'max:200'],


            'media_default_placeholder_image' => ['nullable', 'string'],
            'max_upload_filesize' => ['nullable', 'numeric', 'min:0'],

            'media_folders_can_add_watermark' => ['nullable', 'array'],
            'media_folders_can_add_watermark.*' => ['nullable', 'string'],

            'media_watermark_enabled' => new OnOffRule(),
            'media_image_processing_library' => ['nullable', 'in:gd,imagick'],
            'media_watermark_source' => ['nullable', 'string', 'required_if:media_watermark_enabled,1'],
            'media_watermark_size' => ['nullable', 'numeric', 'min:0', 'required_if:media_watermark_enabled,1'],
            'watermark_opacity' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:media_watermark_enabled,1'],
            'media_watermark_position' => [
                'nullable',
                Rule::in(['top-left', 'top-right', 'bottom-left', 'bottom-right', 'center']),
                'required_if:media_watermark_enabled,1',
            ],
            'watermark_position_x' => ['nullable', 'numeric', 'min:0', 'required_if:media_watermark_enabled,1'],
            'watermark_position_y' => ['nullable', 'numeric', 'min:0', 'required_if:media_watermark_enabled,1'],
            'media_compressed_size' => new OnOffRule(),
            'media_show_webp' => new OnOffRule(),
            'media_optimize_image_enabled' => new OnOffRule(),
            'media_use_original_name_for_file_path' => new OnOffRule(),
            'media_optimize_image_quality' => ['nullable', 'numeric', 'min:1', 'max:100', 'required_if:media_optimize_image_enabled,1'],
        ];
        foreach (RvMedia::getSizes() as $thumbModuleName => $sizes) {
            foreach ($sizes as $size => $sizeValue) {
                $rules['media_' . $thumbModuleName . '_sizes_' . $size . '_width'] = ['required', 'numeric', 'min:0'];
                $rules['media_' . $thumbModuleName . '_sizes_' . $size . '_height'] = ['required', 'numeric', 'min:0'];
            }
        }

        return apply_filters('cms_media_settings_validation_rules', $rules);
    }

    public function attributes(): array
    {
        $attributes = [
            'media_driver' => trans('media::media.setting.driver'),
            'media_aws_access_key_id' => trans('media::media.setting.aws_access_key_id'),
            'media_aws_secret_key' => trans('media::media.setting.aws_secret_key'),
            'media_aws_default_region' => trans('media::media.setting.aws_default_region'),
            'media_aws_bucket' => trans('media::media.setting.aws_bucket'),
            'media_aws_url' => trans('media::media.setting.aws_url'),

            'media_r2_access_key_id' => trans('media::media.setting.r2_access_key_id'),
            'media_r2_secret_key' => trans('media::media.setting.r2_secret_key'),
            'media_r2_bucket' => trans('media::media.setting.r2_bucket'),
            'media_r2_endpoint' => trans('media::media.setting.r2_endpoint'),
            'media_r2_url' => trans('media::media.setting.r2_url'),

            'media_wasabi_access_key_id' => trans('media::media.setting.wasabi_access_key_id'),
            'media_wasabi_secret_key' => trans('media::media.setting.wasabi_secret_key'),
            'media_wasabi_default_region' => trans('media::media.setting.wasabi_default_region'),
            'media_wasabi_bucket' => trans('media::media.setting.wasabi_bucket'),
            'media_wasabi_root' => trans('media::media.setting.wasabi_root'),

            'media_do_spaces_access_key_id' => trans('media::media.setting.do_spaces_access_key_id'),
            'media_do_spaces_secret_key' => trans('media::media.setting.do_spaces_secret_key'),
            'media_do_spaces_default_region' => trans('media::media.setting.do_spaces_default_region'),
            'media_do_spaces_bucket' => trans('media::media.setting.do_spaces_bucket'),
            'media_do_spaces_endpoint' => trans('media::media.setting.do_spaces_endpoint'),

            'media_bunnycdn_hostname' => trans('media::media.setting.bunnycdn_hostname'),
            'media_bunnycdn_zone' => trans('media::media.setting.bunnycdn_zone'),
            'media_bunnycdn_key' => trans('media::media.setting.bunnycdn_key'),
            'media_bunnycdn_region' => trans('media::media.setting.bunnycdn_region'),


            'media_default_placeholder_image' => trans('media::media.setting.default_placeholder_image'),
            'max_upload_filesize' => trans('media::media.setting.max_upload_filesize'),

            'media_folders_can_add_watermark' => trans('media::media.setting.media_folders_can_add_watermark'),
            'media_folders_can_add_watermark.*' => trans('media::media.setting.media_folders_can_add_watermark'),

            'media_watermark_enabled' => trans('media::media.setting.watermark_enable'),
            'media_watermark_source' => trans('media::media.setting.watermark_source'),
            'media_watermark_size' => trans('media::media.setting.watermark_size'),
            'watermark_opacity' => trans('media::media.setting.watermark_opacity'),
            'media_watermark_position' => trans('media::media.setting.watermark_position'),
            'watermark_position_x' => trans('media::media.setting.watermark_position_x'),
            'watermark_position_y' => trans('media::media.setting.watermark_position_y'),
            'media_optimize_image_enabled' => trans('media::media.setting.optimize_image_on_off'),
            'media_optimize_image_quality' => trans('media::media.setting.optimize_image_quality'),
            'media_use_original_name_for_file_path' => trans('media::media.setting.use_original_name_for_file_path'),
        ];
        foreach (RvMedia::getSizes() as $thumbModuleName => $sizes) {
            foreach ($sizes as $size => $sizeValue) {
                $attributes['media_' . $thumbModuleName . '_sizes_' . $size . '_width'] = trans('media::media.setting.media_size_width', ['size' => ucfirst($size)]);
                $attributes['media_' . $thumbModuleName . '_sizes_' . $size . '_height'] = trans('media::media.setting.media_size_height', ['size' => ucfirst($size)]);
            }
        }
        return $attributes;
    }

}
