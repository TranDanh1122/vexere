@switch($type)
    @case('show')
        @if (isset($data['userRoleActions'][$table_name . '_show']) && $data['userRoleActions'][$table_name . '_show'])
            <td class="text-center table-action" style="width: 60px;">
                <a href="{!! route('admin.' . $table_name . '.show', $value->id) !!}"><i class="fa fa-eye text-green"></i></a>
            </td>
        @endif
    @break

    @case('edit')
        @if (isset($data['userRoleActions'][$table_name . '_edit']) && $data['userRoleActions'][$table_name . '_edit'])
            <td class="text-center table-action" style="width: 60px;">
                <a href="{!! route('admin.' . $table_name . '.edit', $value->id) !!}"><i class="fas fa-edit text-green"></i></a>
            </td>
        @endif
    @break

    @case('delete')
        @if (isset($data['userRoleActions'][$table_name . '_delete']) && $data['userRoleActions'][$table_name . '_delete'])
            <td class="text-center table-action" style="width: 60px;">
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-quick_delete
                    data-message="@lang('Translate::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
            </td>
        @endif
    @break

    @case('action')
        <td class="text-center table-action" style="width: 100px;">
            @if (isset($data['userRoleActions'][$table_name . '_edit']) && $data['userRoleActions'][$table_name . '_edit'])
                <a href="{!! route('admin.' . $table_name . '.edit', $value->id) !!}" style="padding-right: 15px;"><i class="fas fa-edit text-green"></i></a>
            @endif
            @if (
                !Request()->trash &&
                    isset($data['userRoleActions'][$table_name . '_delete']) &&
                    $data['userRoleActions'][$table_name . '_delete']
            )
                <a href="javascript:;" class="delete-record" data-old="{{ $value->status }}" data-quick_delete
                    data-message="@lang('Translate::table.delete_question')" style="cursor: pointer;"><i class="fas fa-trash text-red"></i></a>
            @endif
            @if (Request()->trash &&
                    isset($data['userRoleActions'][$table_name . '_deleteForever']) &&
                    $data['userRoleActions'][$table_name . '_deleteForever']
            )
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-delete_forever
                    data-id="{{ $value->id }}" data-message="@lang('Translate::table.delete_forever_question')"><i class="fas fa-trash text-red"></i></a>
            @endif
        </td>
    @break

    @case('action_show')
        <td class="text-center table-action" style="width: 100px;">
            @if (isset($data['userRoleActions'][$table_name . '_show']) && $data['userRoleActions'][$table_name . '_show'])
                <a href="{!! route('admin.' . $table_name . '.show', $value->id) !!}" style="padding-right: 15px;"><i class="fa fa-eye text-green"></i></a>
            @endif
            @if (isset($data['userRoleActions'][$table_name . '_edit']) && $data['userRoleActions'][$table_name . '_edit'])
                <a href="{!! route('admin.' . $table_name . '.edit', $value->id) !!}" style="padding-right: 15px;"><i class="fas fa-edit text-green"></i></a>
            @endif
            @if (
                !Request()->trash &&
                    isset($data['userRoleActions'][$table_name . '_delete']) &&
                    $data['userRoleActions'][$table_name . '_delete']
            )
                <a href="javascript:;" class="delete-record" data-old="{{ $value->status }}" data-quick_delete
                    data-message="@lang('Translate::table.delete_question')" style="cursor: pointer;"><i class="fas fa-trash text-red"></i></a>
            @endif
            @if (Request()->trash &&
                    isset($data['userRoleActions'][$table_name . '_deleteForever']) &&
                    $data['userRoleActions'][$table_name . '_deleteForever']
            )
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-delete_forever
                    data-id="{{ $value->id }}" data-message="@lang('Translate::table.delete_forever_question')"><i class="fas fa-trash text-red"></i></a>
            @endif
        </td>
    @break

    @case('action_delete_custom')
        <td class="text-center table-action" style="width: 100px;">
            @if (isset($data['userRoleActions'][$table_name . '_edit']) && $data['userRoleActions'][$table_name . '_edit'])
                <a href="{!! route('admin.' . $table_name . '.edit', $value->id) !!}" style="padding-right: 15px;"><i class="fas fa-edit text-green"></i></a>
            @endif
            @if (
                !Request()->trash &&
                    isset($data['userRoleActions'][$table_name . '_delete']) &&
                    $data['userRoleActions'][$table_name . '_delete']
            )
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-delete_custom
                    data-message="@lang('Translate::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
            @endif
            @if (Request()->trash &&
                    isset($data['userRoleActions'][$table_name . '_deleteForever']) &&
                    $data['userRoleActions'][$table_name . '_deleteForever']
            )
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-delete_forever
                    data-id="{{ $value->id }}" data-message="@lang('Translate::table.delete_forever_question')"><i class="fas fa-trash text-red"></i></a>
            @endif
        </td>
    @break

    @case('delete_custom')
        @if (isset($data['userRoleActions'][$table_name . '_delete']) && $data['userRoleActions'][$table_name . '_delete'])
            <td class="text-center table-action" style="width: 60px;">
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-delete_custom
                    data-message="@lang('Translate::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
            </td>
        @endif
    @break

    @case('restore')
        @if (isset($data['userRoleActions'][$table_name . '_restore']) && $data['userRoleActions'][$table_name . '_restore'])
            <td class="text-center" style="width: 60px;">
                <a class="delete-record" href="javascript:;" data-old="{{ $value->status }}" data-quick_restore><i
                        class="fas fa-window-restore text-green"></i></a>
            </td>
        @endif
    @break

@endswitch
@if ($table_name == 'admin_users')
    <td class="text-center table-action" style="width: 60px;">
        @if ($value->id == Auth::guard('admin')->user()->id)
            <a class="delete-record delete_admin_user" style="cursor: not-allowed;"><i
                    class="fas fa-trash text-red"></i></a>
        @else
            <a class="delete-record delete_admin_user" href="#delete_user_{{ $value->id }}" data-bs-toggle="modal"
                data-message="@lang('Translate::table.delete_question')"><i class="fas fa-trash text-red"></i></a>
            <div class="modal fade" id="delete_user_{{ $value->id }}">
                <div class="modal-dialog">
                    <form action="" method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">@lang('Core::admin.delete_users')</h4>
                                <button type="button" class="close badge badge-danger" style="border: 0"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $admin_users = DB::table('admin_users')
                                        ->where('status', 1)
                                        ->where('id', '<>', $value->id)
                                        ->get();
                                @endphp
                                <div class="mb-3">
                                    <label for="list_admin"
                                        style="text-align: left; width: 100%">{{ __('Core::admin.select_auth_replace') }}</label>
                                    <select name="admin_users_list" id="list_admin"
                                        class="form-control input-sm form-select">
                                        @if (isset($admin_users) && count($admin_users) > 0)
                                            @foreach ($admin_users as $admin_item)
                                                <option value="{{ $admin_item->id }}">{!! $admin_item->display_name ?? $admin_item->name !!}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <input type="hidden" name="user_id" value="{{ $value->id }}">
                                <button type="button" class="btn btn-default btn-sm"
                                    data-bs-dismiss="modal">@lang('AdminUser::admin.login.close')</button>
                                <button type="submit" class="btn btn-primary btn-sm" data-delete_admin_user
                                    data-message="@lang('Translate::table.delete_question')">@lang('Core::admin.general.delete')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </td>
@endif
