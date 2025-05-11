<div class="advanced-advistory" style="background: #ccc; padding: 15px; margin-left: 20px; border-radius: 6px; {{ EcommerceHelper::isAdvistoryEnabled() ? 'display:  block' : 'display:  none' }}">
	<div class="mb-3 row">
        <label for="advistory_type" class="col-lg-12 col-form-label">{{ __('Ecommerce::admin.advanced.advistory_type') }}</label>
        <div class="col-lg-12">
            <div class="form-radio" style="display: flex; gap: 25px;padding-left: 10px; align-items: center;justify-content: flex-start;">
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="advistory_type" id="advistory_type_popup" {{ get_ecommerce_setting('advistory_type', '') == 'popup' ? 'checked' : '' }} value="popup" style="font-size: 18px;">
                    <label style="padding-top: 4px;" class="form-check-label" for="advistory_type_popup">{{ __('Ecommerce::admin.advanced.advistory_popup') }}</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="advistory_type" id="advistory_type_base" {{ get_ecommerce_setting('advistory_type', '') == 'base' ? 'checked' : '' }} value="base" style="font-size: 18px;">
                    <label style="padding-top: 4px;" class="form-check-label" for="advistory_type_base">{{ __('Ecommerce::admin.advanced.advistory_base') }}</label>
                </div>
            </div>
    	</div>
	</div>
	@include('Form::base.text', [
		'name' => 'advistory_title_button',
		'value' => $data['advistory_title_button'] ?? '',
		'required' => 0,
		'label' => __('Ecommerce::admin.advanced.advistory_title_button'),
		'placeholder' => __('Ecommerce::admin.advanced.advistory_title_button'),
		'has_row' => false,
		'has_full' => true,
		'disable' => false,
		'class_col' => ''
	])
	<div class="mb-3 ">
        <label for="advistory_form_id">* {{ __('FormCustom::admin.select_form') }}</label>
        <select class="form-control form-select validate" name="advistory_form_id" id="form-id">
            <option value="">{{ __('FormCustom::admin.select_form') }}</option>
            @foreach ($forms as $value)
                <option value="{{ $value->id }}"
                    @if( ($data['advistory_form_id'] ?? 0) == $value->id)
                        selected
                    @endif
                >{{ $value->name }} - {{ generate_shortcode('form-custom', ['id' => $value->id]) }}</option>
            @endforeach
        </select>
    </div>
</div>
<script>
	$(document).ready(function(){
		$('body').on('change', 'input[name="advistory_enabled"]', function(){
			if($(this).prop('checked') && $(this).val() == 1) {
				$('.advanced-advistory').slideToggle()
			}else {
				$('.advanced-advistory').hide()
			}
		})
	})
</script>