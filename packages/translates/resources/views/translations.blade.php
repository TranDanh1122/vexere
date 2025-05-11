@extends('Core::layouts.app')

@section('content')
    <div class="row" style="justify-content: center">
        <div class="col-xl-12 col-lg-12">
            <x-Core::alert type="warning">
                <p class="mb-1">
                    {{ trans('Translate::translation.theme_translations_instruction') }}
                </p>
    
                <p class="mb-0">
                    {!! trans('Translate::translation.re_import_alert', [
                        'here' => \DreamTeam\Base\Facades\Html::link('#', trans('Translate::translation.here'), [
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#confirm-publish-modal',
                        ]),
                    ]) !!}
                </p>
            </x-Core::alert>
    
            <div class="row">
                <div class="col-md-6">
                    <p>{{ trans('Translate::translation.translate_from') }}
                        <strong class="text-info">{{ Arr::get($locales, 'en.name', 'en') }}</strong>
                        {{ trans('Translate::translation.to') }}
                        <strong class="text-info">{{ $locale['name'] }}</strong>
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="text-end">
                        @include('Translate::partials.list-theme-languages-to-translate', [
                            'groups' => $locales,
                            'group' => $locale,
                            'route' => 'admin.translations.index',
                        ])
                    </div>
                </div>
            </div>
    
            @if (!$exists)
                <x-Core::card>
                    <x-Core::card.body>
                        <div class="text-center">
                            <p>{!! BaseHelper::clean(
                                trans('Translate::translation.no_translations', ['locale' => "<strong>{$locale['name']}</strong>"]),
                            ) !!}</p>
    
                            <x-Core::button color="primary" class="button-import-groups" :data-url="route('admin.translations.import', ['locale' => Request()->lang_locale ?? ''])">
                                {{ trans('Translate::translation.import_group') }}
                            </x-Core::button>
                        </div>
                    </x-Core::card.body>
                </x-Core::card>
            @else
                <div class="translations-table listdata">
                    <form method="GET" class="form-inline flex" style="display: flex; gap: 15px;">
                        <div class="form-group">
                            <select name="group" id="" class="form-control input-sm form-select">
                                <option value="">{{ trans('Translate::translation.group') }}</option>
                                @foreach($groupWithNameSpace as $group => $groupName)
                                    <option value="{{ $group }}" {{ (Request()->group ?? '') == $group ? 'selected' : '' }}>{{ $groupName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input class="form-control input-sm" name="keyword" placeholder="{{ trans('Translate::table.search') }}" value="{{ Request()->keyword ?? '' }}"/>
                        </div>
                        <div class="form-group">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-flat btn-success btn-sm search-btn"><i class="fas fa-search mr-1"></i>@lang('Translate::table.search')</button>
                            </div>
                        </div>
                        <input type="hidden" name="lang_locale" value="{{ Request()->lang_locale ?? '' }}">
                    </form>
                    <div class="table-rep-plugin mb-4">
                        <div class="table-wrapper">
                            <div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('Translate::translation.group') }}</th>
                                            <th style="width: 300px">{{ Arr::get($locales, 'en.name', 'en') }}</th>
                                            <th style="width: 300px">{{ Arr::get($locales, "{$translateLocale}.name", $translateLocale) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @include('Translate::translation-item')
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="float-right pagination-sm m-0">
                        {{ $translationsCollection->appends(Request()->all())->links() }}
                    </div>
                    <div class="float-right mr-2">
                        <button type="button" class="btn btn-sm btn-default">@lang('Translate::table.total'): <span class="total">{{$translationsCollection->total()}}</span></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('foot')
    <x-Core::modal.action id="confirm-publish-modal" :title="trans('Translate::translation.publish_translations')" :description="trans('Translate::translation.confirm_publish_translations', ['locale' => $locale['name']])" type="warning" :submit-button-attrs="['class' => 'button-import-groups', 'data-url' => route('admin.translations.import')]"
        :submit-button-label="trans('Core::base.yes')" />
@endsection
