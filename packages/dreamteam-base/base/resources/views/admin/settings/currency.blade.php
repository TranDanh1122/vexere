<textarea name="currencies" id="currencies" class="hidden" style="display: none;">{!! json_encode($currencies ?? '') !!}</textarea>
<textarea name="deleted_currencies" id="deleted_currencies" class="hidden" style="display: none;"></textarea>
<div class="swatches-container">
    <div class="header clearfix">
        <div class="swatch-item">
            {{ trans('Core::currency.code') }}
        </div>
        <div class="swatch-item">
            {{ trans('Core::currency.symbol') }}
        </div>
        <div class="swatch-item swatch-decimals">
            {{ trans('Core::currency.number_of_decimals') }}
        </div>
        <div class="swatch-item swatch-exchange-rate">
            {{ trans('Core::currency.exchange_rate') }}
        </div>
        <div class="swatch-item swatch-is-prefix-symbol">
            {{ trans('Core::currency.is_prefix_symbol') }}
        </div>
        <div class="swatch-is-default">
            {{ trans('Core::currency.is_default') }}
        </div>
        <div class="remove-item">{{ trans('Core::currency.remove') }}</div>
    </div>
    <ul class="swatches-list">
        <div id="loading-update-currencies" style="display: none;">
            <div class="currency-loading-backdrop"></div>
            <div class="currency-loading-loader"></div>
        </div>
    </ul>
    <div class="clearfix"></div>
    <a href="#" class="js-add-new-attribute">
        {{ trans('Core::currency.new_currency') }}
    </a>
</div>
<script id="currency_template" type="text/x-custom-template">
    <div id="loading-update-currencies" style="display: none;">
        <div class="currency-loading-backdrop"></div>
        <div class="currency-loading-loader"></div>
    </div>
    <li data-id="__id__" class="clearfix">
        <div class="swatch-item" data-type="title">
            <input type="text" class="form-control" value="__title__">
        </div>
        <div class="swatch-item" data-type="symbol">
            <input type="text" class="form-control" value="__symbol__">
        </div>
        <div class="swatch-item swatch-decimals" data-type="decimals">
            <input type="number" class="form-control" value="__decimals__">
        </div>
        <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
            <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
        </div>
        <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
            <div class="ui-select-wrapper">
                <select class="ui-select form-control form-select">
                    <option value="1" __isPrefixSymbolChecked__>{{ trans('Core::currency.before_number') }}</option>
                    <option value="0" __notIsPrefixSymbolChecked__>{{ trans('Core::currency.after_number') }}</option>
                </select>
            </div>
        </div>
        <div class="swatch-is-default" data-type="is_default">
            <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
        </div>
        <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
    </li>
</script>