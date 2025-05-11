@include('Table::components.image', ['image' => $value->getAvatar()])
<td style="width: {!! $width ?? 'auto' !!};">
    {{ $value->name ?? '' }}
    @if ($value->status == \DreamTeam\Base\Enums\BaseStatusEnum::DRAFT)
        <span class="badge badge-secondary status-label ms-2">{{ __('Core::admin.general.draf') }}</span>
    @endif
</td>
@include('Table::components.text', ['text' => $value->email])
<td>
    @if ($value->is_supper_admin)
        {{ __('AdminUser::admin.supperAdmin') }}
    @else
        <a
            href="{{ route('admin.admin_user_roles.edit', $value->adminUserRole->id ?? 0) }}">{{ $value->adminUserRole?->name }}</a>
    @endif
</td>
@if (isset($isEnabled) && $isEnabled)
    <td class="form-switch form-switch-lg text-center" style="width: 120px;cursor: not-allowed;">
        <input type="checkbox" class="form-check-input" name="enabel_google2fa" value="1" disabled
            {{ $value->enabel_google2fa ? 'checked' : '' }} style="padding: 0;margin: 0;left: 0;">
    </td>
@endif
<td class="form-switch form-switch-lg text-center"
    style="width: 120px; {{ $value->id == Auth::guard('admin')->user()->id ? 'cursor: not-allowed;' : '' }}">
    <input type="checkbox" class="form-check-input" name="status" value="1"
        {{ $value->status == 1 ? 'checked' : '' }} style="padding: 0;margin: 0;left: 0;"
        {{ $value->id == Auth::guard('admin')->user()->id ? 'disabled' : '' }}>
</td>
