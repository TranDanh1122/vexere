<?php 

function loadStyleAdmin() {
	\Asset::addDirectly([
        // Font Awesome
        asset('vendor/core/core/base/css/bootstrap.min.css'),
        asset('vendor/core/core/base/plugins/dropzone/dropzone.css'),
        asset('vendor/core/core/base/libraries/admin-resources/rwd-table/rwd-table.min.css'),
        asset('vendor/core/core/base/plugins/toastr/toastr.min.css'),
        asset('vendor/core/core/base/css/icons.min.css'),
        asset('vendor/core/core/base/css/app.min.css'),
        asset('vendor/core/core/base/plugins/select2/css/select2.min.css'),
        asset('vendor/core/core/base/plugins/datetimepicker/jquery.datetimepicker.min.css'),
        asset('vendor/core/core/base/plugins/daterangepicker/daterangepicker.css'),
        asset('vendor/core/core/base/css/nestable.css'),
        asset('vendor/core/core/base/css/style.css'),
        // tách sau
        asset('vendor/core/core/base/plugins/ckeditor5/content-styles.css'),
        asset('vendor/core/core/base/plugins/cascading-selection-box/cascader.css'),
    ], 'styles', 'top')
    ->addDirectly([
        asset('vendor/core/core/base/libraries/jquery/jquery.min.js'),
        asset('vendor/core/core/base/plugins/dropzone/dropzone.js'),
        asset('vendor/core/core/base/plugins/moment/moment.min.js'),
        asset('vendor/core/core/base/plugins/daterangepicker/daterangepicker.js'),
    ], 'scripts', 'top')
    ->addDirectly([
        asset('vendor/core/core/media/js/integrate.js'),
        asset('vendor/core/core/base/js/app.js'),
        asset('vendor/core/core/base/libraries/bootstrap/js/bootstrap.bundle.min.js'),
        asset('vendor/core/core/base/libraries/metismenu/metisMenu.min.js'),
        asset('vendor/core/core/base/libraries/simplebar/simplebar.min.js'),
        asset('vendor/core/core/base/libraries/node-waves/waves.min.js'),
        // asset('vendor/core/core/base/libraries/admin-resources/rwd-table/rwd-table.min.js'),
        // asset('vendor/core/core/base/libraries/app/pagestable-responsive.init.js'),
        asset('vendor/core/core/base/plugins/toastr/toastr.min.js'),
        asset('vendor/core/core/base/libraries/apexcharts/apexcharts.min.js'),
        asset('vendor/core/core/base/plugins/tinymce/tinymce.min.js'),
        asset('vendor/core/core/base/plugins/select2/js/select2.full.min.js'),
        asset('vendor/core/core/base/plugins/datetimepicker/jquery.datetimepicker.full.min.js'),
        asset('vendor/core/core/base/plugins/jquery-ui/jquery-ui.min.js'),
        asset('vendor/core/core/base/libraries/nestable/jquery.nestable.js'),
        asset('vendor/core/core/base/libraries/inputmask/min/jquery.inputmask.bundle.min.js'),
        asset('vendor/core/core/base/libraries/app/pages/form-mask.init.js'),
        asset('vendor/core/core/base/libraries/app/app.js'),
        asset('vendor/core/core/base/js/core.js'),
        asset('vendor/core/core/base/js/functions.js'),
        asset('vendor/core/core/base/js/nestable.js'),
        // tách sau
        asset('vendor/core/core/base/plugins/ckeditor5/ckeditor.js'),
        asset('vendor/core/core/base/plugins/ckeditor5/min/editor.js'),
        asset('vendor/core/core/base/plugins/cascading-selection-box/cascader.js')
    ], 'scripts', 'bottom');
}
