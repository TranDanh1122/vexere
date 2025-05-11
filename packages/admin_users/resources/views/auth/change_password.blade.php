<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{__('AdminUser::admin.forgot_password.change_password')}}</title>
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
    <script type="text/javascript">
        'use strict';

        var DreamTeamCoreVariables = DreamTeamCoreVariables || {};
        DreamTeamCoreVariables.languages = {
            tables: {{ \Illuminate\Support\Js::from(trans('Core::tables')) }},
            notices_msg: {{ \Illuminate\Support\Js::from(trans('Core::notices')) }},
            pagination: {{ \Illuminate\Support\Js::from(trans('pagination')) }},
        };
    </script>
</head>
<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">{{__('AdminUser::admin.welcome')}}</h5>
                                        <p>{{__('AdminUser::admin.forgot_password.change_password')}}</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('vendor/core/core/base/img/profile-img.png')}}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0"> 
                            <div class="auth-logo">

                                <a href="index.html" class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('vendor/core/core/base/img/logo.svg')}}" alt="" class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                            	<form class="form-horizontal" action="{!! route('admin.setChangePassword', $admin_users->id) !!}" id="change_password">
	                                <div class="mb-3">
	                                    <label class="form-label">@lang('AdminUser::admin.forgot_password.password_new')</label>
	                                    <div class="input-group auth-pass-inputgroup">
	                                        <input type="password" class="form-control" placeholder="Enter new password" aria-label="Password" id="password">
	                                    </div>
	                                </div>
	                                <div class="mb-3">
	                                    <label class="form-label">@lang('AdminUser::admin.forgot_password.password_comfirm')</label>
	                                    <div class="input-group auth-pass-inputgroup">
	                                        <input type="password" class="form-control" placeholder="Enter comfirm password" id="password_comfirm">
	                                    </div>
	                                </div>
	                                <div class="mt-3 d-grid">
	                                    <button class="btn btn-primary waves-effect waves-light" type="submit">@lang('AdminUser::admin.forgot_password.comfirm')</button>
	                                </div>
                            	</form>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="mt-5 text-center">
                        
                        <div>
                            <p>Don't have an account ? <a href="auth-register.html" class="fw-medium text-primary"> Signup now </a> </p>
                        </div>
                    </div> --}}

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
    <script src="{{ asset('vendor/core/core/base/libraries/app/app.js') }}"></script>
    <script src="{{asset('vendor/core/core/base/js/app.js')}}"></script>
    <script src="{{asset('vendor/core/core/base/js/functions.js')}}"></script>
    <script>
        var required_info           		= '@lang('AdminUser::admin.forgot_password.required_info')';
        var required_password       		= '@lang('AdminUser::admin.forgot_password.required_password')';
        var required_password_comfirm       = '@lang('AdminUser::admin.forgot_password.required_password_comfirm')';
        var required_equal   				= '@lang('AdminUser::admin.forgot_password.required_equal')';
        var required_strong   				= '@lang('AdminUser::admin.forgot_password.required_strong')';
    </script>
    <script src="{{asset('vendor/core/core/base/js/login.js')}}"></script>
</body>
</html>