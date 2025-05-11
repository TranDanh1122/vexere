@php
    $supportedLocales = config('app.language');
    $options = [
        'before' => '',
        'flag' => true,
        'name' => true,
        'name_top' => true,
        'flag_top' => true,
        'top_class' => '',
        'loop_class' => '',
        'class' => '',
        'style' => '',
        'after' => '',
        'default_class' => true,
        ...$options ?? [],
    ];
    $link = $language['language'] ?? [];
@endphp

@if (Language::isHasMultipleLanguage() && count($supportedLocales))
    <div class="{{ $options['default_class'] ? 'language-wrapper' : '' }}">
        @if (Language::isShowDropdown())
            {!! Arr::get($options, 'before') !!}
            <div class="dropdown">
                <div class="btn-lang btn-secondary dropdown-toggle flex items-center {{ $options['top_class'] }}" style="cursor:pointer;" data-bs-toggle="dropdown"
                    type="button" aria-haspopup="true" aria-expanded="true"
                    aria-label="{{ $supportedLocales[$lang]['name'] }}">
                    @if ($options['flag_top'])
                        @include('Theme::general.components.image', [
                            'src' => $supportedLocales[$lang]['flag'],
                            'alt' => $supportedLocales[$lang]['name'],
                            'width' => 20,
                            'height' => 20,
                            'lazy' => true,
                        ])
                    @endif
                    @if ($options['name_top'])
                        <span style="margin-left: 6px">{{ $supportedLocales[$lang]['name'] }}</span>
                    @endif
                </div>
                <ul class="dropdown-menu language_bar_chooser {{ Arr::get($options, 'class') }}"
                    style="{{ $options['style'] }}">
                    @foreach ($supportedLocales as $localeCode => $properties)
                        @if ($localeCode != $lang)
                            <li class="{{ $options['loop_class'] }}">
                                <a href="{{ $link[$localeCode] ?? '/' }}" class="flex items-center" aria-label="{{ $properties['name'] }}">
                                    @if ($options['flag'])
                                        @include('Theme::general.components.image', [
                                            'src' => $properties['flag'],
                                            'alt' => $properties['name'],
                                            'width' => 20,
                                            'height' => 20,
                                            'lazy' => true,
                                        ])
                                    @endif
                                    @if ($options['name'])
                                        <span>{{ $properties['name'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            {!! Arr::get($options, 'after') !!}
        @else
            <ul class="language_bar_list {{ Arr::get($options, 'class') }}">
                @foreach ($supportedLocales as $localeCode => $properties)
                    @if ($localeCode != $lang)
                        <li>
                            <a href="{{ $link[$localeCode] ?? '/' }}" aria-label="{{ $properties['name'] }}">
                                @if ($options['flag'])
                                    @include('Theme::general.components.image', [
                                        'src' => $properties['flag'],
                                        'alt' => $properties['name'],
                                        'width' => 20,
                                        'height' => 20,
                                        'lazy' => true,
                                    ])
                                @endif
                                @if ($options['name'])
                                    <span>{{ $properties['name'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif
    </div>
@endif
