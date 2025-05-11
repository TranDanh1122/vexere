<a href="#import-sync-links" style="height: 30px; margin-left: .3rem;" data-order_delivery data-bs-toggle="modal" class="btn-sm btn btn-info">
<i class="fas fa-upload"></i>&nbsp{{ __('SyncLink::admin.import.name') }}</a>
<div class="modal fade" id="import-sync-links">
	<div class="modal-dialog">
		{{-- {{ route('admin.ajax.sync_links.quick_create') }} --}}
		<form action="" method="POST">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">@lang('Import') @lang('SyncLink::admin.name')</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="cursor: pointer;border: 0;width: 30px;height: 30px;border-radius: 50%;padding: 0;">
					<span aria-hidden="true">×</span>
				</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="file" name="file_sync_links" class="form-control" style="padding: 3px; margin-bottom: 5px; width: 100%;">
						<span class="helper">@lang('SyncLink::admin.import.file')</span>
						<span class="helper">@lang('SyncLink::admin.import.col1')</span>
						<span class="helper">@lang('SyncLink::admin.import.col2')</span>
						<span class="helper">@lang('SyncLink::admin.import.col3')</span>
						<a href="/vendor/core/core/sync-link/sync_link_example.xlsx" target="_blank">{{ __('SyncLink::admin.import.download') }}</a>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">@lang('SyncLink::admin.import.close')</button>
					<button type="submit" class="btn btn-primary btn-sm" data-import_sync_links>@lang('SyncLink::admin.import.import_name')</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('body').on('click', '*[data-dismiss]', function(e) {
			e.preventDefault();
			$('#import-sync-links').modal('hide');
		})
		$('body').on('click', '*[data-import_sync_links]', function(e) {
			e.preventDefault();
			file = $('#import-sync-links input[name=file_sync_links]').prop("files");
			if (file.length == 0) {
				alertText('{{ __('SyncLink::admin.import.file_require') }}', 'warning');
			} else {
				
				var form_data = new FormData();
				form_data.append('files', file[0]);
				url = '{{ route('admin.ajax.sync_links.import') }}';
				$.ajax({
			        headers: {
			            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			        },
			        type: 'POST',
			        url: url,
			        data: form_data,
			        enctype: 'multipart/form-data',
			        processData: false,
			        contentType: false,
			        beforeSend: function(){
			        	alertText('{{ __('Widget::widget.onUpdate') }}', 'warning')
			        	loadingBox('open');
			            activeProgress(0);
			        },
			        success:function(result){
			            activeProgress(99, 'close');
			            if (result.status == 1) {
			            	$('#import-sync-links input[name=file_sync_links]').val('');
				        	$('#import-sync-links .close').click();
				        	loadData('no_animate');
			        		alertText(result.message);
			        	} else {
			        		alertText(result.message, 'error');
			        	}
			        	loadingBox('close');
			        },
			        error: function (error) {
			            activeProgress(99, 'close');
			        	loadingBox('close');
			            alertText('@lang('Translate::admin.ajax_fail')', 'error')
			        }
			    })
			}
		})
	})
</script>