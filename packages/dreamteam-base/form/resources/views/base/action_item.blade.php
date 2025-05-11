{{-- 
	Bổ sung các submit, link cho phần submit tại form
		$form->action('custom', 'link_preview', 'link_exit', [
	        [ 
	            'type' => 'submit', 
	            'label' => __('Translate::form.action.save'),
	            'btn_type' => 'success',
	            'icon' => 'fas fa-save',
	            'value' => 'edit',
	        ],
	        [ 
	            'type' => 'link',
	            'label' => __('Translate::form.action.save'),
	            'btn_type' => 'danger',
	            'icon' => '',
	            'link' => '#',
	        ],
	    ]);
--}}
@if (isset($custom) && !empty($custom))
	@foreach (json_decode(json_encode($custom)) as $custom)
		@switch($custom->type)
		    @case('submit') 
				<button 
					type="submit" 
					name="redirect" 
					value="{{ $custom->value ?? '' }}" 
					class="btn btn-sm btn-{{ $custom->btn_type ?? '' }}"
					@if (isset($custom->formaction)) formaction="{{ $custom->formaction ?? '' }}" @endif
				>
	    			<i class="{{ $custom->icon ?? '' }} mr-1"></i> 
	    			@lang($custom->label ?? '')
	    		</button>
		    @break
		    @case('link')
	    		<a href="{{ $custom->link ?? '' }}" class="btn btn-sm btn-{{ $custom->btn_type ?? '' }}">
					<i class="{{ $custom->icon ?? '' }} mr-1"></i> 
					@lang($custom->label ?? '')
				</a>
		    @break
		@endswitch
	@endforeach
@endif