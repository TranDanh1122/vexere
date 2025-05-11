@if (isset($data['table_simple']) && $data['table_simple'] == true)
	@include('Category::listdata_simple')
@elseif (isset(Request()->trash) && Request()->trash == true)
	@include('Table::listdata', ['module_name' => __('Translate::table.delete_temp').': '.__($module_name)])
@else
	@include('Table::listdata')
@endif