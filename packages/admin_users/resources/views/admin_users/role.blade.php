{{-- 
	@include('Core::admin_users.role', [
        'user_id' => \Auth::guard('admin')->user()->id,
    ]);
--}}
@php
    $modules = config('DreamTeamModule.modules');
    if (isset($data_edit)) {
        $capabilities = json_decode($data_edit->$roleName, 1);
    }
@endphp
<style>
    .has-open__role {
        cursor: pointer;
        position: relative;
    }

    .has-open__role:after {
        position: absolute;
        right: 20px;
        top: 10px;
        content: '';
        width: 15px;
        height: 15px;
        border: 1px solid;
        border-width: 0 2px 2px 0;
        border-color: #ccc;
        transform: rotate(-135deg);
        transition: .5s;
    }

    .has-open__role.open:after {
        transform: rotate(45deg);
    }
</style>
<div class="mb3 row">
    @if (!isset($type) || $type != 'users')
        <div class="box-header">
            <h4 for="{!! $name ?? '' !!}" class="col-lg-12 col-md-12">{{ __('Translate::admin.role.name') }}</h4>
            <p class="col-lg-12 col-md-12" style="color: #a2a2a2;font-size: 12px;">
                {{ __('AdminUser::admin.roles.select_permission', compact('name')) }}</p>
        </div>
    @endif
    <div class="role">
        <div class="col-lg-12 col-md-12 role-content" style="width: 100%;">
            <div class="role-head mb-2">
                <div class="role-head__title">{{ __('AdminUser::admin.roles.feature') }}
                </div>
                @if (!isset($type) || $type != 'users')
                    <div class="role-head__title" style="margin-left: 8px;"><input id="check_all_permission"
                            type="checkbox" class="form-check-input" data-select_all><label style="margin-left: 4px"
                            for="check_all_permission">{{ __('Core::admin.general.select_all') }}</label>
                    </div>
                @endif
            </div>
            <div class="role-body">
                @foreach ($modules as $key => $module)
                    @php
                        $permision_all = [];
                        $array_type = [];
                        foreach ($module['permision'] as $value) {
                            $array_type[$value['type']] = $value['type'];
                            $permision_all[$value['type']] = $value['name'];
                        }
                    @endphp
                    <div class="role-item" style="float: none; display: flex;">
                        <div class="role-item__title" style="flex: 0 0 150px; padding-left: .5rem">
                            {!! __($module['name']) !!}</div>
                        <div class="role-item__list" style="">
                            @if (!isset($type) || $type != 'users')
                                <div class="role-item__permission"
                                    style="width: auto; margin-left: 15px; margin-bottom: 15px; display: inline-flex;align-items: center;">
                                    <input type="checkbox" class="form-check-input" name="role[]" data-select_role
                                        id="{{ $key . '_all' }}"><label style="margin-bottom: 0;margin-left: 4px"
                                        for="{{ $key . '_all' }}">{{ __('AdminUser::admin.roles.all') }}</label>
                                </div>
                            @endif
                            @foreach ($permision_all as $k => $val)
                                <div class="role-item__permission item-permisions"
                                    style="width: auto; margin-left: 15px; margin-bottom: 15px; display: inline-flex;align-items: center;">
                                    <input type="checkbox" name="role[]"
                                        @if (!in_array($k, $array_type)) disabled style="cursor: no-drop;background: #2a3042;" @endif
                                        value="{!! str_replace($key . '_settings', 'settings', $key . '_' . $k) !!}" class="form-check-input"
                                        @if (isset($capabilities) && in_array(str_replace($key . '_settings', 'settings', $key . '_' . $k), $capabilities)) checked @endif id="{{ $key . '_' . $k }}"
                                        style="margin-top: 0">
                                    <label for="{{ $key . '_' . $k }}"
                                        style="margin-bottom: 0;margin-left: 4px">{{ __($val) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            select_all = false;
            $('body').on('click', '*[data-select_all]', function(e) {
                if (select_all == false) {
                    select_all = true;
                    $('input[name="role[]"]').prop('checked', true);
                } else {
                    select_all = false;
                    $('input[name="role[]"]').prop('checked', false);
                }
            });

            select_role = false;
            $('body').on('click', '*[data-select_role]', function(e) {
                if (select_role == false) {
                    select_role = true;
                    $(this).closest('.role-item').find('input[name="role[]"]').prop('checked', true);
                } else {
                    select_role = false;
                    $(this).closest('.role-item').find('input[name="role[]"]').prop('checked', false);
                }
            });
            $('body').on('click', '.has-open__role .box-header', function(e) {
                $(this).parent().find('.role').slideToggle()
                $(this).parent().toggleClass('open')
            });
            @if (isset($required) && $required)
                $('body').on('click', '.form-actions__group button[type=submit]', function(e) {
                    let checked = []
                    $('.role-item').find('input[name="role[]"]').each(function() {
                        if ($(this).prop('checked')) {
                            checked.push($(this).val())
                        }
                    })
                    if (!checked.length) {
                        alertText('{{ __('AdminUser::admin.roles.select_permission', compact('name')) }}',
                            'error')
                        e.preventDefault()
                    }
                });
            @endif
            @if (isset($type) && $type == 'users')
                $('body').on('change', 'select[name="admin_user_role_id"]', function(e) {
                    $('.role-item input[type="checkbox"]').removeAttr('style')
                    $('.role-item input[type="checkbox"]').removeAttr('disabled')
                    $('.role-item input[type="checkbox"]').removeAttr('checked')
                    const adminUserRoleId = $(this).val()
                    if (adminUserRoleId == 'spper_admin') {
                        $(`.role-body input[type="checkbox"]`).css({
                            'background-color': '#556ee6',
                            'border-color': '#556ee6'
                        }).attr('disabled', true).attr('checked', true)
                    } else {
                        let checkRole = ''
                        let listRoles = '[]'
                        if (adminUserRoleId !== '' && adminUserRoleId !== null) {
                            @foreach ($rolePermisions as $roleId => $roleItems)
                                checkRole = parseInt('{{ $roleId }}')
                                if (checkRole == adminUserRoleId) {
                                    listRoles = '{!! $roleItems !!}'
                                }
                            @endforeach
                        }
                        listRoles = JSON.parse(listRoles);
                        if (listRoles.length) {
                            listRoles.map(item => {
                                let _this = $(`input[type="checkbox"]#${item}`)
                                _this.css({
                                    'background-color': '#556ee6',
                                    'border-color': '#556ee6'
                                })
                                _this.attr('disabled', true)
                                _this.attr('checked', true)
                            })
                        }
                    }
                });
                @if ($data_edit->is_supper_admin ?? 0)
                    $(`.role-body input[type="checkbox"]`).css({
                            'background-color': '#556ee6',
                            'border-color': '#556ee6'
                        }).attr('disabled', true).attr('checked', true)
                @elseif (isset($data_edit->admin_user_role_id) && $data_edit->admin_user_role_id)
                    const currentRoles = JSON.parse('{!! $rolePermisions[$data_edit->admin_user_role_id] ?? '[]' !!}');
                    if (currentRoles.length) {
                        currentRoles.map(item => {
                            let _this = $(`input[type="checkbox"]#${item}`)
                            _this.css({
                                'background-color': '#556ee6',
                                'border-color': '#556ee6'
                            })
                            _this.attr('disabled', true)
                            _this.attr('checked', true)
                        })
                    }
                @endif
            @endif
        });
    </script>
</div>
