<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin | {{env('APP_NAME')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Laravel csrf_token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="admin_dir" content="{{ config('app.admin_dir') }}">
    <meta name="language" content="{{ \App::getLocale() }}">
    {{-- Asset đầu trang --}}
    {!! \Asset::renderHeader() !!}
    {{-- Code nhúng đầu trang --}}
    @yield('head')
</head>
<body data-sidebar="dark">
    <div class="sudo-wrap">
        <!-- Begin page -->
        <div id="layout-wrapper">
            @yield('content')
        </div>
    </div>
    @include('media::config')
    {{-- Asset cuối trang --}}
    {!! \Asset::renderFooter() !!}
    @yield('foot')
</body>
</html>
