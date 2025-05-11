<tr data-id="{{ $item->id }}">
    <td>
        <a
            href="javascript:void(0);"
            class="gap-2 edit-language-button d-flex align-items-center text-decoration-none"
            data-id="{{ $item->id }}"
            data-url="{{ route('admin.languages.get', ['lang_id' => $item->id]) }}"
            data-bs-original-title="{{ trans('Translate::language.edit_tooltip') }}"
            data-bs-toggle="tooltip"
        >
            {!! languageFlag($item->flag, $item->name) !!}
            {{ $item->name }}
        </a>
    </td>
    <td class="form-switch form-switch-lg text-center">
        <a
            href="javascript:void(0);"
            data-section="{{ route('admin.languages.changeStatus') }}?lang_id={{ $item->id }}"
            class="text-decoration-none change-status"
            data-bs-toggle="tooltip"
            data-bs-original-title="{{ trans('Translate::language.toggle_active', ['language' => $item->name]) }}"
        >
            @if ($item->status)
                <input data-id="{{ $item->id }}"
                data-name="{{ $item->name }}" type="checkbox" class="form-check-input" name="status" value="1" checked="" style="padding: 0;margin: 0;left: 0;">
            @else
                <input data-id="{{ $item->id }}"
                data-name="{{ $item->name }}" type="checkbox" class="form-check-input" name="status" value="1"  style="padding: 0;margin: 0;left: 0;">
            @endif
        </a>
    </td>
    <td>{{ $item->order }}</td>
    <td>
        <x-Core::button
            color="primary"
            icon="ti ti-edit"
            :icon-only="true"
            :data-id="$item->id"
            :data-url="route('admin.languages.get', ['lang_id' => $item->id])"
            :tooltip="trans('Translate::language.edit_tooltip')"
            size="sm"
            class="edit-language-button"
        />
    </td>
</tr>
