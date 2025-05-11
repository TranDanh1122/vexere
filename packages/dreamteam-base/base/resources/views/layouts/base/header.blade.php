<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                @php
                    $configOverview = getOption('overview', null, false);
                @endphp
                <a href="/" class="logo logo-light" target="_blank">
                    <h1 style="color: #fff;font-size: 22px;padding-top: 25px;">{!! $configOverview['name_company'] ?? config('app.name') !!}</h1>
                </a>
            </div>
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>
        <div class="d-flex">
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-cache_clear="" title="Xóa Cache">
                    <i class="bx bx-bug"></i>
                </button>
            </div>
            {{-- <div class="dropdown d-inline-block">
                @if (count(config('app.language') ?? []) > 0)
                    @foreach (config('app.language') as $key => $lang)
                        @if (App::getLocale() == $key)
                        <button type="button" class="btn header-item waves-effect"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img id="header-lang-img" src="{{ $lang['flag']??'' }}" alt="Header Language" height="16">
                        </button>
                        @endif
                    @endforeach
                @endif
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    @if (count(config('app.language') ?? []) > 0)
                    @foreach (config('app.language') as $key => $lang)
                    <a href="javascript:;" data-language="{!! $key !!}" class="dropdown-item notify-item language" data-lang="en">
                        <img src="{{ $lang['flag']?? '' }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">{!! $lang['name'] ?? '' !!}</span>
                    </a>
                    @endforeach
                    @endif
                </div>
            </div> --}}

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
{{--             <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge bg-danger rounded-pill">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <a href="#!" class="small" key="t-view-all"> View All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">

                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                            <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">View More..</span>
                        </a>
                    </div>
                </div>
            </div> --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ Auth::guard('admin')->user()->avatar != '' ? RvMedia::getImageUrl(Auth::guard('admin')->user()->avatar) : asset('vendor/core/core/base/img/users/no-avatar.png') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{!! Auth::guard('admin')->user()->getName() !!}<br/><span style="font-size: 12px">{{ Str::limit(Auth::guard('admin')->user()->is_supper_admin ? 'Supper admin' : (Auth::guard('admin')->user()->adminUserRole?->name ?? ''), 20, '...') }}</span></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('admin.admin_users.change_info', Auth::guard('admin')->user()->id) }}"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">@lang('Translate::admin.account_info')</span></a>
                    {{-- <a class="dropdown-item d-block" href="{{ route('admin.admin_users.change_password', Auth::guard('admin')->user()->id) }}"><i class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings">@lang('Translate::admin.change_password')</span></a> --}}
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">@lang('Translate::admin.logout')</span></a>
                </div>
            </div>
        </div>
    </div>
</header>