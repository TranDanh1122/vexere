<{{$tag}} @if (isset($attributes) && !is_null($attributes))
    @foreach ($attributes as $attKey => $attValue)
        {{ $attKey }}="{{ $attValue }}"
    @endforeach
    @endif
    >
