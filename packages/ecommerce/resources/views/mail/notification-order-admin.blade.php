<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div class="container">
        <h3>{{ __('Ecommerce::admin.info_order') }}</h3>
        <p style="line-height: 22px;"><span style="font-weight: bold;">{{ __('Ecommerce::admin.name') }}:</span>{{ $order->customer->name ?? '' }}</p>
        {{-- <p style="line-height: 22px;"><span style="font-weight: bold;">{{ __('Ecommerce::admin.email') }}:</span>{{ $order->customer->email ?? '' }}</p> --}}
        <p style="line-height: 22px;"><span style="font-weight: bold;">{{ __('Ecommerce::admin.phone') }}:</span>{{ $order->customer->phone ?? '' }}</p>
        {{-- <p style="line-height: 22px;"><span style="font-weight: bold;">{{ __('Ecommerce::admin.address') }}:</span>{{ $order->customer->getAddress() ?? '' }}</p> --}}
        <p style="line-height: 22px;"><span style="font-weight: bold;">{{ __('Ecommerce::admin.total_price') }}:</span>{{ $order->getTotalPrice() }}</p>
        <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:3px;padding: 15px 0; margin-bottom: 15px;">
            <tbody>
                <tr style="padding: 0 20px;display: block;">
                    <td style="display: block;width: 100%">
                        <h2 style="text-align:left;margin:10px 0;border-bottom:1px solid #ddd;padding-bottom:5px;font-size:13px;color:#078546">{{ __('Ecommerce::admin.order_detail') }} #{!! $order->code !!}
                        </h2>      
                        <table style="background:#f5f5f5" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px" bgcolor="#078546" align="left">{{ __('Ecommerce::admin.product') }}</th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px" bgcolor="#078546" align="left"> {{ __('Ecommerce::admin.unit_price') }}</th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px" bgcolor="#078546" align="left">{{ __('Ecommerce::admin.quantity') }}</th>
                                    <th style="padding:6px 9px;color:#fff;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px" bgcolor="#078546" align="right">{{ __('Ecommerce::admin.provisional_total') }}</th>
                                </tr>
                            </thead>

                            <tbody style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px" bgcolor="#eee">
                                @php
                                    $totalPrice = 0;
                                    $orderDetails = $order->orderDetail;
                                @endphp
                                @foreach ($orderDetails as $item)
                                    @php
                                        $price = $item->price ?? 0;
                                        $quantity = $item->quantity ?? 0;
                                        $provisionalPrice = $price*$quantity;
                                        $totalPrice = $totalPrice+($price*$quantity);
                                        $product = \DreamTeam\Ecommerce\Models\Product::find($item->product_id)
                                    @endphp                                
                                    <tr>
                                        <td></td>
                                        <td style="padding:3px 9px" valign="top" align="left">
                                            @if($product)
                                                <b><a href="{{ $product->getUrl() }}">{!!$item->product_name ?? ''!!}</a></b>
                                            @else
                                                <b>{!!$item->product_name ?? ''!!}</b>
                                            @endif
                                        </td>                    
                                        <td style="padding:3px 9px" valign="top" align="left"><span>{!!formatPrice($price)!!}</span></td>
                                        <td style="padding:3px 9px" valign="top" align="left">{{ $quantity }}</td>
                                        <td style="padding:3px 9px" valign="top" align="right">
                                            <span>{!! formatPrice($provisionalPrice ?? '') !!}</span>
                                        </td>
                                    </tr> 
                                @endforeach   
                            </tbody>  
                            <tfoot style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px">
                                <tr bgcolor="#eee">
                                    <td colspan="4" style="padding:7px 9px" align="right"><b><big>{{ __('Ecommerce::admin.total_price') }}</big></b></td>
                                    <td style="padding:7px 9px" align="right"><b><big><span>{{ $order->getTotalPrice() }}</span></big></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <p style="line-height: 22px;"><span style="font-weight: bold;">{{ __('Ecommerce::admin.show_detail') }}: <b><a href="{{ route('admin.orders.show', $order->id) }}" target="_blank">{{ __('Ecommerce::admin.here') }}</a></p>
    </div>
</body>
</html>
