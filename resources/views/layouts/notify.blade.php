<section id="show_compare" class="compare" data-compare_box=""></section>
<section id="loading_box" data-device="web" data-error="{{ __('Có lỗi xảy ra, vui lòng thử lại sau') }}"><div id="loading_image"></div></section>
@if(Session::has('notify_message'))
<div id="toast-container" class="toast-top-right show_fade" data-delay="{!! Session::get('notify_delay') ?? 0 !!}">
    <div class="toast toast-{!! Session::get('notify_level') !!}" aria-live="assertive" style="">
        <div class="toast-icon">
            <span class="success flex-center"><svg xmlns="http://www.w3.org/2000/svg" fill="#1ec28b" width="15" height="15" class="done" viewBox="0 0 512 512"><path d="M256 512c141.4 0 256-114.6 256-256S397.4 0 256 0S0 114.6 0 256S114.6 512 256 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg></span>
            <span class="error flex-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="#fff" width="16" height="16"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/></svg></span>
        </div>
        <div class="toast-message">
            <p class="toast-message_title">{{ __('Thông báo') }}</p>
            <p class="toast-message_content">{!! Session::get('notify_message') !!}</p>
        </div>
        <p class="toast-close" title="{{ __('Đóng') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="16" height="16"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/></svg>
        </p>
    </div>
</div>
@else
    <div id="toast-container" class="toast-top-right">
        <div class="toast" aria-live="assertive" style="">
            <div class="toast-icon">
                <span class="success flex-center"><svg xmlns="http://www.w3.org/2000/svg" fill="#1ec28b" width="15" height="15" class="done" viewBox="0 0 512 512"><path d="M256 512c141.4 0 256-114.6 256-256S397.4 0 256 0S0 114.6 0 256S114.6 512 256 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg></span>
                <span class="error flex-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="#fff" width="16" height="16"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/></svg></span>
            </div>
            <div class="toast-message">
                <p class="toast-message_title">{{ __('Thông báo') }}</p>
                <p class="toast-message_content"></p>
            </div>
            <p class="toast-close" title="{{ __('Đóng') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="16" height="16"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/></svg>
            </p>
        </div>
    </div>
@endif
