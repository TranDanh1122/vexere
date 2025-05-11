<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="{{ $lang ?? 'vi' }}" xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2"/>
    <link href="{{ !empty($themeConfig['favicon'] ?? '') ? RvMedia::url($themeConfig['favicon']) : asset('favicon.ico') }}" type="image/x-icon" rel="shortcut icon"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="agent" content="{{ checkAgent() == 'web' ? 'desktop' : 'mobile' }}">
    {{-- Các meta seo --}}
    @include('layouts.seo')
    @yield('head')
    {!! $config_code['html_head_no_script'] ?? '' !!}
    @if(isset($config_code['on_off_delay']) && $config_code['on_off_delay'] == 0)
        {!! $config_code['html_head'] ?? '' !!}
    @endif
    @if (isset($meta_seo['html_head']) && !empty($meta_seo['html_head']))
        {!! $meta_seo['html_head'] !!}
    @endif
    <script>var errorMessage = '{{ __('Theme::general.error_message') }}';</script>
    <link rel="stylesheet" href="{{ asset('assets/fonts/roboto/stylesheet.min.css') }}?v={{ config('dreamteam_asset.version') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}?v={{ config('dreamteam_asset.version') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ config('dreamteam_asset.version') }}">
    <link rel="stylesheet" href="{{ asset('/assets/libs/flatpickr/flatpickr.min.css') }}">
</head>
<body class="website-body desktop {{ BaseHelper::getCurrentPageName() }}">
	{{-- Thanh admin hiển thị cho quản trị --}}
    <div id="wrapper" class="page-wrapper">
        @include('layouts.header')
        <main class="main">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
    @include('layouts.notify')
    <div class="fixed"></div>
    <script defer src="{{ asset('/assets/libs/jquery/jquery.min.js') }}"></script>
    @yield('foot')
    <script defer src="{{ asset('/assets/js/main.js') }}"></script>
    <script defer src="{{ asset('/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script defer src="{{ asset('/assets/libs/flatpickr/flatpickr-vn.js') }}"></script>
    @if(isset($config_code['on_off_delay']) && $config_code['on_off_delay'] == 0)
        {!! $config_code['html_body'] ?? '' !!}
    @endif
    <script type="text/javascript" defer>
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function() {
                @if(!isset($config_code['on_off_delay']) || $config_code['on_off_delay'] == 1)
                    @php
                        $third_party_script_head = str_replace(['<script','</script'],['\x3Cscript','\x3C/script'],$config_code['html_head'] ?? '');
                        $third_party_script_body = str_replace(['<script','</script'],['\x3Cscript','\x3C/script'],$config_code['html_body'] ?? '');
                        $timeDelay = ($config_code['time_delay'] ?? 10).'000';
                    @endphp
                    setTimeout(function() {
                        let script_head = `{!!$third_party_script_head!!}`;
                        $('head').append(script_head);

                        let script_body = `{!!$third_party_script_body!!}`;
                        $('body').append(script_body);
                    }, parseInt('{{ $timeDelay }}'));
                @endif
                @if(isset($hasPluginFormCustom) && $hasPluginFormCustom)
                    setTimeout(function() {
                        let script_body = `<script defer="" src="https://www.google.com/recaptcha/api.js?v=0.1.4"><\/script>`;
                        $('body').append(script_body);
                    }, 6000);
                @endif
            });

        });
    </script>
</body>
</html>
