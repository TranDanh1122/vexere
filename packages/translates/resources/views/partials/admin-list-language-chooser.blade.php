@php
    $languages ??= Language::isHasMultipleLanguage() ? Language::getActiveLanguage() : [];
@endphp
@if (count($languages))
    <div class="text-end d-flex gap-2 justify-content-start justify-content-lg-end align-items-center lang-drop">
        <h5 style="font-weight: 400" class="mb-0">{{ trans('Translate::language.translations') }}:</h5>
        @if (count($languages) <= 3)
            <div class="d-flex gap-3 align-items-center">
                @foreach ($languages as $language)
                    @continue($language->locale === Arr::get(Language::getCurrentLagRecord(), 'locale'))

                    <a href="{{ route(Route::currentRouteName(), array_merge($params ?? [], $language->locale === Arr::get(Language::getCurrentLagRecord(), 'locale') ? [] : [Language::refLangKey() => $language->locale])) }}"
                        class="text-decoration-none">
                        {!! languageFlag($language->flag, $language->name) !!}
                        {{ $language->name }}
                    </a>
                @endforeach
            </div>
        @else
            <x-Core::dropdown>
                <x-slot:trigger>
                    <a class="d-flex align-items-center gap-2 dropdown-toggle text-muted text-decoration-none"
                        href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {!! languageFlag(
                            Arr::get(Language::getCurrentLagRecord(), 'flag'),
                            Arr::get(Language::getCurrentLagRecord(), 'name'),
                        ) !!}
                        {{ Arr::get(Language::getCurrentLagRecord(), 'name') }}
                    </a>
                </x-slot:trigger>

                @foreach ($languages as $language)
                    @continue($language->locale === Arr::get(Language::getCurrentLagRecord(), 'locale'))

                    <x-Core::dropdown.item :href="route(
                        Route::currentRouteName(),
                        array_merge(
                            $params ?? [],
                            $language->locale === Arr::get(Language::getCurrentLagRecord(), 'locale')
                                ? []
                                : [Language::refLangKey() => $language->locale],
                        ),
                    )" class="d-flex gap-2 align-items-center">
                        {!! languageFlag($language->flag, $language->name) !!}
                        {{ $language->name }}
                    </x-Core::dropdown.item>
                @endforeach
            </x-Core::dropdown>
        @endif
        <input name="{{ Language::refLangKey() }}" type="hidden" value="{{ Language::getRefLang() }}">
    </div>
@endif
