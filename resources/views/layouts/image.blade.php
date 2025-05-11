{{-- @include('Theme::general.components.image', [
    'src' => $config_general['logo_header']??'',
    'width' => '150px',
    'height' => '40px'
    'lazy'   => true,
    'title'  => ''
]) --}}

<img @if (isset($lazy) && $lazy == true) loading="lazy" @endif
    src="{{ addWebp(RvMedia::url($src ?? RvMedia::getDefaultImage())) }}" alt="{{ $alt ?? getAlt($src ?? '') }}"
    @if (isset($title) && !empty($title)) title="{{ $title ?? '' }}" @endif
    @if (isset($width) && !empty($width)) width="{{ $width ?? '' }}" @endif
    @if (isset($height) && !empty($height)) height="{{ $height ?? '' }}" @endif
    @if (isset($origin) && !empty($origin)) data-origin="{{ $origin }}" @endif
    @if (isset($class) && !empty($class)) class="{{ $class }}" @endif
>
