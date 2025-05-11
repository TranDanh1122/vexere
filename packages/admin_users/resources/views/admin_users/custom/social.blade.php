@php
	if(isset($data_edit)){
		$social = json_decode($data_edit->social, 1);
	}
@endphp
<div class="mb-3 ">
    <label for="address" style="width: 100%;float: left;">{{__('AdminUser::admin.link_internet')}}</label>
    <button type="button" class="btn btn-sm btn-success" style="margin: 5px 0 5px;" id="add_link">{{__('AdminUser::admin.add_link_internet')}}</button>
    <div class="row" id="result_link">
    	@if(isset($social['social_name']) && is_array($social['social_name']))
    	@foreach($social['social_name'] as $key => $val)
    	<div class="row link_item">
			<div class="col-lg-5">
			    <div class="mb-3 ">
			        <label>{{__('AdminUser::admin.title_link_internet')}}</label>
	                <input type="text" class="form-control" autocomplete="off" name="social_name[]" placeholder="" value="{!! $val !!}">
	            </div>
	    	</div>       		        
		    <div class="col-lg-5">
			    <div class="mb-3 ">
			        <label>{{__('Core::admin.general.slug')}}</label>
			        <input type="text" class="form-control" autocomplete="off" name="social_link[]" placeholder="" value="{!! $social['social_link'][$key] !!}">
			    </div>
		    </div>
		    <div class="col-lg-2">
		    	<div class="mb-3 ">
			        <label style="opacity: 0;">a</label>
		    		<button type="button" class="btn btn-sm btn-danger remove_link" style="width: 100%;float: left;padding: 8px 10px;"> Xóa</button>
		    	</div>
		    </div>
    	</div>
    	@endforeach
    	@endif
	</div>
</div>

<script>
	$(document).ready(function(){

        $(".mb-3").on('click', '#add_link', function(event) {
        	event.preventDefault();
			var link_item = 
			'<div class="row link_item">' + 
			'<div class="col-lg-5">' + 
			'<div class="mb-3 ">' + 
			'<label>{{__('AdminUser::admin.title_link_internet')}}</label>'+
			'<input type="text" class="form-control" autocomplete="off" name="social_name[]" placeholder="" value="">'+
			'</div>'+
			'</div>'+
			'<div class="col-lg-5">' + 
			'<div class="mb-3 ">' + 
			'<label>{{__('AdminUser::admin.title_link_internet')}}</label>'+
			'<input type="text" class="form-control" autocomplete="off" name="social_link[]" placeholder="" value="">'+
			'</div>'+
			'</div>'+
			'<div class="col-lg-2">'+
			'<div class="mb-3 ">'+
			'<label style="opacity: 0;">a</label>'+
			'<button type="button" class="btn btn-sm btn-danger remove_link" style="width: 100%;float: left;padding: 8px 10px;"> Xóa</button>'+
			'</div>'+
			'</div>'+
			'</div>';
			$(this).parents('.mb-3').find("#result_link").append(link_item);
        });
        $("#result_link").on('click', '.remove_link', function(event) {
            event.preventDefault();
            $(this).parents('.link_item').remove();
        });
	});
</script>