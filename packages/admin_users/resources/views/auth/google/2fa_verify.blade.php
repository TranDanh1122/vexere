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
</head>
<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5 text-muted">
                        <a href="index" class="d-block auth-logo">
                            <img src="{{ getOption('theme_config', '', false)['logo_header_desktop'] ?? '/' }}" alt="Logo" height="50"
                                class="auth-logo-dark mx-auto">
                        </a>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="p-2">
                                <div class="text-center">
                                    <div class="p-2 mt-4">

                                        <h5>{{__('AdminUser::admin.register.security')}}</h5>
                                        <p class="mb-5">{{__('AdminUser::admin.register.step_2')}}</p>
                                        @if ($errors->any())
                                            <div>
                                                @foreach ($errors->all() as $error)
                                                    <div style="text-align: justify;" class="alert alert-danger alert-dismissible fade show" role="alert">
                                                        <i class="mdi mdi-block-helper me-2"></i>
                                                        {{ $error }}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <form action="{{ route('admin.2faVerify') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="mb-3">
                                                        <label for="digit1" class="visually-hidden">Digit 1</label>
                                                        <input type="text" class="form-control form-control-lg text-center two-step" id="digit1-input" maxLength="1">
                                                    </div>
                                                </div>

                                                <div class="col-2">
                                                    <div class="mb-3">
                                                        <label for="digit2" class="visually-hidden">Digit 2</label>
                                                        <input type="text" class="form-control form-control-lg text-center two-step" id="digit2-input" maxLength="1">
                                                    </div>
                                                </div>

                                                <div class="col-2">
                                                    <div class="mb-3">
                                                        <label for="digit3" class="visually-hidden">Digit 3</label>
                                                        <input type="text" class="form-control form-control-lg text-center two-step" id="digit3-input" maxLength="1">
                                                    </div>
                                                </div>

                                                <div class="col-2">
                                                    <div class="mb-3">
                                                        <label for="digit4" class="visually-hidden">Digit 4</label>
                                                        <input type="text" class="form-control form-control-lg text-center two-step"  id="digit4-input" maxLength="1">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="mb-3">
                                                        <label for="digit4" class="visually-hidden">Digit 4</label>
                                                        <input type="text" class="form-control form-control-lg text-center two-step"  id="digit5-input" maxLength="1">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="mb-3">
                                                        <label for="digit4" class="visually-hidden">Digit 4</label>
                                                        <input type="text" class="form-control form-control-lg text-center two-step"  id="digit6-input" maxLength="1">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="one_time_password" name="one_time_password">
                                            <div class="mt-4">
                                                <button type="submit" type="submit" class="btn btn-success w-md">{{__('AdminUser::admin.register.verification')}}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <p>©
                            <script>
                                document.write(new Date().getFullYear())

                            </script> {{ getSiteName() }}
                        </p>
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
        function moveToNext(elem, count){
            if(elem.value.length > 0) {
                $("#digit"+count+"-input").focus();
            }
        }
        $(document).ready(function() {
            let count = 1;
            document.querySelector('button[type="submit"]').setAttribute('disabled', true)
            $(".two-step").keyup(function(e){
                document.querySelector('button[type="submit"]').setAttribute('disabled', true)
                if(count == 0){
                    count = 1;
                }
                if(e.keyCode === 8){
                    if(count == 7){
                        count = 5;
                    }
                    $("#digit"+count+"-input").focus();
                    count--;
                }else{
                    const regex = /^[a-zA-Z0-9]$/;
                    if (regex.test($(this).val())) {
                        if(count > 0) {
                            count++;
                            $("#digit"+count+"-input").focus();
                        }
                    }
                }
                let completeCode = ''
                $('.two-step').each(function() {
                    completeCode += $(this).val()
                })
                if (!checkEmpty(completeCode) && completeCode.length == 6) {
                    $('#one_time_password').val(completeCode).change()
                    document.querySelector('button[type="submit"]').removeAttribute('disabled')
                }
            });
            const item = document.querySelector('.two-step');
            $(".two-step").on('paste', function (e) {
                e.preventDefault();
                let paste = (e.originalEvent.clipboardData || window.clipboardData).getData("text");
                const regex = /^[a-zA-Z0-9]{1,6}$/;
                if (regex.test(paste)) {
                    $('.two-step').each(function (index) {
                        if (paste[index]) {
                            $(this).val(paste[index]);
                            count = index + 1;
                        } else {
                            $(this).val('');
                        }
                    });
                    $('.two-step').get(count - 1).focus();
                    $(this).trigger('keyup');
                }
            });
        })
    </script>
    <script src="{{asset('vendor/core/core/base/js/login.js')}}"></script>
</body>
</html>
