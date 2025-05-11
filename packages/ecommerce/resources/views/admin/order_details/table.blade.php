<td style="min-width: 100px; width: 100px;" class="center">
    <a href="{{ route('admin.orders.show', $value->order_id) }}">{{ $value->order->code ?? '' }}</a>
</td>
<td style="width: 200px; white-space: pre-line;"><a href="{{ route('admin.products.edit', $value->product_id) }}">{!! $value->product_name !!}
        @if(!empty($value->flash_sale_name))
            <span class="badge badge-primary p-1">{{ $value->flash_sale_name }}</span>
        @endif
    </a>
</td>
<td class="text-center" style="width: 120px;">{{ $value->quantity }}</td>
<td class="text-center" style="width: 120px;">{{ formatPrice($value->price) }}</td>
<td class="text-center" style="width: 120px;">{{ formatPrice($value->getTotalPrice()) }}</td>
<td style="width: 300px; white-space: inherit;">
    <p class="mb-1">{{ $value->order->customer->name ?? '' }}</p>
    <p class="mb-1"><strong><a href="tel:{{ $value->order?->customer?->phone ?? '' }}">{{ $value->order?->customer?->phone }}</a></strong></p>
    <p class="mb-1"><strong><a href="mailto:{{ $value->order?->customer?->email ?? '' }}">{{ $value->order?->customer?->email }}</a></strong></p>
    <p class="mb-1">{{ $value->order?->customer?->getAddress() }}</p>
</td>
<td style="width: 120px;">{{ ($value->order?->created_at) ? date('H:i d-m-Y', strtotime($value->order?->created_at)) : '' }}</td>
