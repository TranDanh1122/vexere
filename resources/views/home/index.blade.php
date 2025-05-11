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
        @include('layouts.form-search')
    </div>
@endsection
