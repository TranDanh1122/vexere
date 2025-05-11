<div class="custom_link_cate" style="border-top: 1px solid;padding-top: 1rem;">
    <span style="font-weight: bold; font-size: 16px">{{ __('Core::admin.setting.link_custom.link', ['name'=>__('Ecommerce::admin.product_category')]) }}</span>
    <p style="font-size: 12px">{{ __('Core::admin.setting.link_custom.link_prod_category_desc') }} {{env('APP_URL')}}/san-pham/danh-muc-san-pham. {{ __('Core::admin.setting.link_custom.if_null') }} {{ env('APP_URL') }}/danh-muc-san-pham</p>
    <div class="mb-3 row " style="position: relative;">
        <label for="link_cate" class="col-md-3 col-form-label">{{  __('Core::admin.setting.link_custom.link', ['name'=>__('Ecommerce::admin.category')]) }}</label>
        <div class="col-md-9">
            <input type="text" class="form-control" autocomplete="off" name="product_category_link" id="link_cate" placeholder="" value="{{$data['product_category_link'] ?? ''}}">
            @include('Form::base.radio', [
                'name'              => 'product_category_link_html',
                'value'             => $data['product_category_link_html'] ?? 0,
                'label'             => __('Core::admin.setting.link_custom.enablehtml'),
                'class_col'         => '',
                'has_full'          => true,
                'options'           => [
                    1 => __('Post::post.setting.yes'),
                    0 => __('Post::post.setting.no'),
                ],
            ])
            @include('Form::base.radio', [
                'name'              => 'product_category_link_structure',
                'value'             => $data['product_category_link_structure'] ?? 0,
                'label'             => __('Ecommerce::admin.category_link_structure'),
                'class_col'         => '',
                'has_full'          => true,
                'options'           => [
                    1 => __('Post::post.setting.yes'),
                    0 => __('Post::post.setting.no'),
                ],
            ])
        </div>
    </div>
</div>
<div class="custom_link_cate">
    <span style="font-weight: bold; font-size: 16px">{{  __('Core::admin.setting.link_custom.link', ['name'=>__('Ecommerce::admin.product')]) }}</span>
    <p style="font-size: 12px">{{ __('Core::admin.setting.link_custom.link_product_desc') }} {{ __('Core::admin.setting.link_custom.if_null') }} {{ env('APP_URL') }}/san-pham.html . <span style="color: red;">{{ __('Core::admin.customlink_require_last', ['name' => '{productname}']) }}</span></p>
    <div class="mb-3 row " style="position: relative;">
        <label for="link_cate" class="col-md-3 col-form-label">{{  __('Core::admin.setting.link_custom.link', ['name'=>__('Ecommerce::admin.product')]) }}</label>
        <div class="col-md-9">
            <input type="text" class="form-control" autocomplete="off" name="product_link" id="link_cate" placeholder="" value="{{$data['product_link'] ?? '/{productname}.html'}}">
            <div class="tag_link" style="padding-left: 0">
                <span>{{ __('Core::admin.setting.link_custom.tag') }}</span>
                <div class="tag_link_list">
                    <span class="tag_product_item" data-id="1" data-value="{productname}">{productname}</span>
                    <span class="tag_product_item" data-id="2" data-value="{productcategory}">{productcategory}</span>
                </div>
            </div>
            <div class="product_link_structure" style="{{ str_contains($data['product_link'] ?? '', '{productcategory}') ? 'display: block;' : 'display: none;' }}">
                @include('Form::base.radio', [
                    'name'              => 'product_link_structure',
                    'value'             => $data['product_link_structure'] ?? 0,
                    'label'             => __('Ecommerce::admin.product_link_structure'),
                    'class_col'         => '',
                    'has_full'          => true,
                    'options'           => [
                        1 => __('Post::post.setting.yes'),
                        0 => __('Post::post.setting.no'),
                    ],
                ])
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function(){
		// san pham
        $('body').on('click', '.tag_product_item', function() {
            value_tag = $(this).data('value');
            id_tag = $(this).data('id');
            tag = $('input[name="product_link"]').val();
            if(tag.includes(value_tag) == false) {
                if(value_tag == '{productcategory}') {
                    $('.product_link_structure').css('display', 'block');
                    tag = '/{productcategory}' + tag
                } else {
                    tag = tag.replace('.html', `/${value_tag}.html`)
                }
                $('input[name="product_link"]').val(tag);
                $(this).addClass('active');
            } else {
                tag = tag.replace(`/${value_tag}`, '')
                $('input[name="product_link"]').val(tag);
                $(this).removeClass('active');
                if(value_tag == '{productcategory}') {
                    $('.product_link_structure').css('display', 'none');
                }
            }
            valueTag = $('input[name="product_link"]').val();
            if(valueTag.includes('/{productname}') == false) {
                valueTag = valueTag.replace('.html', '/{productname}.html')
                if(valueTag.includes('/{productname}') == false) {
                	 valueTag = valueTag + '/{productname}.html'
                }
                $('input[name="product_link"]').val(valueTag);
                $(this).addClass('active');
            }
            if(valueTag.includes('{productcategory}.html') != false) {
                valueTag = valueTag.replace('/{productcategory}.html', '.html')
                valueTag = '/{productcategory}' + valueTag
                $('input[name="product_link"]').val(valueTag);
            }
        });
        $('.tag_product_item').each(function(e) {
            let val = $(this).data('value');
            tag = $('input[name="product_link"]').val();
            if(tag.includes(val) != false) {
                $(this).addClass('active')
            }
        })

        $('body').on('blur', 'input[name="product_link"]', function(){
            let input = $(this).val();
            let inputArray = input.split('/');
            if(!input.includes('{productname}')) {
                input = input.replace('.html', '')
                input = input + '{productname}.html'
                input = input.replace('}{', '}/{')
                $('input[name="product_link"]').val(input);
            }
        })
	})
</script>