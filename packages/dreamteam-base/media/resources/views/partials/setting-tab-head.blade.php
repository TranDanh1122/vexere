<div class="col-md-3">
    <ul class="tab-list" style="border: 0;">
        <li class="tab-list__item active" style="display: block;padding-left: 0;border-bottom: 1px solid #ddd;"
            data-active="driver">{{ trans('media::media.setting.driver') }}</li>
        <li class="tab-list__item" style="display: block;padding-left: 0;border-bottom: 1px solid #ddd;"
            data-active="optimize_image">{{ trans('media::media.setting.optimize_image') }}</li>
        <li class="tab-list__item" style="display: block;padding-left: 0;border-bottom: 1px solid #ddd;"
                data-active="watermark">{{ trans('Core::admin.setting.media.watermark') }}</li>
        <li class="tab-list__item" style="display: block;padding-left: 0;border-bottom: 1px solid #ddd;"
            data-active="display_config">{{ trans('Core::admin.setting.media.display_config') }}</li>
        <li class="tab-list__item" style="display: block;padding-left: 0;border-bottom: 1px solid #ddd;"
            data-active="thumbnail">{{ trans('media::media.setting.sizes') }}</li>
    </ul>
</div>
<script>
    $(document).ready(function() {
        var class_active = $('.tab-list__item.active').data('active');
        $('.tab .tab-content').removeClass('active');
        $('.tab .tab-content__' + class_active).addClass('active');
        $('.tab-list__item').on('click', function() {
            var class_active = $(this).data('active');
            $('.tab-list__item').removeClass('active');
            $(this).addClass('active');
            $('.tab .tab-content').removeClass('active');
            $('.tab .tab-content__' + class_active).addClass('active');
        });
        $('.tab-list__item:first-child').trigger('click')
    });
</script>