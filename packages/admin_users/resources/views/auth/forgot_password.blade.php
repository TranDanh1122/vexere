<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title>{{__('AdminUser::admin.login.forgot_password')}}</title>
        {{-- Laravel csrf_token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('vendor/core/core/base/img/favicon.ico') }}">

        <!-- Bootstrap Css -->
        <link href="{{ asset('vendor/core/core/base/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('vendor/core/core/base/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('vendor/core/core/base/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
        <!-- Toastr -->
        <link rel="stylesheet" href="{{asset('vendor/core/core/base/css/custom-style.css')}}">
        <link rel="stylesheet" href="{{asset('vendor/core/core/base/plugins/toastr/toastr.min.css')}}">
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
                                            <h5 class="text-primary"> {{__('AdminUser::admin.login.reset_password')}}</h5>
                                            <p>{{__('AdminUser::admin.forgot_password.forgot_with')}} {{ getSiteName() }}.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ asset('vendor/core/core/base/img/profile-img.png') }}" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div>
                                    <div>
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ asset('vendor/core/core/base/img/logo.svg') }}" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div class="alert alert-success text-center mb-4" role="alert">
                                        {{__('AdminUser::admin.forgot_password.note')}}
                                    </div>
                                    {{-- <form class="form-horizontal"> --}}
                                        <div class="mb-3">
                                            <label for="useremail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="forgot_password_email" placeholder="@lang('AdminUser::admin.forgot_password.enter_email')">
                                        </div>

                                        <div class="text-end">
                                            <button class="btn btn-primary w-md waves-effect waves-light" id="forgot_password_comfirm" action="{!! route('admin.setForgotPassword') !!}">@lang('AdminUser::admin.forgot_password.confirm')</button>
                                        </div>
                                    {{-- </form> --}}
                                </div>

                            </div>
                        </div>

                        <div class="pt-4">
                            <p class="text-center">@lang('AdminUser::admin.forgot_password.return_login_desc') <a href="{{ route('admin.login') }}">@lang('AdminUser::admin.login.desc')</a></p>
                            <p class="text-center">&copy; {{ date('Y') }} {{ config('app.name') }}. Crafted with <svg width="14px" height="14px" viewBox="0 -5.37 77.646 77.646" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <defs>
                                  <linearGradient id="linear-gradient" x1="1.044" y1="0.005" x2="0.413" y2="0.749" gradientUnits="objectBoundingBox">
                                    <stop offset="0" stop-color="#ff7471"/>
                                    <stop offset="1" stop-color="#ff5245"/>
                                  </linearGradient>
                                </defs>
                                <g id="heart_red" data-name="heart red" transform="translate(-263.982 -435.283)">
                                  <g id="Group_25" data-name="Group 25">
                                    <path id="Path_69" data-name="Path 69" d="M302.81,446.03c-.059-.106-.128-.2-.187-.307.059.1.128.2.187.307Z" fill="none"/>
                                    <path id="Path_70" data-name="Path 70" d="M341.628,456.395l-.025-.006c.006-.142.025-.279.025-.431a20.662,20.662,0,0,0-37.039-12.611.171.171,0,0,0-.024-.007,2.169,2.169,0,0,1-3.54-.046l-.035.008a20.657,20.657,0,0,0-37,12.656c0,.147.018.282.018.424l-.029.013s0,.5.1,1.413a20.552,20.552,0,0,0,.6,3.364c1.608,6.945,6.938,20.286,24.659,32.122,10.242,6.879,12.73,8.743,13.383,8.867.031.006.048.033.083.033s.058-.033.094-.043c.7-.162,3.265-2.071,13.359-8.857,16.931-11.313,22.555-24,24.428-31.163a20.743,20.743,0,0,0,.854-4.546C341.623,456.824,341.628,456.395,341.628,456.395ZM302.81,446.03h0c-.059-.1-.128-.2-.187-.307C302.682,445.825,302.751,445.924,302.81,446.03Z" fill="#ff5245"/>
                                  </g>
                                  <path id="Path_71" data-name="Path 71" d="M295.337,474.437c-5.407-20.228,1.411-28.894,5-31.889a20.747,20.747,0,0,0-6.426-5.077c-6.5-1.416-15.583.295-21.458,16.921-1,3.4-1.458,11.938-.492,22.426a65.334,65.334,0,0,0,17.38,16.476c10.242,6.879,12.73,8.743,13.383,8.867.031.006.048.033.083.033s.058-.033.094-.043a2.946,2.946,0,0,0,.76-.373C301.6,496.005,298.749,487.182,295.337,474.437Z" fill="url(#linear-gradient)"/>
                                </g>
                              </svg> by WebAi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="progress-box"><div class="progress-run"></div></div>
        <section id="loading_box"><div id="loading_image"></div></section>
        <!-- JAVASCRIPT -->
        <script src="{{ asset('vendor/core/core/base/libraries/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/core/core/base/libraries/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/core/core/base/libraries/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('vendor/core/core/base/libraries/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('vendor/core/core/base/libraries/node-waves/waves.min.js') }}"></script>
        <!-- Toastr -->
        <script src="{{asset('vendor/core/core/base/plugins/toastr/toastr.min.js')}}"></script>

        <!-- App js -->
        <script src="{{ asset('vendor/core/core/base/libraries/app/app.js') }}"></script>
        <script src="{{asset('vendor/core/core/base/js/app.js')}}"></script>
        <script>
            var required_forgot_email   = '@lang('AdminUser::admin.login.required_forgot_email')';
        </script>
        <script src="{{asset('vendor/core/core/base/js/functions.js')}}"></script>
        <script src="{{asset('vendor/core/core/base/js/login.js')}}"></script>
    </body>
</html>
