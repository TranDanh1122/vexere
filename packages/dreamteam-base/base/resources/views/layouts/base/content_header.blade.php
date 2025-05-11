<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                @if(View::hasSection('title'))
                    @yield('title')
                @else
                    {{__($module_name??'')}}
                @endif
            </h4>
            @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
            @php $breadcrumbs = BaseHelper::injectionBreadcrumbSetting($breadcrumbs);@endphp
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{__('Translate::admin.homepage')}}</a></li>
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if (isset($breadcrumb['url']) && !empty($breadcrumb['url']))
                            <li class="breadcrumb-item"><a href="{{$breadcrumb['url']??''}}">{{__($breadcrumb['name']??'')}}</a></li>
                        @else
                            <li class="breadcrumb-item">{{__($breadcrumb['name'] ?? '')}}</li>
                        @endif
                    @endforeach
                </ol>
            </div>
            @endif
        </div>
    </div>
</div>
<!-- end page title -->
@if((((isset($has_locale) && $has_locale == true) || isset($hasLocale) && $hasLocale == true) && isset(\Request()->lang_locale) && !empty(\Request()->lang_locale) && \Request()->lang_locale != config('app.fallback_locale')) || isset($recordLangLocale) && $recordLangLocale != config('app.fallback_locale'))
    <div class="alert alert-warning">
        <span>{{ __('Core::admin.general.notice_lang') }} "<b>{{ Language::getSupportedLocales()[(\Request()->lang_locale ?? $recordLangLocale)]['name'] ?? '' }}</b>"</span>
    </div>
@endif