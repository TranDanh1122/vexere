<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{__('AdminUser::admin.login.login')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Laravel csrf_token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('vendor/core/core/base/img/favicon.ico')}}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('vendor/core/core/base/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('vendor/core/core/base/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Toastr -->
    <link rel="stylesheet" href="{{asset('vendor/core/core/base/plugins/toastr/toastr.min.css')}}">
    <!-- App Css-->
    <link href="{{ asset('vendor/core/core/base/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('vendor/core/core/base/css/custom-style.css')}}">
    <script src="{{asset('vendor/core/core/base/js/app.js')}}"></script>
    <script src="{{asset('vendor/core/core/base/js/functions.js')}}"></script>
    <script type="text/javascript">
        'use strict';

        var DreamTeamCoreVariables = DreamTeamCoreVariables || {};
        DreamTeamCoreVariables.languages = {
            tables: {{ \Illuminate\Support\Js::from(trans('Core::tables')) }},
            notices_msg: {{ \Illuminate\Support\Js::from(trans('Core::notices')) }},
            pagination: {{ \Illuminate\Support\Js::from(trans('pagination')) }},
        };
    </script>
    <style>
        .col-md-8 {
            margin: 0 auto;
        }
        .panel {
            background: #fff;
        }
        .panel-heading {
            padding: 20px 0;
            background: #ccc;
        }
        .panel-body {
            padding: 15px;
        }
        .img {
            text-align: center;
        }
        button {
            margin-top: 15px;
        }
    </style> 
</head>
<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <h3 class="panel-heading text-center">{{__('AdminUser::admin.register.security')}}</h3>
                        <div class="panel-body text-left">
                            {{__('AdminUser::admin.register.step_1')}} <code>{{ $data['secret'] ?? '' }}</code><br/><br/>
                            <div class="img">
                                <img src="{{$data['google2fa_url']  ?? ''}}" alt="">
                            </div>
                            <br/>
                            2. {{__('AdminUser::admin.register.step_2')}}<br/><br/>
                            <form class="form-horizontal" action="{{ route('admin.2faVerify') }}" method="POST">
                                {{ csrf_field() }}
                                <div class="form-group{{ $errors->has('one_time_password-code') ? ' has-error' : '' }}">
                                    {{-- <label for="one_time_password" class="control-label">{{__('AdminUser::admin.register.verification_code')}}</label> --}}
                                    <input id="one_time_password" name="one_time_password" class="form-control col-md-4"  type="text" required placeholder="{{__('AdminUser::admin.register.verification_code')}}" />
                                </div>
                                <button class="btn btn-lg btn-login btn-block btn-success" type="submit">{{__('AdminUser::admin.register.verification')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="progress-box"><div class="progress-run"></div></div>
    <section id="loading_box"><div id="loading_image"></div></section>
    <!-- end account-pages -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('vendor/core/core/base/libraries/jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('vendor/core/core/base/libraries/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('vendor/core/core/base/libraries/metismenu/metisMenu.min.js')}}"></script>
    <script src="{{ asset('vendor/core/core/base/libraries/simplebar/simplebar.min.js')}}"></script>
    <script src="{{ asset('vendor/core/core/base/libraries/node-waves/waves.min.js')}}"></script>
    <!-- Toastr -->
    <script src="{{asset('vendor/core/core/base/plugins/toastr/toastr.min.js')}}"></script>
    <!-- App js -->
    <script src="{{ asset('vendor/core/core/base/libraries/app/app.js')}}"></script>
    <script>
        var required_info           = '@lang('AdminUser::admin.login.required_info')';
        var required_name           = '@lang('AdminUser::admin.login.required_name')';
        var required_password       = '@lang('AdminUser::admin.login.required_password')';
        var required_forgot_email   = '@lang('AdminUser::admin.login.required_forgot_email')';
    </script>
    <script src="{{asset('vendor/core/core/base/js/login.js')}}"></script>
</body>
</html>