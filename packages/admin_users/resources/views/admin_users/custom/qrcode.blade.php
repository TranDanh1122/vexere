<div x-data="{
    openVerify: '{{ $adminUser->enabel_google2fa == 1 ? true : false }}',
    handleInput(e) {
        this.openVerify = false
        if (e.target.checked) {
            this.openVerify = true
            document.querySelector('.form-actions__group button').setAttribute('disabled', true)
        }
    },
    handleOffInput(e) {
        this.openVerify = true
        if (e.target.checked) {
            this.openVerify = false
            document.querySelector('.form-actions__group button').removeAttribute('disabled')
        }
    }
}" class="row" style="padding-left: 10px">
    <div class="col-lg-12">
        <div class="form-radio">
            <div class="form-check">
                <input type="radio" x-on:input="handleOffInput($event)" class="form-check-input" name="enabel_google2fa"
                    id="enabel_google2fa_1" value="0" style="font-size: 18px;"
                    {{ $adminUser->enabel_google2fa == 0 ? 'checked' : '' }}>
                <label style="padding-top: 4px;" class="form-check-label"
                    for="enabel_google2fa_1">{{ __('AdminUser::admin.2fa_on') }}</label>
            </div>
            <div class="form-check">
                <input x-on:input="handleInput($event)" type="radio" class="form-check-input" name="enabel_google2fa"
                    id="enabel_google2fa_0" value="1" style="font-size: 18px;"
                    {{ $adminUser->enabel_google2fa == 1 ? 'checked' : '' }}>
                <label style="padding-top: 4px;" class="form-check-label"
                    for="enabel_google2fa_0">{{ __('AdminUser::admin.2fa_off') }}</label>
            </div>
        </div>
    </div>
    <div class="mb-3 row" x-show="openVerify">
        <div class="col-md-10">
            @if ($adminUser->id == Auth::guard('admin')->user()->id)
                <label for="google2fa_url" class="col-form-label"><span
                        style="font-size: 12px;">{{ __('AdminUser::admin.qr_note') }}</span></label>
                <div class="image-box">
                    <img src="{{ $google2faUrl }}" alt="">
                    <p class="text-center" style="max-width: 200px"><code>{{ $google2faSecret }}</code></p>
                    <p class="text-center" style="max-width: 200px">{{ __('AdminUser::admin.qr_key') }}</p>
                </div>
                <div class="mb-3" x-data="{ enabelBtn: false }">
                    <label class="form-label">{{ __('AdminUser::admin.qr_code_enter') }}</label>
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected"><input
                            x-on:input="enabelBtn = $event.target.value.lenght ? true : false" data-toggle="touchspin"
                            type="text" value="" class="form-control" name="one_time_password"><span
                            class="input-group-btn input-group-append"><button
                                class="btn btn-primary bootstrap-touchspin-up verify-2fa" type="button"
                                x-bind:disabled="enabelBtn">Verify</button></span></div>
                </div>
            @elseif($adminUser->enabel_google2fa == 0)
                <p class="error helper">{{ __('AdminUser::admin.2fa_only') }}</p>
            @endif
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('body').on('click', '.verify-2fa', function(e) {
            e.preventDefault();
            $('input[name="one_time_password"]').closest('.mb-3').find('.error').remove()
            if (checkEmpty($('input[name="one_time_password"]').val())) {
                $('input[name="one_time_password"]').closest('.mb-3').append(formHelper(
                    "{{ __('Core::admin.general.require', ['name' => 'Otp']) }}"));
                return
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('admin.admin_users.2faVerify') }}',
                data: {
                    one_time_password: $('input[name="one_time_password"]').val()
                },
                beforeSend: function() {
                    activeProgress(0)
                },
                success: function(result) {
                    activeProgress(99, 'close');
                    alertText(result.message, result.type || 'success')
                    if (result.type == 'success') {
                        document.querySelector('.form-actions__group button')
                            .removeAttribute('disabled')
                    } else {
                        document.querySelector('.form-actions__group button').setAttribute(
                            'disabled', true)
                    }
                },
                error: function(error) {
                    document.querySelector('.form-actions__group button').setAttribute(
                        'disabled', true)
                    activeProgress(99, 'close');
                    alertText(errMessage, 'error')
                }
            })
        })
    })
</script>
