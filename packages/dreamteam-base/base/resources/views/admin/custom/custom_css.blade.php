<div class="mb-3  row ">
    <label for="{{ $name }}" class="col-md-12 col-form-label">{{ $label }}</label>
    <div class="col-md-12">
        <textarea class="form-control" autocomplete="off" name="{{ $name }}" id="{{ $name }}" placeholder="" rows="5">{!! $data ?? '' !!}</textarea>
    </div>
</div>
