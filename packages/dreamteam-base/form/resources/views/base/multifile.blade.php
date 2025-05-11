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
            $files = old($name)??$value;
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
            <x-Core::form.files
                :name="$inputName"
                :files="$files"
                :action="$attributes['action'] ?? 'attachment'"
            />
        </x-Core::form.field>
        @if($has_row == true) 
            </div> 
        @endif
    </div>

@if($class_col != '')
    </div>
@endif