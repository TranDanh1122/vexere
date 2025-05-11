{{-- 
	@include('Form::base.multiImage', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'title_btn' 		=> $item['title_btn'],
    ])
--}}
@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
    <div class="mb-3 @if($has_row == true) row @endif">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
        @if($has_row == true) 
            <div class="col-md-10">
        @endif
        @php
            $attributes = $attributes ?? [];
            $allowThumb = Arr::get($attributes, 'allow_thumb', 'no');
            $allowWebp = Arr::get($attributes, 'allow_webp', 'no');
            $images = old($name)??$value;
            $options['wrapper'] = false;
            $showLabel = false;
            $showField = true;
            $showError = true;
            $nameKey = $name;
            if (($showLabel ?? '') && empty($options['label'] ?? '')) {
                $options['label'] = trans('Core::forms.image');
            }
            $inputName = $name . '[]';
        @endphp
        <x-Core::form.field
            :showLabel="$showLabel"
            :showField="$showField"
            :options="$options"
            :name="$name"
            :prepend="$prepend ?? null"
            :append="$append ?? null"
            :showError="$showError"
            :nameKey="$nameKey"
        >
            <x-Core::form.images
                :name="$inputName"
                :allow-thumb="$allowThumb"
                :allow-webp="$allowWebp"
                :images="$images"
            />
        </x-Core::form.field>
        @if($has_row == true) 
            </div> 
        @endif
    </div>

@if($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        $('#{{$name??''}}_box').sortable().disableSelection();
        $('#{{$name??''}}_box').bind("sortstart", function(event, ui) {
            ui.placeholder.css("visibility", "visible");
            ui.placeholder.css("border", "1px dotted #e3e3e3");
            ui.placeholder.css("background", "transparent");
        });
        @if ($required==1)
            $('body').on('click','button[type=submit]', function(e) {
                if($('input[name="{{$name??''}}[]"]').length == 0) {
                    e.preventDefault();
                    $('#{{$name??''}}_box').find('.error').remove();
                    $('#{{$name??''}}_box').append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')'));
                    openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
                }
            });
        @endif
    });
</script>