@extends('layouts.app')
@section('content')
<div class="banner">
    @include('layouts.image', [
    'src' => $settingHome['banner'] ?? '',
    'width' => '100%',
    'height' => '640',
    'lazy' => false,
    'title' => '',
    ])
    <div class="container banner-container">
        <div class="booking-container ">
            <div class="form-container ">
                <form class="form-container__form tracuu-form">
                    <div class="form-row form-input relative" style="width:100%">
                        <div class="form-row" style="width:100%">
                            <div class="location-inputs" style="width:100%">
                                <div class="input-group" style="max-width:50%">

                                    <div class="input-group__content">
                                        <span class="input-label">Số điện thoại đã đặt vé</span>
                                        <input type="text" class="input-field" id="phone" value="">
                                    </div>
                                </div>

                                <div class="input-group" style="max-width:50%">

                                    <div class="input-group__content">
                                        <span class="input-label">Số căn cước công dân người đặt</span>
                                        <input type="text" class="input-field" id="cccd" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="search-btn">Tra cứu</button>
                        <p class="error text-xs italic text-red-500 mt-2 mb-2 absolute -bottom-7 right-0"></p>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@include('home.popup')

@endsection
@section('foot')
<!-- <link rel="stylesheet" href="{{ asset('/assets/libs/jquery/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/libs/swiper/swiper-bundle.min.css') }}">
    <script defer src="{{ asset('/assets/libs/jquery/jquery-ui.min.js') }}"></script>
    <script defer src="{{ asset('/assets/libs/swiper/swiper-bundle.min.js') }}"></script> -->
@endsection