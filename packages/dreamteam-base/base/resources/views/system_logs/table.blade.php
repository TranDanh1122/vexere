<td style="width: 180px;">{!! $admin_users[$value->admin_id] ?? __('Core::admin.unknown') !!}</td>
<td style="width: 180px;">{!! $value->ip !!}</td>
<td style="width: 180px;">{!! $value->getActionName() !!}</td>
<td style="width: 250px;">{!! $value->getModuleName() !!}</td>
<td style="width: 180px;">{!! $value->getTime('time') !!}</td>
