<td>
    <a href="{{ route('admin.menus.edit', $value->id) }}">{{ $value->name }}</a>
</td>
<td>
    {{ __($locations[$value->location] ?? '') }}
</td>
