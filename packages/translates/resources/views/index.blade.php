@extends('Core::layouts.app')

@section('content')
    <x-Core::card>
        <x-Core::card.header style="background: #fff; border-bottom: 1px solid #ced4da;">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a
                        href="#tabs-detail"
                        class="nav-link active"
                        data-bs-toggle="tab"
                    >{{ trans('Translate::language.name') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        href="#tabs-settings"
                        class="nav-link"
                        data-bs-toggle="tab"
                    >{{ trans('Translate::language.config') }}
                    </a>
                </li>
            </ul>
        </x-Core::card.header>
        <x-Core::card.body>
            <div class="tab-content">
                <div
                    class="tab-pane active"
                    id="tabs-detail"
                >
                    <div class="row">
                        <div class="col-md-6">

                            <input
                                type="hidden"
                                id="lang_id"
                                value="0"
                            >
                            <input
                                type="hidden"
                                id="language_flag_path"
                                value="{{ BASE_LANGUAGE_FLAG_PATH }}"
                            >

                            <x-Core::form.select
                                name="language_id"
                                :label="trans('Translate::language.choose_language')"
                                :helper-text="trans('Translate::language.choose_language_helper')"
                                searchable
                            >
                                <option>{{ trans('Translate::language.select_language') }}
                                </option>
                                @php
                                    $newLanguages = array_diff_key($languages, $activeLanguages->pluck('name', 'code')->toArray());
                                @endphp
                                @foreach ($newLanguages as $key => $language)
                                    <option
                                        value="{{ $key }}"
                                        data-language="{{ \Illuminate\Support\Js::encode($language) }}"
                                    > {{ $language[2] }} - {{ $language[1] }}
                                    </option>
                                @endforeach
                            </x-Core::form.select>

                            <x-Core::form.text-input
                                :label="trans('Translate::language.language_name')"
                                name="lang_name"
                                :helper-text="trans('Translate::language.language_name_helper')"
                            />

                            <x-Core::form.text-input
                                :label="trans('Translate::language.locale')"
                                name="lang_locale"
                                :helper-text="trans('Translate::language.locale_helper')"
                                wrapperClass="d-none"
                            />

                            <x-Core::form.text-input
                                :label="trans('Translate::language.language_code')"
                                name="lang_code"
                                :helper-text="trans('Translate::language.language_code_helper')"
                                wrapperClass="d-none"
                            />

                            <x-Core::form.radio-list
                                name="lang_rtl"
                                :label="trans('Translate::language.text_direction')"
                                value="0"
                                :options="[
                                    '0' => trans('Translate::language.left_to_right'),
                                    '1' => trans('Translate::language.right_to_left'),
                                ]"
                                :helper-text="trans('Translate::language.text_direction_helper')"
                                wrapperClass="d-none"
                            />

                            <x-Core::form.select
                                :label="trans('Translate::language.flag')"
                                name="flag_list"
                                :options="['' => trans('Translate::language.select_flag')] + $flags"
                                :helper-text="trans('Translate::language.flag_helper')"
                                class="select-search-language"
                            />

                            <x-Core::form.text-input
                                :label="trans('Translate::language.order')"
                                type="number"
                                name="lang_order"
                                :helper-text="trans('Translate::language.order_helper')"
                                value="0"
                            />

                            <x-Core::button
                                type="submit"
                                color="primary"
                                id="btn-language-submit"
                                data-store-url="{{ route('admin.languages.store') }}"
                                data-update-url="{{ route('admin.languages.edit') }}"
                                data-add-language-text="{{ trans('Translate::language.add_new_language') }}"
                                data-update-language-text="{{ trans('Translate::language.update') }}"
                            >
                                {{ trans('Translate::language.add_new_language') }}
                            </x-Core::button>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <x-Core::table class="table-language">
                                    <x-Core::table.header>
                                        <x-Core::table.header.cell>
                                            {{ trans('Translate::language.language_name') }}
                                        </x-Core::table.header.cell>
                                        <x-Core::table.header.cell>
                                            {{ trans('Translate::language.status') }}
                                        </x-Core::table.header.cell>
                                        <x-Core::table.header.cell>
                                            {{ trans('Translate::language.order') }}
                                        </x-Core::table.header.cell>
                                        <x-Core::table.header.cell>
                                            {{ trans('Translate::language.actions') }}
                                        </x-Core::table.header.cell>
                                    </x-Core::table.header>
                                    <x-Core::table.body>
                                        @if(count($activeLanguages))
                                            @each('Translate::partials.language-item', $activeLanguages, 'item')
                                        @else
                                            <tr>
                                                <td colspan="6" class="bg-gray-200">
                                                    {{ trans('Translate::language.no_languages') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </x-Core::table.body>
                                </x-Core::table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tabs-settings">
                    @include('Translate::partials.language-setting', ['data' => $settingLanguages])
                </div>
            </div>
        </x-Core::card.body>
    </x-Core::card>

    <x-Core::modal.action
        type="danger"
        class="modal-confirm-delete"
        :title="trans('core/base::tables.confirm_delete')"
        :description="trans('Translate::language.delete_confirmation_message')"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['class' => 'delete-crud-entry']"
    />
@endsection
@section('foot')
    <script>
        $('.select-search-full, .select-search-language').select2();
    </script>
@endsection
