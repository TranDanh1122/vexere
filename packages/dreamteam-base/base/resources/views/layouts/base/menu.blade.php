{{-- ========== Left Sidebar Start ==========  --}}
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->

            @php
                $menu = admin_menu()->getAll();
            @endphp
            <ul class="metismenu list-unstyled" id="side-menu">
                @if (isset($menu) && !empty($menu))
                @foreach ($menu as $item)
                    @switch($item['type'])
                        @case('group')
                            @php
                                // Kiểm tra quyền
                                $show = false;
                                foreach ($item['role'] ?? [] as $role) {
                                    if (checkRole($role) == true) { $show = true; }
                                }
                            @endphp
                            @if ($show == true)
                                <li class="menu-title" key="t-menu">
                                    @lang($item['name'] ?? '')
                                    @if(isset($item['count']))
                                        <span class="badge badge-success" style="border-radius: 10px;">{{ $item['count'] }}</span>
                                    @endif
                                </li>
                            @endif
                        @break
                        @case('single')
                            @php
                                // Kiểm tra active menu
                                $active = activeMenu($item['route'] ?? '', $item['active'] ?? []);
                                // echo $active;
                            @endphp
                            @if (checkRole($item['role']) && (!isset($roleDisable) || !in_array($item['role'], $roleDisable)))
                                <li>
                                    <a href="{{ route($item['route']) }}" class="waves-effect {!! $active !!}">
                                        <i class="{!! $item['icon'] ?? '' !!}"></i>
                                        <span key="t-dashboards">@lang($item['name'] ?? '')</span>
                                        @if(isset($item['count']))
                                            <span class="badge badge-success" style="border-radius: 10px;">{{ $item['count'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @break
                        @case('multiple')
                            @php
                                // Kiểm tra quyền
                                $show = false;
                                $array_active = [];
                                foreach ($item['childs'] ?? [] as $childs) {
                                    // Kiểm tra quyền
                                    if (checkRole(convertRole($childs['role'])) == true) { $show = true; }
                                    // Kiểm tra active menu
                                    if (isset($childs['route']) && !empty($childs['route'])) {
                                        $array_active[] = $childs['route'];
                                    }
                                    if (isset($childs['active']) && !empty($childs['active'])) {
                                        foreach ($childs['active'] as $v) {
                                            $array_active[] = $v;
                                        }
                                    }
                                }
                                $active_parent = '';
                                // Nếu menu con được active thì mở menu cha
                                if (in_array(\Route::currentRouteName(), $array_active)) {
                                    $active_parent = 'menu-open';
                                }
                            @endphp
                            @if ($show == true)
                            <li class="{{ ($item['id'] == 'group_interface' || $item['id'] == 'group_setting') ? activeMenu($item['route'] ?? '', array_map(function($item) {
                                    if(!$item['childs']) return $item['route'];
                                    return $item;
                                }, $item['childs'])) : '' }}">
                                <a href="{{ $item['route'] ? route($item['route']) : 'javascript: void(0);' }}" class="{{ ($item['id'] == 'group_interface' || $item['id'] == 'group_setting') ? '' : 'has-arrow waves-effect' }}">
                                    <i class="{!! $item['icon'] ?? '' !!}"></i>
                                    <span key="t-ecommerce">@lang($item['name'] ?? '')</span>
                                    @if(isset($item['count']))
                                        <span class="badge badge-success" style="border-radius: 10px;">{{ $item['count'] }}</span>
                                    @endif
                                </a>
                                @if($item['id'] != 'group_interface' && $item['id'] != 'group_setting')
                                <ul class="sub-menu" aria-expanded="false">
                                    @foreach ($item['childs'] ?? [] as $childs)
                                        @switch($childs['type'])
                                            @case('single')
                                                @if (checkRole($childs['role']) && (!isset($roleDisable) || !in_array($childs['role'], $roleDisable)))
                                                    @php
                                                        // Kiểm tra active menu
                                                        $active = activeMenu($childs['route'] ?? '', $childs['active'] ?? []);
                                                    @endphp
                                                    <li class="{!! $active !!}"><a href="{{ route($childs['route']) }}" key="t-products">@lang($childs['name'] ?? '')
                                                            @if(isset($childs['count']))
                                                                <span class="badge badge-success" style="border-radius: 10px;">{{ $childs['count'] }}</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endif
                                            @break
                                            @case('multiple')
                                                @php
                                                    $menu_childs = $menu[$childs['id']] ?? '';
                                                    // Kiểm tra quyền
                                                    $show = false;
                                                    $array_active = [];
                                                    foreach ($menu_childs['childs'] ?? [] as $childs_v2) {
                                                        // Kiểm tra quyền
                                                        if (checkRole(convertRole($childs_v2['role'])) == true) { $show = true; }
                                                        // Kiểm tra active menu
                                                        if (isset($childs_v2['route']) && !empty($childs_v2['route'])) {
                                                            $array_active[] = $childs_v2['route'];
                                                        }
                                                        if (isset($childs_v2['active']) && !empty($childs_v2['active'])) {
                                                            foreach ($childs_v2['active'] as $v) {
                                                                $array_active[] = $v;
                                                            }
                                                        }
                                                    }
                                                    $active_parent = '';
                                                    // Nếu menu con được active thì mở menu cha
                                                    if (in_array(\Route::currentRouteName(), $array_active)) {
                                                        $active_parent = 'menu-open';
                                                    }
                                                @endphp
                                                @if ($show == true)

                                                    <li>
                                                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                                                            <span key="t-ecommerce">@lang($childs['name'] ?? '')</span>
                                                            @if(isset($childs['count']))
                                                                <span class="badge badge-success" style="border-radius: 10px;">{{ $childs['count'] }}</span>
                                                            @endif
                                                        </a>
                                                        <ul class="sub-menu" aria-expanded="false">
                                                            @foreach ($menu_childs['childs'] ?? [] as $childs_v2)
                                                                @if (checkRole($childs_v2['role']) && (!isset($roleDisable) || !in_array($childs_v2['role'], $roleDisable)))
                                                                @php
                                                                    // Kiểm tra active menu
                                                                    $active = activeMenu($childs_v2['route'] ?? '', $childs_v2['active'] ?? []);
                                                                @endphp
                                                                <li class="{!! $active !!}"><a href="{{ route($childs_v2['route']) }}" key="t-products">@lang($childs_v2['name'] ?? '')
                                                                        @if(isset($childs_v2['count']))
                                                                            <span class="badge badge-success" style="border-radius: 10px;">{{ $childs['count'] }}</span>
                                                                        @endif
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                            @break
                                            @default
                                                @if (checkRole(convertRole($childs['role'])) && (!isset($roleDisable) || !in_array($childs['role'], $roleDisable)))
                                                    @php
                                                        // Kiểm tra active menu
                                                        $active = activeMenu($childs['route'] ?? '', $childs['active'] ?? []);
                                                    @endphp
                                                    <li class="{!! $active !!}"><a href="{{ route($childs['route']) }}" key="t-products">@lang($childs['name'] ?? '')
                                                            @if(isset($childs['count']))
                                                                <span class="badge badge-success" style="border-radius: 10px;">{{ $childs['count'] }}</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endif
                                        @endswitch
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endif
                        @break
                    @endswitch
                @endforeach
                @endif
            </ul>
        </div>
        <!-- Sidebar -->

        
    </div>
</div>
<!-- Left Sidebar End