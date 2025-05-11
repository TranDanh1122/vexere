<div class="form-title row mb-4" style="float: none;">
    <div class="col col-4">@lang('Core::admin.setting.email.email_template')</div>
    <div class="col col-6">@lang('Core::admin.setting.email.email_desc')</div>
    <div class="col col-2" style="text-align: right">@lang('Core::admin.setting.email.action')</div>
</div>
<div>
    @foreach ($data as $emailName => $defaultData)
        @php
            $namePrefix = $module . "[$emailName]";
        @endphp
        <div class="row mb-4 email-wrapper">
            <div class="row">
                <div class="col col-4" style="font-size: 14px; color: #333"><a href="#" class="toggle-show-email-content d-block">{{ $defaultData['name'] }}</a>
                </div>
                <div class="col col-6" style="font-size: 14px; color: #333">
                    {{ $defaultData['description'] ?? '' }}</div>
                <div class="col col-2" style="display: flex; align-items:center; justify-content:end">
                    <div class="form-switch form-switch-lg">
                        <input @checked($value[$emailName]['enabled'] ?? 1) type="checkbox" class="form-check-input toggle-enable-send-mail" name="{{ $namePrefix }}[enabled]"
                            style="transform: scale(1.2)" value="1" style="left: 0;">
                    </div>
                    <div class="edit-email-wrapper">
                        <svg fill="#fff" height="18px" width="18px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 348.882 348.882" xml:space="preserve"><g><path d="M333.988,11.758l-0.42-0.383C325.538,4.04,315.129,0,304.258,0c-12.187,0-23.888,5.159-32.104,14.153L116.803,184.231 c-1.416,1.55-2.49,3.379-3.154,5.37l-18.267,54.762c-2.112,6.331-1.052,13.333,2.835,18.729c3.918,5.438,10.23,8.685,16.886,8.685 c0,0,0.001,0,0.001,0c2.879,0,5.693-0.592,8.362-1.76l52.89-23.138c1.923-0.841,3.648-2.076,5.063-3.626L336.771,73.176 C352.937,55.479,351.69,27.929,333.988,11.758z M130.381,234.247l10.719-32.134l0.904-0.99l20.316,18.556l-0.904,0.99 L130.381,234.247z M314.621,52.943L182.553,197.53l-20.316-18.556L294.305,34.386c2.583-2.828,6.118-4.386,9.954-4.386 c3.365,0,6.588,1.252,9.082,3.53l0.419,0.383C319.244,38.922,319.63,47.459,314.621,52.943z"/><path d="M303.85,138.388c-8.284,0-15,6.716-15,15v127.347c0,21.034-17.113,38.147-38.147,38.147H68.904 c-21.035,0-38.147-17.113-38.147-38.147V100.413c0-21.034,17.113-38.147,38.147-38.147h131.587c8.284,0,15-6.716,15-15 s-6.716-15-15-15H68.904c-37.577,0-68.147,30.571-68.147,68.147v180.321c0,37.576,30.571,68.147,68.147,68.147h181.798 c37.576,0,68.147-30.571,68.147-68.147V153.388C318.85,145.104,312.134,138.388,303.85,138.388z"/></g></svg>
                    </div>
                </div>
            </div>
            <div class="row mt-3 email-content hidden-tab">
                <div class="col col-12">
                    <div style="padding: 20px; border: 1px solid #ddd; border-radius: 4px">
                        <label>@lang('Core::admin.setting.email.variable_desc')</label>
                        @include('Core::admin.custom.param_email', ['param' => $defaultData['guide_params']])
                        <div class="mb-3" style="position: relative">
                            <label for="{{ $namePrefix }}[title]">@lang('Core::admin.setting.email.title')</label>
                            <input type="text" class="form-control" x-on:input="handleInput($event)"
                                autocomplete="off" name="{{ $namePrefix }}[title]" id="{{ $namePrefix }}[title]"
                                placeholder="@lang('Core::admin.setting.email.title')" value="{{ $value[$emailName]['title'] ?? $defaultData['default_title'] }}">
                        </div>
                        @include('Form::base.ckeditor', [
                            'name'              => $namePrefix . '[content]',
                            'value'             => $value[$emailName]['content'] ?? $defaultData['default_content'],
                            'required'          => '0',
                            'label'             => __('Core::admin.setting.email.content'),
                        ])
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
