@extends('layouts.app')
@section('content')
    <div class="@if (!$page->hide_sidebar || !$page->hide_breadcrumb) post_single @endif w-100">
        <div class="container">
            @if (!$page->hide_title)
                <div class="post_single__name w-100">
                    <h1 class="color_title" style="margin: 20px 0;">{{ $page->name ?? '' }}</h1>
                </div>
            @endif
            <div class="ck ck-reset ck-editor ck-rounded-corners w-100" role="application" dir="ltr">
                <div class="ck-content w-100" id="single-content" data-title="{{ __('Core::admin.setting.toc.title') }}">
                    {!! $page->getDetail() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
