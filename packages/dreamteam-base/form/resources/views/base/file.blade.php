<div class="image-box attachment-wrapper">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="attachment-url">
    <div class="attachment-info">
        <a href="{{ $url ?? $value }}" target="_blank">{{ $value }}</a>
    </div>
    <div class="image-box-actions">
        <a href="#" class="@if (is_in_admin(true) && auth()->guard('admin')->check()) btn_gallery @else media-select-file @endif" data-result="{{ $name }}" data-action="{{ $attributes['action'] ?? 'attachment' }}">
            {{ trans('Core::forms.choose_file') }}
        </a> |
        <a href="#" class="text-danger btn_remove_attachment">
            {{ trans('Core::forms.remove_file') }}
        </a>
    </div>
</div>
