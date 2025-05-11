{{--
	@include('Form::base.slug', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'extends' 			=> $item['extends'],
        'unique'            => $item['unique'],
		'table' 			=> $item['table'],
    ])
--}}

@if ($class_col != '')
    <div class="{{ $class_col }}">
@endif
<div id="slug-row__{{ $name }}" class="mb-3 @if ($has_row == true) row @endif"
    style="position: relative; {{ empty(old($name) ?? ($value['value'] ?? '')) ? 'display:  none;' : '' }}">
    <label for="{{ $name ?? '' }}" @if ($has_row == true) class="col-md-2 col-form-label" @endif
        style="margin-bottom: 0;">
        @if ($required == 1)
            *
        @endif @lang($label ?? '')
    </label>
    @if ($has_row == true)
        <div class="col-md-10">
    @endif
    <input type="hidden" class="form-control" autocomplete="off" name="{{ $name ?? '' }}" id="{{ $name ?? '' }}"
        value="{{ old($name) ?? ($value['value'] ?? '') }}">
    <div class="slug" style="max-width: calc(100% - 40px)">
        <div class="slug-content">
            <span>{{ isset($value['dataTable']) ? $value['dataTable']->getUrl() : old($name) ?? ($value['value'] ?? '') }}</span>
        </div>
        <div class="action">
            <a href="javascript:;" title="" class="edit">Edit</a>
            <a href="javascript:;" title="" class="ok">Ok</a>
            <a href="javascript:;" title="" class="cancel">Cancel</a>
        </div>
    </div>
    @if ($has_row == true)
</div>
@endif
</div>

@if ($class_col != '')
    </div>
@endif
@if ($has_row != true)
    <style>
        img.form-loading {
            top: 25px;
        }
    </style>
@endif
<style>
    .slug {
        float: none;
        display: flex;
        align-items: center;
    }

    .add-sync {
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    .add-sync,
    .add-sync label {
        margin-bottom: 0;
    }

    .add-sync label span {
        background: #fcfcd1;
        padding: 1px;
    }

    .add-sync input {
        flex: 0 0 20px;
        width: 20px !important;
        height: 20px !important;
    }
</style>
<script>
    $(document).ready(function() {
        text_error = '@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.is_unique')';
        var get_slug = 1;
        $('body').on('click', '.slug .edit', function() {
            const slug = $('#{{ $name ?? '' }}').val();
            $('.slug-content span').html('<input type="text" class="edit-slug" value="' + slug + '">');
            $(this).hide();
            $('.slug .ok').show();
            $('.slug .cancel').show();
        });
        $('body').on('click', '.slug .ok', function(e) {
            $('button[type="submit"]').attr('disabled', 'disabled')
            let slug = $('body .slug-content .edit-slug').val();
            if (slug == '') {
                alertText('@lang('Translate::form.valid.no_empty')', 'error');
                return;
            }
            const slugOld =
                '{{ isset($value['dataTable']) ? str_replace($value['dataTable']->slug ?? '', '{slugName}', $value['dataTable']->getUrl()) : '{slugName}' }}'
            slug = convertToSlug(slug || '')
            const newSlug = slugOld.replace('{slugName}', slug)
            $('#{{ $name ?? '' }}').val(slug).change();
            $('.slug-content span').html(newSlug);
            $('.slug .edit').show();
            $('.slug .ok').hide();
            $('.slug .cancel').hide();
            $('button[type="submit"]').removeAttr('disabled')
            $('#{{ $name ?? '' }}').parent().find('.form-loading').remove();
            // $('#{{ $name ?? '' }}').parent().append(formLoading('loading'));
            addLoadingBtn($('.slug .edit'))
            get_slug = 0;
        });
        $('body').on('click', '.slug .cancel', function() {
            var slug = $('#{{ $name ?? '' }}').val();
            $('.slug-content span').html(convertToSlug(slug));
            $('.slug .edit').show();
            $('.slug .ok').hide();
            $('.slug .cancel').hide();
            $('button[type="submit"]').removeAttr('disabled');
        });
        {{-- Nếu bắt buộc --}}
        @if ($required == 1)
            validateInput('#{{ $name ?? '' }}', '@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')');
        @endif
        {{-- Nếu có kế thừa từ input khác --}}
        @if (isset($extends) && !empty($extends))
            $('body').on('keyup change', '#{{ $extends ?? '' }}', function() {
                if (get_slug == 1) {
                    value = $(this).val();
                    $('#{{ $name ?? '' }}').val(convertToSlug(value));
                    $('.slug-content span').html(convertToSlug(value));
                    $('#{{ $name ?? '' }}').change();
                    $('#slug-row__{{ $name }}').show()
                }
            });
        @endif

        {{-- Nếu là duy nhất --}}
        @if (isset($unique) && $unique == 'true')
            $check = null;
            $is_unique = true; // true: không trùng | false: bị trùng
            // nếu input có thay đổi
            $('body').on('keyup change', '#{{ $name ?? '' }}', function() {
                // giá trị slug
                $value = $('#{{ $name ?? '' }}').val();
                // Tên bảng
                $table = '{{ $table ?? ($table_name ?? '') }}';
                // xóa setTimeout $check
                clearTimeout($check);
                $('.slug .edit').hide()
                // Nếu xác định được giá trị và bảng
                if (!checkEmpty($value) && !checkEmpty($table)) {
                    // lấy sau keyup 1 giây
                    $check = setTimeout(function() {
                        // chuẩn hóa dữ liệu form
                        data = {
                            table: $table,
                            slug: $value,
                            tableId: '{{ isset($value['dataTable']) ? $value['dataTable']->id : '' }}',
                            tableSlug: '{{ isset($value['dataTable']) ? $value['dataTable']->slug ?? '' : '' }}',
                            tableUrl: '{{ isset($value['dataTable']) ? $value['dataTable']->getUrl() : '' }}',
                            locale: '{{ $value['locale'] ?? (\Request()->lang_locale ?? getLocale()) }}'
                        };
                        // load ajax check tồn tại slug
                        loadAjaxPost('{{ route('admin.ajax.check_slug') }}', data, {
                            beforeSend: function() {
                                $('#{{ $name ?? '' }}').parent().find(
                                    '.form-loading').remove();
                                $('#{{ $name ?? '' }}').parent().append(
                                    formLoading('loading'));
                            },
                            success: function(result) {
                                if (result.status == false) {
                                    $('#{{ $name ?? '' }}').parent().find(
                                        '.form-loading').remove();
                                    $('#{{ $name ?? '' }}').parent().append(
                                        formLoading('error'));
                                    $is_unique = false;
                                    text_error =
                                        '{{ __($label ?? ($placeholder ?? ($name ?? ''))) }} {{ __('Translate::form.valid.is_unique') }}' +
                                        ` ${result.link}`
                                } else {
                                    $('#{{ $name ?? '' }}').parent().find(
                                        '.form-loading').remove();
                                    $('#{{ $name ?? '' }}').parent().append(
                                        formLoading('success'));
                                    $is_unique = true;
                                }
                                if (result.slug) {
                                    $('#{{ $name ?? '' }}').val(result.slug)
                                    $('.slug-content span').html(result.fullUrl);
                                }
                                $('#slug-row__{{ $name }} .add-sync')
                                    .remove()
                                if (result.showSync && result.showSync == true) {
                                    $('#slug-row__{{ $name }} .slug')
                                        .after(`
                                        <p class="add-sync">
                                            <input type="checkbox" class="form-check-input" name="add_slug_sync" value="1" id="add_slug_sync">
                                            <label for="add_slug_sync">{!! __('SyncLink::admin.add_sync', ['link' => isset($value['dataTable']) ? $value['dataTable']->getUrl() : '']) !!} <span>${result.fullUrl}</span></label>
                                        <p>
                                    `)
                                }
                                $('.slug .edit').show()
                                removeLoadingBtn($('.slug .edit'))
                                validateSlug($is_unique, '#{{ $name ?? '' }}',
                                    text_error);
                                if (result.alert) {
                                    alertText(result.alert, 'error')
                                }
                            },
                            error: function(error) {
                                $('#{{ $name ?? '' }}').parent().find(
                                    '.form-loading').remove();
                                $('#{{ $name ?? '' }}').parent().append(
                                    formLoading('error'));
                                $('.slug .edit').show()
                                removeLoadingBtn($('.slug .edit'))
                            }
                        }, 'custom');
                    }, 1000);
                } else {
                    $is_unique = true;
                    validateSlug($is_unique, '#{{ $name ?? '' }}', null);
                }
            });
            $('body').on('click', 'button[type=submit]', function(e) {
                if ($is_unique == false) {
                    e.preventDefault();
                    $('#{{ $name ?? '' }}').parent().append(formHelper(text_error));
                    openPopup(text_error);
                    $('#{{ $name ?? '' }}').css('border', '1px solid #ff0000');
                }
            });
        @endif
    });
</script>
