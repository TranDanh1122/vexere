@include('Table::components.name',[
    'width' => '300px',
    'name' => $value->getName(),
    'route' => route('admin.products.edit', $value->id),
    'duplicate' => route('admin.products.create', ['duplicateID' => $value->id]),
])
<td style="width: 150px;">
    @if($value->brand_id)
        <a href="{{ route('admin.brands.edit', $value->brand_id) }}"  target="_blank" style="padding: 2px 5px;margin-right: 2px; max-width: 150px;">{{ $value->brand->name ?? '' }}</a>
    @else
        ----
    @endif
</td>
@include('Table::components.publish-time')
