@php
    $permark_link = config('permark_links');
@endphp
<div class="custom_link">
    <div class="form-radio" style="margin-left: 18px;">
        @foreach ($permark_link as $key => $link)
            <div class="form-check" style="margin-bottom: 20px">
                <input type="radio" class="form-check-input" name="check_permark_link"
                    id="check_permark_link_{{ $key ?? 0 }}" value="{{ $key ?? 0 }}"
                    @if (isset($data['check_permark_link']) && $key == intval($data['check_permark_link'])) checked @elseif(!isset($data['check_permark_link']) && $key == 5) checked @endif
                    style="font-size: 18px;">
                <label style="padding-top: 4px; display: inline-block;min-width: 200px;" class="form-check-label"
                    for="check_permark_link_{{ $key ?? 0 }}">{{ __($link['name'] ?? '') }}</label>
                <span class="link_example"
                    style="padding: 0 5px;background: #ededed;display: inline-block;">{{ env('APP_URL') }}/{{ $link['url_example'] ?? '' }}</span>
                @if ($key == 6)
                    <input type="text" name="link_custom" class="form-control"
                        value="{{ $data['link_custom'] ?? '/{postname}.html' }}  "
                        style="max-width: 700px;display: inline-block;margin-left: 5px">
                    <input type="hidden" name="id_tag_item" value="{{ $data['id_tag_item'] ?? 8 }}">
                    <div class="tag_link">
                        <span>{{ __('Core::admin.setting.link_custom.tag') }}</span>
                        <div class="tag_link_list">
                            <span class="tag_item" data-id="1" data-value="{year}">{year}</span>
                            <span class="tag_item" data-id="2" data-value="{monthnum}">{monthnum}</span>
                            <span class="tag_item" data-id="3" data-value="{day}">{day}</span>
                            <span class="tag_item" data-id="4" data-value="{hour}">{hour}</span>
                            <span class="tag_item" data-id="5" data-value="{minute}">{minute}</span>
                            <span class="tag_item" data-id="6" data-value="{second}">{second}</span>
                            <span class="tag_item" data-id="7" data-value="{post_id}">{post_id}</span>
                            <span class="tag_item active" data-id="8" data-value="{postname}">{postname}</span>
                            <span class="tag_item" data-id="9" data-value="{category}">{category}</span>
                            <span class="tag_item" data-id="10" data-value="{author}">{author}</span>
                        </div>
                    </div>
                @endif
                @if ($key == 7)
                    <div class="post_link_structure">
                        @include('Form::base.radio', [
                            'name' => 'post_link_structure',
                            'value' => $data['post_link_structure'] ?? 0,
                            'label' => __('Post::post.post_link_structure'),
                            'class_col' => '',
                            'has_full' => true,
                            'options' => [
                                1 => __('Post::post.setting.yes'),
                                0 => __('Post::post.setting.no'),
                            ],
                        ])
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
<div class="custom_link_cate">
    <span style="font-weight: bold; font-size: 16px">{{ __('Core::admin.setting.link_custom.options') }}</span>
    <p style="font-size: 12px">{{ __('Core::admin.setting.link_custom.link_category_desc') }}
        {{ env('APP_URL') }}/tin-tuc/chuyen-muc. {{ __('Core::admin.setting.link_custom.if_null') }}
        {{ env('APP_URL') }}/chuyen-muc</p>
    <div class="mb-3 row " style="position: relative;">
        <label for="link_cate"
            class="col-md-3 col-form-label">{{ __('Core::admin.setting.link_custom.link', ['name' => __('Post::post.post_category')]) }}</label>
        <div class="col-md-9">
            <input type="text" class="form-control" autocomplete="off" name="permark_link_cate" id="link_cate"
                placeholder="" value="{{ $data['permark_link_cate'] ?? '' }}">
            @include('Form::base.radio', [
                'name' => 'permark_link_cate_html',
                'value' => $data['permark_link_cate_html'] ?? 0,
                'label' => __('Core::admin.setting.link_custom.enablehtml'),
                'class_col' => '',
                'has_full' => true,
                'options' => [
                    1 => __('Post::post.setting.yes'),
                    0 => __('Post::post.setting.no'),
                ],
            ])
            @include('Form::base.radio', [
                'name' => 'post_category_link_structure',
                'value' => $data['post_category_link_structure'] ?? 0,
                'label' => __('Post::post.category_link_structure'),
                'class_col' => '',
                'has_full' => true,
                'options' => [
                    1 => __('Post::post.setting.yes'),
                    0 => __('Post::post.setting.no'),
                ],
            ])
        </div>
    </div>
</div>
<div class="custom_link_cate">
    <span
        style="font-weight: bold; font-size: 16px">{{ __('Core::admin.setting.link_custom.link', ['name' => __('Page::page.name')]) }}</span>
    <p style="font-size: 12px">{{ __('Core::admin.setting.link_custom.link_page_desc') }}
        {{ __('Core::admin.setting.link_custom.if_null') }} {{ env('APP_URL') }}/trang . <span
            style="color: red;">{{ __('Core::admin.customlink_require_last', ['name' => '{pagename}']) }}</span></p>
    <div class="mb-3 row " style="position: relative;">
        <label for="link_cate"
            class="col-md-3 col-form-label">{{ __('Core::admin.setting.link_custom.link', ['name' => __('Page::page.name')]) }}</label>
        <div class="col-md-9">
            <input type="text" class="form-control" autocomplete="off" name="page_link" id="link_cate" placeholder=""
                value="{{ $data['page_link'] ?? '' }}">
        </div>
    </div>
</div>
@php
    do_action(INIT_LINK_CUSTOM, $data);
@endphp
<style>
    .tag_link,
    .post_link_structure {
        padding-left: 200px;
    }

    .tag_link_list {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .element-item,
    .tag_product_item,
    .tag_item {
        padding: 5px 10px;
        border-radius: 3px;
        border: 1px solid #ccc;
        margin-right: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .element-item:hover,
    .tag_product_item:hover,
    .tag_item:hover {
        background: #ededed;
    }

    .element-item.active,
    .tag_product_item.active,
    .tag_item.active {
        background: #ededed;
    }
</style>
<script>
    $(document).ready(function() {
        $('body').on('click', '.tag_item', function() {
            value_tag = $(this).data('value');
            id_tag = $(this).data('id');
            tag = $('input[name="link_custom"]').val();
            if (tag.includes(value_tag) == false) {
                if (value_tag == '{category}') {
                    tag = '/{category}' + tag
                } else {
                    tag = tag.replace('.html', `/${value_tag}.html`)
                }
                $('input[name="link_custom"]').val(tag);
                $(this).addClass('active');
            } else {
                tag = tag.replace(`/${value_tag}`, '')
                $('input[name="link_custom"]').val(tag);
                $(this).removeClass('active');
            }
            valueTag = $('input[name="link_custom"]').val();
            if (valueTag.includes('/{postname}') == false && valueTag.includes('/{post_id}') == false) {
                valueTag = valueTag.replace('.html', '/{post_id}.html')
                $('input[name="link_custom"]').val(valueTag);
                $(this).addClass('active');
            }
            if (valueTag.includes('{category}.html') != false) {
                valueTag = valueTag.replace('/{category}.html', '.html')
                valueTag = '/{category}' + valueTag
                $('input[name="link_custom"]').val(valueTag);
            }
        });
        $('.tag_item').each(function(e) {
            let val = $(this).data('value');
            tag = $('input[name="link_custom"]').val();
            if (tag.includes(val) != false) {
                $(this).addClass('active')
            }
        })
        $('body').on('click', 'button[type=submit]', function(e) {
            let checked = parseInt($('input[name="check_permark_link"]:checked').val());
            let input = $('input[name="link_custom"]').val();
            let inputArray = input.split('/');
            if (checked == 6 && input.includes('{category}') && inputArray[1] != '{category}') {
                e.preventDefault();
                alert('Giá trị biến {category} bắt buộc phải ở vị trí đầu tiên!')
            }
            if (confirm('{{ __('Core::admin.setting.link_custom.warning') }}')) {
                loadingBox('open')
                $(this).closest('form').submit()
            }
            e.preventDefault()
        })
        $('body').on('blur', 'input[name="link_custom"]', function() {
            let checked = parseInt($('input[name="check_permark_link"]:checked').val());
            let input = $('input[name="link_custom"]').val();
            let inputArray = input.split('/');
            if (checked == 6 && input.includes('{category}') && inputArray[1] != '{category}') {
                input = input.replace('/{category}', '')
                input = '/{category}' + input
                $('input[name="link_custom"]').val(input);
            }
        })
    });
</script>
