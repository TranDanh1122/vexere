@include('Table::components.name',[
	'width' => '600px',
	'name' => $value->getName(),
	'view' => $value->getUrl(),
	'route' => route('admin.pages.edit', $value->id),
	'hasAdditionName' => true,
])
@include('Table::components.publish-time')
@if(defined('FILTER_RENDER_SEO_DETAIL_COLUMN') && !\Request()->trash)
	{!! apply_filters(FILTER_RENDER_SEO_DETAIL_COLUMN, null, 'pages', $value) !!}
@endif