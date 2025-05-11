@extends('Core::layouts.app')
@section('content')
    <div class="row">
        <div class="card listdata" id="listdata">
            <div class="table-rep-plugin">
                <div class="table-wrapper">
                    <div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                        <table class="table table-striped">
                            <thead>
                                <th style="width: 40px;" class="text-center">STT</th>
                                <th class="text-center">{{ trans('Ecommerce::order.name_method') }}</th>
                                <th class="text-center">{{ trans('Ecommerce::order.status') }}</th>
                                <th class="text-center">{{ trans('Ecommerce::order.option') }}</th>
                            </thead>
                            <tbody>
                                @if(count(EcommerceHelper::getAllShippingMethods()))
                                    @foreach(EcommerceHelper::getAllShippingMethods() as $methodKey => $value)
                                    @php
                                        $shippingStatus = setting('shipping_'.$methodKey.'_status');
                                    @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <span>{{ $value['name'] ?? '' }}</span>
                                                @if($methodKey ==  EcommerceHelper::defaultShippingMethod())
                                                    <span class="badge badge-secondary ms-2">{{ trans('Ecommerce::order.default_method') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($shippingStatus == 1)
                                                    <span class="badge badge-success px-4 py-2">{{ trans('Ecommerce::order.turn_on') }}</span>
                                                @else
                                                    <span class="badge badge-secondary px-4 py-2">{{ trans('Ecommerce::order.turn_off') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.settings.shipping_method', ['methodKey' => $methodKey]) }}" class="btn btn-info btn-sm">{{ trans('Ecommerce::order.setting') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <p>{!! trans('Ecommerce::admin.plugin_active_shipping', ['route' => route('admin.plugins.index')]) !!}</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
