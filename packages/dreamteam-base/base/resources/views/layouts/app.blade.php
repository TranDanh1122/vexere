<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin | {{ env('APP_NAME') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Laravel csrf_token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="admin_dir" content="{{ config('app.admin_dir') }}">
    <meta name="language" content="{{ \App::getLocale() }}">
    {{-- Asset đầu trang --}}
    {!! \Asset::renderHeader() !!}
    {{-- Code nhúng đầu trang --}}
    <script type="text/javascript">
        'use strict';

        var DreamTeamCoreVariables = DreamTeamCoreVariables || {};
        DreamTeamCoreVariables.languages = {
            tables: {{ \Illuminate\Support\Js::from(trans('Core::tables')) }},
            notices_msg: {{ \Illuminate\Support\Js::from(trans('Core::notices')) }},
            pagination: {{ \Illuminate\Support\Js::from(trans('pagination')) }},
        };
    </script>
    @yield('head')
    <style>
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--single {
            height: 35px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body data-sidebar="dark" style="position: relative;">
    <script type="text/javascript">
        var form_delete_confirm = '{{ __('Core::admin.form_delete_confirm') }}';
        var recordLangLocale = '{{ $recordLangLocale ?? (\Request()->lang_locale ?? getLocale()) }}'
    </script>
    <div class="sudo-wrap">
        <!-- Begin page -->
        <div id="layout-wrapper">

            {{-- header --}}
            @include('Core::layouts.base.header')

            {{-- menu --}}
            @include('Core::layouts.base.menu')

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content" style="min-height: 100vh">
                    <div class="container-fluid">

                        {{-- menu --}}
                        @include('Core::layouts.base.content_header')
                        @include('Core::layouts.base.alert')
                        @yield('content')
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                {{-- footer --}}
                @include('Core::layouts.base.footer')
            </div>
            <!-- end main content-->
        </div>

        <!-- END layout-wrapper -->
        <!-- Right Sidebar -->

        <!-- /Right-bar -->
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
        @include('media::config')
        @include('media::partials.media')
        @include('Core::layouts.base.popup-alert')
    </div>
    {{-- Asset cuối trang --}}
    {!! \Asset::renderFooter() !!}
    @yield('foot')
    <script>
        var errMessage = '{{ __('Core::admin.general.error_message') }}'
        var adminDir = '{{ config('app.admin_dir', 'admin') }}';
        checkAuth();

        function checkAuth() {
            let isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);;
            if (isChrome) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                $.ajax({
                    method: 'POST',
                    url: '/admin/checkAuth',
                    dataType: 'json',
                    token: '{{ csrf_token() }}',
                    beforeSend: function() {},
                    success: function(data) {
                        if (data.status == 0) {
                            window.location.href = data.link;
                        }
                    },
                    error: function(error) {},
                });
            }
        }
        @php
            $type = request()->cookie('typeSave') ?? 'error';
            $currentUrl = url()->current() ?? '';
            if ($type == 'success' && strpos($currentUrl, '/create') != false) {
                \Cookie::queue(Cookie::forget('typeSave'));
            }
        @endphp
        var typeSave = "{!! $type !!}";
        var currentUrlCreate = "{!! $currentUrl !!}";
        if (typeSave == 'success' && currentUrlCreate.includes('/create')) {
            $('.form-horizontal .form-control, .form-check-input').each(function(index, element) {
                if ($(element).attr('id') == undefined) {
                    localStorage.removeItem(`${currentUrlCreate}__${$(element).attr('name')}`);
                } else {
                    localStorage.removeItem(`${currentUrlCreate}__${$(element).attr('id')}`);
                }
            })
        }
    </script>
</body>

</html>
