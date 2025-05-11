<div class="col-lg-12">
	<div class="card w-100" id="filters">
		<div class="" data-card-widget="collapse">
			<div class="form-title mb-0">{{ __('Ecommerce::admin.filter') }}</div>
		</div>
		<div class="card-body" style="padding: 5px 0 0 0 !important;"></div>
	</div>
</div>

<script>
	$(document).ready(function() {
		product_id = '{{$product_id ?? ''}}';
		// Dữ liệu
		data = {
			product_id: product_id
		};
		url = '{{ route('admin.products.filters') }}';
		loadAjaxPost(url, data, {
			beforeSend: function(){
				$('#filters').addClass('loading');
			},
			success:function(result){
				$('#filters').removeClass('loading');
				if (result.status == 1) {
					$('#filters').find('.card-body').html(result.html);
				} else {
					alertText(result.message, 'error');
				}
			},
			error: function (error) {
				$('#filters').removeClass('loading');
			}
		}, 'custom');
	});
</script>
