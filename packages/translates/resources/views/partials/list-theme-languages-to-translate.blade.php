@if (count($groups) > 1)
    <div class="mb-3 text-end d-flex gap-2 justify-content-start justify-content-lg-end align-items-center lang-drop">
        <h5 class="mb-0">{{ trans('Translate::translation.translations') }}:</h5>
        @if (count($groups) <= 3)
            <div class="d-flex gap-3 align-items-center">
                @foreach ($groups as $language)
                    @continue($language['locale'] === $group['locale'])
                    <a
                        href="{{ route($route, $language['locale'] == app()->getLocale() ? [] : ['lang_locale' => $language['locale']]) }}"
                        class="text-decoration-none small"
                    >
                        {!! languageFlag($language['flag'], $language['name']) !!}
                        {{ $language['name'] }}
                    </a>
                @endforeach
            </div>
        @else
            <x-Core::dropdown>
                <x-slot:trigger>
                    <a
                        class="d-flex align-items-center gap-2 dropdown-toggle text-muted text-decoration-none"
                        href="#"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        {!! languageFlag($group['flag'], $group['name']) !!}
                        {{ $group['name'] }}
                    </a>
                </x-slot:trigger>

                @foreach ($groups as $language)
                    @continue($language['locale'] === $group['locale'])

                    <x-Core::dropdown.item
                        href="{{ route($route, $language['locale'] == app()->getLocale() ? [] : ['lang_locale' => $language['locale']]) }}"
                        class="d-flex gap-2 align-items-center"
                    >
                        @if ($language['flag'])
                            {!! languageFlag($language['flag'], $language['name']) !!}
                        @endif
                        {{ $language['name'] }}
                    </x-Core::dropdown.item>
                @endforeach
            </x-Core::dropdown>
        @endif

        <input
            name="lang_locale"
            type="hidden"
            value="{{ BaseHelper::stringify(request()->input('lang_locale')) }}"
        >
    </div>
@endif
