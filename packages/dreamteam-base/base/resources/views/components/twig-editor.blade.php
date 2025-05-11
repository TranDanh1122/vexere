@props([
    'variables' => [],
    'functions' => [],
    'value' => null,
    'name' => null,
    'mode' => 'html',
    'helperText' => null,
])

<div class="twig-template">
    <div class="mb-3 btn-list">
        @if (! empty($variables))
            <x-Core::dropdown
                :label="__('Variables')"
                icon="ti ti-code"
            >
                @foreach ($variables as $key => $label)
                    <x-Core::dropdown.item
                        data-bb-toggle="twig-variable"
                        :data-key="$key"
                    >
                        <span class="text-danger">{{ $key }}</span>: {{ trans($label) }}
                    </x-Core::dropdown.item>
                @endforeach
            </x-Core::dropdown>
        @endif

        @if (! empty($functions))
            <x-Core::dropdown
                :label="__('Functions')"
                icon="ti ti-code"
            >
                @foreach ($functions as $key => $function)
                    <x-Core::dropdown.item
                        data-bb-toggle="twig-function"
                        :data-key="$key"
                        :data-sample="$function['sample']"
                    >
                        <span class="text-danger">{{ $key }}</span>: {{ trans($function['label']) }}
                    </x-Core::dropdown.item>
                @endforeach
            </x-Core::dropdown>
        @endif
    </div>

    <x-Core::form-group>
        <x-Core::form.code-editor
            :name="$name"
            :value="$value"
            :mode="$mode"
        >
            <x-slot:helper-text>
                @if($helperText)
                    {{ $helperText }}
                @else
                    {!! BaseHelper::clean(
                        __('Learn more about Twig template: :url', [
                            'url' => \DreamTeam\Base\Facades\Html::link('https://twig.symfony.com/doc/3.x/', null, ['target' => '_blank']),
                        ]),
                    ) !!}
                @endif
            </x-slot:helper-text>
        </x-Core::form.code-editor>
    </x-Core::form-group>
</div>
