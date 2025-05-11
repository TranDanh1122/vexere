@foreach($translationsCollection as $item)
    <tr>
        <td style="width: 70px; white-space: break-spaces;">{{ Language::formatGroupLang($item['group']) }}</td>
        <td style="width: 300px; white-space: break-spaces;">{{ Language::formatKeyLang($item) }}</td>
        <td style="width: 300px;white-space: break-spaces;">{{ Language::formatValueLang($item) }}</td>
    </tr>
@endforeach