{{-- 
	@include('Form::base.actionInline', [
    	'type'				=> $item['type'],
    ])
--}}
<div class="form-actions-inline">
	<div class="form-actions__group float-left">
		@switch($type)
		    @case('add')
	    		<button type="submit" name="redirect" value="create" class="btn btn-sm btn-primary">
	    			<i class="fas fa-save mr-1"></i> 
	    			@lang('Translate::form.action.create')
	    		</button>
				<a href="/admin" class="btn btn-sm btn-danger">
					<i class="fa fa-sign-out-alt mr-1"></i>
					@lang('Translate::form.action.exit')
				</a>
		    @break

		    @case('edit')
	    		<button type="submit" name="redirect" value="edit" class="btn btn-sm btn-primary">
	    			<i class="fas fa-save mr-1"></i> 
	    			@lang('Translate::form.action.save')
	    		</button>
				<a href="/admin" class="btn btn-sm btn-danger">
					<i class="fa fa-sign-out-alt mr-1"></i> 
					@lang('Translate::form.action.exit')
				</a>
		    @break

		    @case('editconfig')
	    		<button type="submit" name="redirect" value="edit" class="btn btn-sm btn-primary">
	    			<i class="fas fa-save mr-1"></i> 
	    			@lang('Translate::form.action.save_setting')
	    		</button>
				@if(isset($preview) && $preview != '')
				<a href="{{ $preview }}" target="_blank" class="btn btn-sm btn-primary">
					<i class="fa fa-eye mr-1"></i> 
					@lang('Translate::form.action.show')
				</a>
				@endif
				@include('Form::base.action_item')
		    @break

		    @default
				@include('Form::base.action_item')
		    @break
		@endswitch
	</div>
</div>
