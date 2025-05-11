<div class="tab-content tab-content__payment_success">
    <div class="w-100" x-data="{
        openArea: {{ getGoogleConversion('conversion_by_success', 0) }},
        handleInput: function(e) {
            if (e.target.checked) {
                this.openArea = true
            } else {
                this.openArea = false
            }
        }
    }">
        <h5>{{ trans('Core::google_conversion.by_success') }}</h5>
        <div class="mb-3 row toggle-on-off-checkbox" style="position: relative;">
            <label for="conversion_by_success"
                class="col-md-4 col-form-label">{{ trans('Core::google_conversion.active') }}</label>
            <div class=" col-md-8  form-switch form-switch-lg">
                <input type="hidden" name="conversion_by_success" value="0">
                <input type="checkbox" class="form-check-input" x-on:input="handleInput($event)"
                    name="conversion_by_success" id="conversion_by_success" value="1"
                    {{ ((bool) getGoogleConversion('conversion_by_success', 0)) ? 'checked=""' : '' }}
                    style="margin-top: 6px;left: 0;">
            </div>
        </div>
        <div class="mb-3 on-of-checkbox-config" x-show="openArea">
            <div class="row">
                <div class="col-md-8">
                    <label for=""
                        class="col-form-label form-controll-label">{{ trans('Core::google_conversion.code_page') }}</label>
                    <textarea rows="8" class="form-control" name="conversion_by_success_data" id="conversion_by_success_data"
                        placeholder="{{ trans('Core::google_conversion.code_conversion') }}">{{ getGoogleConversion(
                            'conversion_by_success_data',
                            "<script>
                                                                                        gtag('event', 'conversion', {
                                                                                            'send_to': 'AW-XXXXXXXXX/YOUR_EVENT_ID_PURCHASE',
                                                                                            'value': {value},
                                                                                            'currency': '{currency}',
                                                                                            'transaction_id': '{transaction_id}'
                                                                                        });
                                                                                        </script>",
                        ) }}</textarea>
                </div>
                <div class="col-md-4">
                    <p class="mb-0"><b>{{ trans('Core::google_conversion.paramater_conversion') }}</b></p>
                    <p class="helper" style="float: none">
                        {{ trans('Core::google_conversion.paramater_conversion_note') }}</p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.price_total') }}</b>
                        <span>{value}</span></p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.currency') }}</b>
                        <span>{currency}</span></p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.transaction_id') }}</b>
                        <span>{transaction_id}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content tab-content__cart">
    <div class="w-100" x-data="{
        openArea: {{ getGoogleConversion('conversion_follow_page_cart', 0) }},
        handleInput: function(e) {
            if (e.target.checked) {
                this.openArea = true
            } else {
                this.openArea = false
            }
        }
    }">
        <h5>{{ trans('Core::google_conversion.follow_page_cart') }}</h5>
        <div class="mb-3 row toggle-on-off-checkbox" style="position: relative;">
            <label for="conversion_follow_page_cart"
                class="col-md-4 col-form-label">{{ trans('Core::google_conversion.active') }}</label>
            <div class=" col-md-8  form-switch form-switch-lg">
                <input type="hidden" name="conversion_follow_page_cart" value="0">
                <input type="checkbox" class="form-check-input" x-on:input="handleInput($event)"
                    name="conversion_follow_page_cart" id="conversion_follow_page_cart" value="1"
                    {{ ((bool) getGoogleConversion('conversion_follow_page_cart', 0)) ? 'checked=""' : '' }}
                    style="margin-top: 6px;left: 0;">
            </div>
        </div>
        <div class="mb-3 on-of-checkbox-config" x-show="openArea">
            <div class="row">
                <div class="col-md-8">
                    <label for=""
                        class="col-form-label form-controll-label">{{ trans('Core::google_conversion.code_page') }}</label>
                    <textarea rows="8" class="form-control" name="conversion_follow_page_cart_data"
                        id="conversion_follow_page_cart_data" placeholder="{{ trans('Core::google_conversion.code_conversion') }}">{{ getGoogleConversion(
                            'conversion_follow_page_cart_data',
                            "<script>
                                                                                        gtag('event', 'page_view', {
                                                                                            'send_to': 'AW-XXXXXXXXX/YOUR_EVENT_ID_CART',
                                                                                            'value': {value},
                                                                                            'currency': '{currency}'
                                                                                        });
                                                                                        </script>",
                        ) }}</textarea>
                </div>
                <div class="col-md-4">
                    <p class="mb-0"><b>{{ trans('Core::google_conversion.paramater_conversion') }}</b></p>
                    <p class="helper" style="float: none">
                        {{ trans('Core::google_conversion.paramater_conversion_note') }}</p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.price_total') }}</b>
                        <span>{value}</span></p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.currency') }}</b>
                        <span>{currency}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content tab-content__payment">
    <div class="w-100" x-data="{
        openArea: {{ getGoogleConversion('conversion_follow_page_payment', 0) }},
        handleInput: function(e) {
            if (e.target.checked) {
                this.openArea = true
            } else {
                this.openArea = false
            }
        }
    }">
        <h5>{{ trans('Core::google_conversion.follow_page_payment') }}</h5>
        <div class="mb-3 row toggle-on-off-checkbox" style="position: relative;">
            <label for="conversion_follow_page_payment"
                class="col-md-4 col-form-label">{{ trans('Core::google_conversion.active') }}</label>
            <div class=" col-md-8  form-switch form-switch-lg">
                <input type="hidden" name="conversion_follow_page_payment" value="0">
                <input type="checkbox" class="form-check-input" x-on:input="handleInput($event)"
                    name="conversion_follow_page_payment" id="conversion_follow_page_payment" value="1"
                    {{ ((bool) getGoogleConversion('conversion_follow_page_payment', 0)) ? 'checked=""' : '' }}
                    style="margin-top: 6px;left: 0;">
            </div>
        </div>
        <div class="mb-3 on-of-checkbox-config" x-show="openArea">
            <div class="row">
                <div class="col-md-8">
                    <label for=""
                        class="col-form-label form-controll-label">{{ trans('Core::google_conversion.code_page') }}</label>
                    <textarea rows="8" class="form-control" name="conversion_follow_page_payment_data"
                        id="conversion_follow_page_payment_data" placeholder="{{ trans('Core::google_conversion.code_conversion') }}">{{ getGoogleConversion(
                            'conversion_follow_page_payment_data',
                            "<script>
                                                                                        gtag('event', 'page_view', {
                                                                                        'send_to': 'AW-XXXXXXXXX/YOUR_EVENT_ID_CART',
                                                                                        'value': 0,
                                                                                        'currency': 'VND'
                                                                                    });
                                                                                    </script>",
                        ) }}</textarea>
                </div>
                <div class="col-md-4">
                    <p class="mb-0"><b>{{ trans('Core::google_conversion.paramater_conversion') }}</b></p>
                    <p class="helper" style="float: none">
                        {{ trans('Core::google_conversion.paramater_conversion_note') }}</p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.price_total') }}</b>
                        <span>{value}</span></p>
                    <p class="parammater-code"><b>{{ trans('Core::google_conversion.currency') }}</b>
                        <span>{currency}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
