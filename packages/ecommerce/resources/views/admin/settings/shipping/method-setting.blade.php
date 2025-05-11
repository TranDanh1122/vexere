@extends('Core::layouts.app')
@section('content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <div class="page-title-right">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Trang chủ') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.settings.shipping_method') }}">{{ trans('Ecommerce::order.shipping.shipping_methods') }}</a></li>
            </ol>
        </div>
    </div>
    <div class="container">
        @php do_action(ACTION_RENDER_VIEW_CONFIG_SHIPPING_METHOD, $methodKey); @endphp
    </div>
    <div id="confirm-disable-shipping-method-modal" class="modal fade show" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('Ecommerce::order.shipping.deactivate_shipping_method') }}</strong></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">
                    </button>
                </div>
                <div class="modal-body with-padding">
                    {{  trans('Ecommerce::order.shipping.deactivate_shipping_method_description') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="float-start btn btn-warning" data-bs-dismiss="modal">{{ trans('Ecommerce::order.shipping.cancel') }}</button>
                    <a class="float-end btn btn-info" id="confirm-disable-shipping-method-button" href="#" data-route="{{ route('admin.ajax.shipping_method.updateMethodStatus') }}">{{ trans('Ecommerce::order.shipping.agree') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection