<div marginwidth="0" marginheight="0" style="padding:0">
    <div dir="ltr" style="background-color:#f5f5f5;margin:0;padding:70px 0;width:100%">
        @php
            $config_general = getOption('general');
        @endphp
        <table border="0" cellpadding="0" cellspacing="0" width="600"
            style="background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:3px;margin: 0 auto;padding: 15px 0;">
            <tbody>
                <tr>
                    <td>
                        <img style="display: block;margin: 0 auto;"
                            src="{{ asset($config_general['logo_header'] ?? '') }}" alt="{{ env('APP_NAME', '') }}"
                            title="Logo">
                        <p style="padding: 0 20px">{{ __('Ecommerce::admin.thank_order') }} {{ env('APP_NAME', '') }}
                        </p>
                    </td>
                </tr>
                <tr style="padding: 0 10px;display: block;">

                    <td
                        style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;display: block;width: 100%">
                        <h2
                            style="text-align:left;margin:10px;border-bottom:1px solid #ddd;padding-bottom:5px;font-size:13px;color:#078546">
                            {{ __('Ecommerce::admin.info_order') }} #{!! $order->code ?? '' !!} ({!! date('d/m/Y H:m:i', strtotime($order->created_at ?? '')) !!})
                        </h2>
                        <table style="display: block;" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <thead>
                                <tr>
                                    <th style="padding:6px 9px 0px 9px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;font-weight:bold"
                                        width="50%" align="left">{{ __('Ecommerce::admin.info_payment') }}
                                    </th>
                                    <th style="padding:6px 9px 0px 9px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;font-weight:bold"
                                        width="50%" align="left">{{ __('Ecommerce::admin.address') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:3px 9px 9px 9px;border-top:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal"
                                        valign="top">
                                        <span
                                            style="text-transform:capitalize">{{ $order->customer->name ?? '' }}</span>
                                        <br> <a href="mailto:{{ $order->customer->email ?? '' }}"
                                            target="_blank">{{ $order->customer->email ?? '' }}</a>
                                        <br> {{ $order->customer->phone ?? '' }}
                                    </td>
                                    <td style="padding:3px 9px 9px 9px;border-top:0;border-left:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal"
                                        valign="top">
                                        {{ $order->customer->name ?? '' }}
                                        <br>
                                        {{ $order->customer->getAddress() ?? '' }}
                                        <br>
                                        {{ $order->customer->phone ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 9px 0px 9px;border-top:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444"
                                        colspan="2" valign="top">
                                        <p
                                            style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;font-weight:normal">
                                            @if (!empty($order->shipping ?? ''))
                                                <b>{{ __('Ecommerce::product.payment.shipping_method') }}:
                                                </b>{{ $order->shipping->service_name ?? '' }}
                                                <br>
                                                <b>{{ __('Ecommerce::product.payment.shipping_amount') }}:
                                                </b>{{ formatPrice($order->shipping->shipping_amount ?? 0, __('Ecommerce::product.payment.shipping_amount_after')) }}
                                            @endif
                                            <br>
                                            @if ($order->payment_id)
                                                <b>{{ __('Ecommerce::admin.payment_method') }}
                                                </b>{{ $order->load('payment')->payment?->payment_channel->label() }}
                                                <b>{{ __('Ecommerce::admin.payment_status') }}
                                                </b>{{ $order->load('payment')->payment?->status->label() }}
                                            @endif
                                            @if (!empty($order->customer->vat_company_name ?? ''))
                                                <br />
                                                <b>{{ __('Ecommerce::product.payment.vat_title') }}: </b>
                                                <br />
                                                <span>
                                                    {{ __('Ecommerce::product.payment.vat_company_name') }}:
                                                    {{ $order->customer->vat_company_name ?? '' }} <br>
                                                    {{ __('Ecommerce::product.payment.vat_email') }}:
                                                    {{ $order->customer->vat_email ?? '' }}<br>
                                                    {{ __('Ecommerce::product.payment.vat_tax_code') }}:
                                                    {{ $order->customer->vat_tax_code ?? '' }}<br>
                                                    {{ __('Ecommerce::product.payment.vat_address') }}:
                                                    {{ $order->customer->vat_address ?? '' }}
                                                </span>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr style="padding: 0 20px;display: block;">
                    <td style="display: block;width: 100%">
                        <h2
                            style="text-align:left;margin:10px 0;border-bottom:1px solid #ddd;padding-bottom:5px;font-size:13px;color:#078546">
                            {{ __('Ecommerce::admin.order_detail') }} #{!! $order->code !!}
                        </h2>
                        <table style="background:#f5f5f5" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px"
                                        bgcolor="#078546" align="left">{{ __('Ecommerce::admin.product') }}</th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px"
                                        bgcolor="#078546" align="left"> {{ __('Ecommerce::admin.unit_price') }}</th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px"
                                        bgcolor="#078546" align="left">{{ __('Ecommerce::admin.quantity') }}</th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px"
                                        bgcolor="#078546" align="right">{{ __('Ecommerce::admin.provisional_total') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px"
                                bgcolor="#eee">
                                @php
                                    $totalPrice = 0;
                                    $orderDetails = $order->orderDetail;
                                @endphp
                                @foreach ($orderDetails as $item)
                                    @php
                                        $price = $item->price ?? 0;
                                        $quantity = $item->quantity ?? 0;
                                        $provisionalPrice = $price * $quantity;
                                        $totalPrice = $totalPrice + $price * $quantity;
                                    @endphp
                                    <tr>
                                        <td></td>
                                        <td style="padding:3px 9px" valign="top" align="left">
                                            <b>{!! $item->product_name ?? '' !!}</b>
                                            @if (
                                                $item->product_type == DreamTeam\Ecommerce\Enums\ProductTypeEnum::DIGITAL &&
                                                    is_plugin_active('payment'))
                                                <p><a
                                                        href="{{ route('app.public.downloadDigitalProduct', $item->id) }}">{{ __('Ecommerce::product.digital_file_action') }}</a>
                                                </p>
                                            @endif
                                        </td>
                                        <td style="padding:3px 9px" valign="top" align="left">
                                            <span>{!! formatPrice($price) !!}</span></td>
                                        <td style="padding:3px 9px" valign="top" align="left">{{ $quantity }}
                                        </td>
                                        <td style="padding:3px 9px" valign="top" align="right">
                                            <span>{!! formatPrice($provisionalPrice ?? '') !!}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot
                                style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px">
                                @if (isset($order->voucher_value) && !empty($order->voucher_value))
                                    <tr>
                                        <td colspan="4" style="padding:5px 9px" align="right">
                                            {{ __('Ecommerce::admin.discount') }}: </td>
                                        <td style="padding:5px 9px" align="right"><span>{!! formatPrice($order->voucher_value) !!}</span>
                                        </td>
                                    </tr>
                                @endif
                                @if (!empty($order->shipping ?? ''))
                                    <tr>
                                        <td colspan="4" style="padding:5px 9px" align="right">
                                            {{ __('Ecommerce::product.payment.shipping_amount') }}</td>
                                        <td style="padding:5px 9px" align="right">
                                            <span>{{ formatPrice($order->shipping->shipping_amount ?? 0, __('Ecommerce::product.payment.shipping_amount_after')) }}</span>
                                        </td>
                                    </tr>
                                @endif
                                <tr bgcolor="#eee">
                                    <td colspan="4" style="padding:7px 9px" align="right">
                                        <b><big>{{ __('Ecommerce::admin.total_price') }}</big></b></td>
                                    <td style="padding:7px 9px" align="right">
                                        <b><big><span>{{ $order->getTotalPrice() }}</span></big></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <tr style="display: block;padding: 0 20px;">
                    <td>
                        <p>{{ __('Ecommerce::admin.contact_hotline') }} {{ $config_general['hotline'] ?? '' }}
                            {{ __('Ecommerce::admin.contact_support') }}</p>
                        <p>{{ env('APP_NAME', '') }}, {{ __('Ecommerce::admin.thanks') }}</p>
                    </td>
                <tr>
            </tbody>
        </table>
    </div>
</div>
