@extends('layouts.app')
@section('content')
    @if(checkAgent() === 'mobile')
        @php
        if (empty($dataRequest['from'] ?? '') || ($dataRequest['from'] ?? '') === 'saigon') {
            $dataRequest['departureName'] = 'Sài Gòn';
            $dataRequest['destinationName'] = 'Vũng Tàu';
        } else {
            $dataRequest['departureName'] = 'Vũng Tàu';
            $dataRequest['destinationName'] = 'Sài Gòn';
        }
        @endphp
        <div class="relative w-full bg-blue-600 p-4 text-white change-time-box">
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <h1 class="text-xl font-bold">{{ $dataRequest['departureName'] }} → {{ $dataRequest['destinationName'] }}</h1>
                    <div class="mt-2 inline-flex items-center py-1 rounded">
                        <span class="font-medium text-sm mr-2">CHIỀU ĐI</span>
                        <span class="text-sm">{{ $dataRequest['fromDate'] ?? '' }}</span>
                    </div>
                </div>
                <div style="display: flex;flex-direction: column;">
                    <a href="#" class="text-white underline text-sm open-change-time">Thay đổi</a>
                    <a href="#" class="text-white underline text-sm open-filter">Lọc kết quả</a>
                </div>
            </div>
        </div>
    @endif
    <div class="banner search-page {{ checkAgent() }}">
        @if(checkAgent() == 'mobile')
            <div class="relative w-full bg-blue-600 p-4 text-white">
                <div class="flex justify-between items-center">
                    <div class="flex-1">
                        <h1 class="text-xl font-bold">Thay đổi tìm kiếm</h1>
                    </div>
                    <a href="#" class="text-white underline text-sm close-change-time">Đóng</a>
                </div>
            </div>
        @endif
        @include('layouts.form-search', compact('fromDate', 'toDate', 'dataRequest'))
    </div>
    <div class="content">
        <div class="container">
            <div class="content-left {{ checkAgent() }}">
                @include('search.sidebar')
            </div>
            <div class="content-right {{ checkAgent() }}">
                <input type="hidden" name="url" value="{{ request()->fullUrl() }}">
                <div class="content-right__top">
                    @include('search.result-top')
                </div>
                <div class="content-right__list">
                    @include('search.result')
                </div>
            </div>
        </div>
    </div>
    @include('search.popup')
@endsection
@section('foot')
    <link rel="stylesheet" href="{{ asset('/assets/libs/jquery/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/libs/swiper/swiper-bundle.min.css') }}">
    <script defer src="{{ asset('/assets/libs/jquery/jquery-ui.min.js') }}"></script>
    <script defer src="{{ asset('/assets/libs/swiper/swiper-bundle.min.js') }}"></script>
@endsection
