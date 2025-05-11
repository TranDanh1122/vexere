@extends('Core::layouts.app')

@section('title')
    @lang($module_name ?? '')
    <style>.parammater-code {display: flex;justify-content: space-between;}.parammater-code span {color: #556ee6}</style>
@endsection
@section('content')
    @php
        $dataFilter = apply_filters(FILTER_LIST_DATA_GOOGLE_CONVERSION, []);
    @endphp
    <div class="row tab">
        @if (count($dataFilter))
            <div class="col-md-2">
                <ul class="tab-list" style="border: 0;">
                    @foreach ($dataFilter as $filterItems)
                        @foreach (($filterItems['tab'] ?? []) as $filterItem)
                            <li class="tab-list__item" style="display: block;padding-left: 0;border-bottom: 1px solid #ddd;" data-active="{{ $filterItem['key'] }}">{{ $filterItem['name'] }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            <div class="col-md-10">
                <form action="" method="POST">
                    @csrf
                    <input type="hidden" name="google_conversion" value="google_conversion">
                    @foreach ($dataFilter as $filterItem)
                        @include($filterItem['view'])
                    @endforeach
                    <div class="form-actions">
                        <div class="form-actions__group"> <button type="submit" name="google_conversion"
                                value="google_conversion" class="btn btn-sm btn-primary"><i
                                    class="fas fa-save mr-1"></i>@lang('Translate::form.action.save_setting')</button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div style="text-align: justify;" class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-block-helper me-2"></i>
                {{ trans('Core::google_conversion.notfound') }}
            </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            var class_active = $('.tab-list__item.active').data('active');
            $('.tab .tab-content').removeClass('active');
            $('.tab .tab-content__' + class_active).addClass('active');
            $('.tab-list__item').on('click', function() {
                var class_active = $(this).data('active');
                $('.tab-list__item').removeClass('active');
                $(this).addClass('active');
                $('.tab .tab-content').removeClass('active');
                $('.tab .tab-content__' + class_active).addClass('active');
            });
            $('.tab-list__item:first-child').trigger('click')
        });
    </script>
@endsection
