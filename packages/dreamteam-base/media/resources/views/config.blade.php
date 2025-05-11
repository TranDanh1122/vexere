<script>
    "use strict";
    var RV_MEDIA_URL = {{ \Illuminate\Support\Js::from(RvMedia::getUrls()) }}
    var RV_MEDIA_CONFIG = {{ \Illuminate\Support\Js::from([
        'permissions' => RvMedia::getPermissions(),
        'translations' => trans('media::media.javascript'),
        'pagination' => [
            'paged' => RvMedia::getConfig('pagination.paged'),
            'posts_per_page' => RvMedia::getConfig('pagination.per_page'),
            'in_process_get_media' => false,
            'has_more' => true,
        ],
        'chunk' => RvMedia::getConfig('chunk'),
        'random_hash' => null,
        'default_image' => RvMedia::getDefaultImage(),
        'is_using_cloud' => RvMedia::isUsingCloud()
    ]) }}

    RV_MEDIA_CONFIG.translations.actions_list.other.properties = '{{ trans('media::media.javascript.actions_list.other.properties') }}';
</script>
