@if (isset($filters) && count($filters) > 0)
    @foreach ($filters as $filter)
        <div class="card card-pink mb-2">
            <div class="card-header text-sm" data-card-widget="collapse" style="padding: 5px 13px;">
                <div class="card-title" style="font-size: 14px; margin-bottom: 0;">@lang($filter->name)</div>
            </div>
            <div class="card-body form-group-checkbox" style="padding: 10px 13px;">
                @foreach ($filterDetails->where('filter_id', $filter->id) as $details)
                    <div class="float-left mb-1 form-checkbox" style="margin-right: 10px;">
                        <input type="checkbox" class="form-check-input mr-1" name="filters[]"
                            id="filters-{{ $details->id }}" value="{{ $details->id }}"
                            @if (isset($productFilters[$details->id]) && $productFilters[$details->id] == $productId) checked @endif>
                        <label class="m-0" style="cursor: pointer;"
                            for="filters-{{ $details->id }}">{{ $details->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <h5 class="p-2" style="font-weight: normal;">{{ __('Ecommerce::admin.filter_error') }}
        {{ __('Ecommerce::admin.please') }}<a href="{{ route('admin.filters.index') }}"
            target="_blank">{{ __('Ecommerce::admin.add_filter') }}</a> {{ __('Ecommerce::admin.or_choose_filter') }} <a
            href="{{ route('admin.product_categories.edit', $categoryId) }}"
            target="_blank">{{ __('Ecommerce::admin.category') }}</a></h5>
@endif