<div class="settings-sidebar">
    <ul>
        <li @if($setting_name == 'interface_email_ecommerce') class="active" @endif><a href="{!! route('admin.settings.interface_email_ecommerce') !!}">@lang('Ecommerce::admin.setting_email_content')</a></li>
        <li @if($setting_name == 'ec_shipping_method') class="active" @endif><a href="{!! route('admin.settings.shipping_method') !!}">@lang('Ecommerce::product.payment.shipping_method')</a></li>
        @if(!defined('ACTION_DO_NOT_SHOW_PRODUCT_SINGLE_CONFIG'))
            <li @if($setting_name == 'summary_product') class="active" @endif><a href="{!! route('admin.settings.summary_product') !!}">@lang('Ecommerce::admin.summary_product')</a></li>
        @endif
    </ul>
</div>
