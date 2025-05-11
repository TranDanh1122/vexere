@extends('Core::layouts.app')

@section('title')
    @lang($module_name ?? '')
@endsection
@section('content')
    <div class="setting-group">
        @foreach($menuConfigs as $menuConfig)
            @continue(!count($menuConfig['childs']))
            <div class=" mx-auto bg-white border border-gray-300 rounded-md mb-4">
                <h2 class="text-lg font-bold mb-4 border-b border-gray-300 pl-6 pt-3 pb-3">{{ trans($menuConfig['name']) }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pl-6 pr-6 pt-3 pb-3">
                    @foreach($menuConfig['childs'] as $child)
                        @if (checkRole($child['role']) && (!isset($roleDisable) || !in_array($child['role'], $roleDisable)))
                            <div class="flex items-start space-x-3">
                                <div>
                                    <h5 class="text-blue-600 font-semibold"><a href="{{ route($child['route']) }}">{{ trans($child['name']) }}</a></h5>
                                    <p class="text-sm text-gray-500">{{ $child['description'] ? trans($child['description']) : trans('Core::tables.desc_setting', ['name' => trans($child['name'])]) }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
