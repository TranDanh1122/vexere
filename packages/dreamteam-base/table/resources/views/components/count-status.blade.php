<div style="display: flex; flex-wrap: wrap;align-items: center;justify-content: flex-start;margin-bottom: 1rem;">
    <div style="display: inline-flex; margin-right: 1rem;">
        <p style="margin-bottom: 0"><a href="{{ route('admin.'.$tableName.'.index') }}">{{ trans('Core::admin.general.all') }} ({{ $datas->sum('total') }})</a></p>
    </div>
    <span style="display: inline-flex; margin-right: 1rem;">|</span>
    <div style="display: inline-flex; margin-right: 1rem;">
        <a href="{{ route('admin.'.$tableName.'.index', ['search' => 1, 'status' => 1]) }}">{{ trans('Translate::table.active') }} ({{ $datas->where('status', \DreamTeam\Base\Enums\BaseStatusEnum::ACTIVE)->first()?->total ?? 0 }})</a>
    </div>
    <span style="display: inline-flex; margin-right: 1rem;">|</span>
    <div style="display: inline-flex; margin-right: 1rem;">
        <a href="{{ route('admin.'.$tableName.'.index', ['search' => 1, 'status' => 0]) }}">{{ trans('Translate::table.no_active') }} ({{ $datas->where('status', \DreamTeam\Base\Enums\BaseStatusEnum::DEACTIVE)->first()?->total ?? 0 }})</a>
    </div>
    <span style="display: inline-flex; margin-right: 1rem;">|</span>
    <div style="display: inline-flex; margin-right: 1rem;">
        <a href="{{ route('admin.'.$tableName.'.index', ['search' => 1, 'status' => 2]) }}">{{ trans('Core::admin.general.draf') }} ({{ $datas->where('status', \DreamTeam\Base\Enums\BaseStatusEnum::DRAFT)->first()?->total ?? 0 }})</a>
    </div>
    <span style="display: inline-flex; margin-right: 1rem;">|</span>
    <div style="display: inline-flex; margin-right: 1rem;">
        <a href="{{ route('admin.'.$tableName.'.index', ['search' => 1, 'status' => -1, 'trash' => true]) }}">{{ trans('Translate::table.trash_name') }} ({{ $datas->where('status', \DreamTeam\Base\Enums\BaseStatusEnum::DELETE)->first()?->total ?? 0 }})</a>
    </div>
</div>