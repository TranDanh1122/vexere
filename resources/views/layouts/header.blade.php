@php
    $menuHeader = \DreamTeam\Base\Facades\BaseHelper::getMenuWithLocation(\DreamTeam\Base\Models\Menu::PRIMARY, getLocale());
@endphp
<header class="header bg-primary-color">
    <nav class="mx-auto flex items-center justify-between p-5 lg:px-8" aria-label="Global">
        <a href="{{ route('app.home.vi') }}" class="-m-1.5 p-1.5">
            @include('layouts.image', [
                'src' => $themeConfig['logo_header_desktop'] ?? '/vendor/core/core/base/img/default_150x48.png',
                'alt' => 'logo',
                'width' => '148',
                'height' => '40',
                'lazy' => false,
                'class' => 'h-8 w-auto',
            ])
        </a>
        <div class="flex lg:hidden menu-mobile-icon">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
        <div class="hidden lg:flex lg:gap-x-12 items-center">
            @if (isset($menuHeader) && count($menuHeader) > 0)
                @foreach ($menuHeader as $menuLv1)
                    <li
                        class="list-none relative menu_level1 {{ isset($menuLv1['children']) && count($menuLv1['children']) > 0 ? 'menu_parent' : '' }}">
                        <a class="text-sm/6 font-semibold text-white" rel="{{ $menuLv1['rel'] ?? '' }}"
                            href="{{ $menuLv1['link'] ?? '' }}"
                            target="{{ $menuLv1['target'] == '_blank' ? 'blank' : '_self' }}">
                            {!! $menuLv1['name'] ?? '' !!}
                            @if (isset($menuLv1['children']) && count($menuLv1['children']) > 0)
                                <span class="icon-down">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="12"
                                        height="12">
                                        <path
                                            d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"
                                            fill="#fff" />
                                    </svg>
                                </span>
                            @endif
                        </a>
                        @if (isset($menuLv1['children']) && count($menuLv1['children']) > 0)
                            <ul class="submenu">
                                @foreach ($menuLv1['children'] as $menuLv2)
                                    <li class="submenu_item flex">
                                        <a rel="{{ $menuLv2['rel'] ?? '' }}" href="{{ $menuLv2['link'] ?? '' }}"
                                            target="{{ $menuLv2['target'] == '_blank' ? '_blank' : '_self' }}"
                                            class="lh-22 color_header">{!! $menuLv2['name'] ?? '' !!}
                                            @if (isset($menuLv2['children']) && count($menuLv2['children']) > 0)
                                                <span class="icon-down">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                                        width="12" height="12">
                                                        <path
                                                            d="M201.4 342.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 274.7 86.6 137.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"
                                                            fill="#fff" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </a>
                                        @if (isset($menuLv2['children']) && count($menuLv2['children']) > 0)
                                            <ul class="submenu">
                                                @foreach ($menuLv2['children'] as $menuLv3)
                                                    <li class="submenu_item flex">
                                                        <a rel="{{ $menuLv3['rel'] ?? '' }}"
                                                            href="{{ $menuLv3['link'] ?? '' }}"
                                                            target="{{ $menuLv3['target'] == '_blank' ? 'blank' : '_self' }}"
                                                            class="lh-22 color_header">{!! $menuLv3['name'] ?? '' !!}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            @endif
            <a href="tel:{{ $config_general['phone'] ?? '' }}"
                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-primary-color shadow-xs items-center flex gap-3 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-phone">
                    <path
                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
                Hotline 24/7
            </a>
        </div>
    </nav>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div class="menu-mobile" role="dialog" aria-modal="true">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-10"></div>
        <div
            class="menu-mobile__fixed fixed inset-y-0 right-0 z-10 w-full overflow-y-auto px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
            <div class="flex items-center justify-between">
                <a href="{{ route('app.home.vi') }}" class="-m-1.5 p-1.5">
                    @include('layouts.image', [
                        'src' =>
                            $themeConfig['logo_header_desktop'] ?? '/vendor/core/core/base/img/default_150x48.png',
                        'alt' => 'logo',
                        'width' => '148',
                        'height' => '40',
                        'lazy' => false,
                        'class' => 'h-8 w-auto',
                    ])
                </a>
                <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700 menu-mobile-close">
                    <span class="sr-only">Close menu</span>
                    <svg class="size-8 text-prirmary-color" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="#fff" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-6 flow-root">
                <div class="-my-6 divide-y divide-gray-500/10">
                    <div class="space-y-2 py-6">
                        <nav class="navigation-nav">
                            <ul class="navigation-nav__content">
                                @foreach ($menuHeader as $menu_lv1)
                                    <li
                                        class="menu_level1 flex-center-between {{ isset($menu_lv1['children']) && count($menu_lv1['children']) > 0 ? 'menu_parent' : '' }}">
                                        <a class="menu_item fs-16 lh-22 color_header flex-inline-center h-100"
                                            rel="{{ $menu_lv1['rel'] ?? '' }}" href="{{ $menu_lv1['link'] ?? '' }}"
                                            target="{{ $menu_lv1['target'] == '_blank' ? 'blank' : '_self' }}">
                                            {!! $menu_lv1['name'] ?? '' !!}
                                        </a>
                                        @if (isset($menu_lv1['children']) && count($menu_lv1['children']) > 0)
                                            <span class="icon-down"></span>
                                        @endif
                                        @if (isset($menu_lv1['children']) && count($menu_lv1['children']) > 0)
                                            <ul class="submenu">
                                                @foreach ($menu_lv1['children'] as $menu_lv2)
                                                    <li class="submenu-item flex-center-between">
                                                        <a rel="{{ $menu_lv2['rel'] ?? '' }}"
                                                            href="{{ $menu_lv2['link'] ?? '' }}"
                                                            target="{{ $menu_lv2['target'] == '_blank' ? 'blank' : '_self' }}"
                                                            class="lh-22 color_header">{!! $menu_lv2['name'] ?? '' !!}
                                                        </a>
                                                        @if (isset($menu_lv2['children']) && count($menu_lv2['children']) > 0)
                                                            <span class="icon-down"></span>
                                                        @endif
                                                        @if (isset($menu_lv2['children']) && count($menu_lv2['children']) > 0)
                                                            <ul class="submenu">
                                                                @foreach ($menu_lv2['children'] as $menu_lv3)
                                                                    <li class="submenu-item flex-center-between">
                                                                        <a rel="{{ $menu_lv3['rel'] ?? '' }}"
                                                                            href="{{ $menu_lv3['link'] ?? '' }}"
                                                                            target="{{ $menu_lv3['target'] == '_blank' ? 'blank' : '_self' }}"
                                                                            class="lh-22 color_header">{!! $menu_lv3['name'] ?? '' !!}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                    <div class="py-1">
                        <a href="tel:{{ $config_general['phone'] ?? '' }}"
                            class="w-[150px] rounded-md bg-white px-3 py-2 text-sm font-semibold text-primary-color shadow-xs items-center flex gap-3 hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            Hotline 24/7
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
