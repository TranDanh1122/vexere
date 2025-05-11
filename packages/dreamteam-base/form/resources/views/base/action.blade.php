{{-- 
	@include('Form::base.action', [
    	'type'				=> $item['type'],
    	'preview'			=> $item['preview'],
    	'exit_url'			=> $item['exit_url'],
    ])
--}}
<div class="form-actions">
	<div class="form-actions__group">
		@switch($type)
		    @case('add')
	    		<button type="submit" name="redirect" value="edit" class="btn btn-sm btn-primary">
	    			<i class="fas fa-save mr-1"></i> 
	    			@lang('Translate::form.action.create')
	    		</button>
                <button type="submit" name="redirect" value="exit" class="btn btn-sm btn-success">
                    <i class="fas fa-save mr-1"></i>
                    @lang('Translate::form.action.create_and_exit')
                </button>
				{{-- <button type="submit" name="redirect" value="save" class="btn btn-sm btn-warning">
					<i class="fas fa-share-square mr-1"></i> 
					@lang('Translate::form.action.save_draft')
				</button> --}}
				@if(isset($preview) && $preview != '')
				<a href="{{ $preview }}" target="_blank" class="btn btn-sm btn-primary">
					<i class="fa fa-eye mr-1"></i> 
					@lang('Translate::form.action.show')
				</a>
				@endif
				@include('Form::base.action_item')
				<a href="{{ (!empty($exit_url)) ? $exit_url : route('admin.'.$table_name.'.index') }}" class="btn btn-sm btn-danger">
					<i class="fa fa-sign-out-alt mr-1"></i>
					@lang('Translate::form.action.exit')
				</a>
		    @break

		    @case('edit')
	    		<button type="submit" name="redirect" value="edit" class="btn btn-sm btn-primary">
	    			<i class="fas fa-save mr-1"></i> 
	    			@lang('Translate::form.action.save')
	    		</button>
				<button type="submit" name="redirect" value="index" class="btn btn-sm btn-info">
					<i class="fas fa-share-square mr-1"></i> 
					@lang('Translate::form.action.save_and_exit')
				</button>
				@if(isset($duplicate) && $duplicate != '' )
					<a href="{{ route('admin.'.$table_name.'.create', ['duplicateID' => $duplicate]) }}" target="_blank" class="btn btn-sm btn-success">
						<i class="fa fa-copy mr-1"></i> 
						@lang('Translate::form.action.duplicate')
					</a>
				@endif
				@if(isset($preview) && $preview != '')
					<a href="{{ $preview }}" target="_blank" class="btn btn-sm btn-primary">
						<i class="fa fa-eye mr-1"></i> 
						@lang('Translate::form.action.show')
					</a>
				@endif
				@include('Form::base.action_item')
				<a href="{{ (!empty($exit_url)) ? $exit_url : route('admin.'.$table_name.'.index') }}" class="btn btn-sm btn-danger">
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
				<a href="{{ $preview }}" target="_blank" class="btn btn-sm btn-secondary">
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
