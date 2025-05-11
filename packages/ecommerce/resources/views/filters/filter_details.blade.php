<div class="col-lg-9">
	<div class="card listdata" id="listdata">
		<div class="card-header" style="padding: .75rem 1.25rem" data-card-widget="collapse">
			<div class="form-title">{{ __('Ecommerce::admin.filter') }}</div>
		</div>
		<div class="card-body table-responsive p-0">
			<table class="table table-striped table-bordered table-head-fixed">
				<thead>
					<tr>
						<th class="text-center pl-3" style="width: 50px;">{{ __('Ecommerce::admin.numerical_order') }}</th>
						<th class="text-center pl-3">{{ __('Core::admin.general.title') }}</th>
						<th class="text-center pl-3">{{ __('Ecommerce::admin.sort') }}</th>
						<th class="text-center pl-3" style="width: 100px;">{{ __('Ecommerce::admin.status') }}</th>
						<th class="text-center pl-3">{{ __('Ecommerce::admin.delete') }}</th>
					</tr>
				</thead>
				<tbody>
					@if (isset($filterDetails) && count($filterDetails) > 0)
						@foreach ($filterDetails as $key => $value)
							<tr data-table="filter_details" data-id="{!! $value->id !!}">
								<td class="text-center">{{$key+1}}</td>
								@include('Table::components.edit_text', [ 'width' => 'auto', 'name' => 'name' ])
								@include('Table::components.edit_text', [ 'width' => '100px', 'name' => 'order' ])
								@include('Table::components.edit_array',[
									'name' => 'status',
									'value' => $value->status, 
									'options' => $statusEnum
								])
								<td class="text-center table-action">
									@if(Request()->trash)
										<a class="delete-record" href="javascript:;" data-delete_filter_forever data-id="{!! $value->id !!}" data-message="@lang('Core::admin.form_delete_confirm')"><i class="fas fa-trash text-red"></i></a>
									@else
						            	<a class="delete-record" href="javascript:;" data-delete_filter data-message="@lang('Translate::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
						            @endif
						        </td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="5" class="text-center">@lang('Translate::table.no_record')</td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>
		@if (isset($filter_id) && !empty($filter_id))
			<div class="card-footer clearfix">
				<div class="float-left footer-action">
					@if(Request()->trash)
						<a href="{{ route('admin.filters.edit', $filter_id) }}" class="btn btn-sm btn-info">@lang('Translate::table.list_url')</a>
					@else	
						<a href="?trash=true" class="btn btn-sm btn-info" style="color: #fff;background: #f1b44c;border: none;padding: 5px 20px;">@lang('Translate::table.trash_url')</a>
					@endif	
				</div>
			</div>
		@endif
	</div>
	<script>
		$(document).ready(function() {
			// Xóa nhanh chi tiết thuộc tính
			$('body').on('click', '*[data-delete_filter]', function(e) {
				e.preventDefault();
				e = $(this);
				// Bảng
				table = $(this).closest('*[data-table]').data('table');
				// id
				id = $(this).closest('*[data-id]').data('id');
				// mảng id_array
				id_array = [];
				id_array.push(id);
				// Chuẩn hóa data
				data = {
					table 		: table,
					id_array 	: id_array,
				};
				if (confirm( $(this).data('message') )) {
					loadAjaxPost('/{{config('app.admin_dir')}}/ajax/quick_delete', data, {
						beforeSend: function(){},
				        success:function(result){
				        	if (result.status == 1) {
				        		alertText(result.message);
				        		e.closest('tr').fadeOut(function() {
				        			$(this).remove();
				        			if (e.closest('tbody').find('tr').length == 0) {
				        				$('.listdata').find('tbody').append(`
											<tr>
												<td colspan="7" class="text-center">@lang('Translate::table.no_record')</td>
											</tr>
				        				`);
				        			}
				        		});
				        	} else {
				        		alertText(result.message, 'warning');
				        	}
				        },
				        error: function (error) {}
					});
				}
			});
			$('body').on('click', '*[data-delete_filter_forever]', function(e) {
				e.preventDefault();
				const _this = $(this);
				// Bảng
				const table = $(this).closest('*[data-table]').data('table');
				const id = _this.data('id')
				// Chuẩn hóa data
				data = {
					_method: 'DELETE',
					id 		: table,
					table 	: id,
				};
				if (confirm( $(this).data('message') )) {
					loadAjaxPost('/{{config('app.admin_dir')}}/filter_details/delete-forever/'+id, data, {
						beforeSend: function(){},
				        success:function(result){
				        	if (result.status == 1) {
				        		alertText(result.message);
				        		_this.closest('tr').fadeOut(function() {
				        			$(this).remove();
				        			if (_this.closest('tbody').find('tr').length == 0) {
				        				$('.listdata').find('tbody').append(`
											<tr>
												<td colspan="7" class="text-center">@lang('Translate::table.no_record')</td>
											</tr>
				        				`);
				        			}
				        		});
				        	} else {
				        		alertText(result.message, 'warning');
				        	}
				        },
				        error: function (error) {}
					});
				}
			});
		})
	</script>
</div>
