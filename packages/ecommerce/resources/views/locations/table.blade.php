@include('Table::components.link',['text' => $value->name, 'url' => route('admin.locations.edit', $value->id), 'width'=>'auto'])
<td>{{ $value->parent->name ?? '' }}</td>
<td>{{ $value->from->toHtml() }}</td>