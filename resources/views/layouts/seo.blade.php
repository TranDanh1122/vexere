{{--
	title: Lấy theo title tại biến MetaSeo
	Tiêu đề của trang
--}}
	<title>{{ $meta_seo['title'] ?? '' }}</title>

{{--
	description: Lấy theo description tại biến MetaSeo
	Mô tả của trang
--}}
@if(!empty($meta_seo['description']))
	<meta name="description" content="{{ ($meta_seo['description'] ?? '') }}"/>
@endif

{{--
	robots: Lấy theo robots tại biến MetaSeo
	có cho phép Google đánh index hay không
--}}
{{-- kiểm tra nếu cấu hình chặn từ admin thì luôn chặn --}}
@if(isset($configReading['no_index']) && $configReading['no_index'] == 1)
    <meta name="robots" content="noindex,nofollow"/>
@else
    @if(isset($meta_seo['robots']) && !empty($meta_seo['robots']))
    	<meta name="robots" content="{{ $meta_seo['robots'] ?? '' }}"/>
    @endif
@endif

{{--
	canonical: Lấy theo url tại biến MetaSeo
	Dùng để định danh trang là duy nhất tại Domain
--}}
@if(!empty($meta_seo['url']))
	<link rel="canonical" href="{{ $meta_seo['url'] }}"/>
@endif

{{--
	hreflang với website đa ngôn ngữ
	$switch_language = [
		'lang_key_1' => 'lang_link_1',
		'lang_key_2' => 'lang_link_2',
	];
	Chỉ hiển thị nếu page không cấu hình canonical riêng
--}}
@if (isset($switch_language) && !empty($switch_language) && (!($meta_seo['is_custom_canonical'] ?? false)))
	@foreach ($switch_language as $lang_key => $lang_link)
		<link rel="alternate" hreflang="{{ $lang_key ?? '' }}" href="{{ $lang_link ?? '' }}">
	@endforeach
@endif

{{-- Facebook Meta Tags --}}
@if (!empty($meta_seo['locale']))
	<meta property="og:locale" content="{{ $meta_seo['locale'] }}"/>
@endif
	<meta property="og:site_name" content="{{ $configOverview['name_company'] ?? config('app.name') }}"/>
	<meta property="og:type" content="{{ $meta_seo['type'] ?? 'website' }}"/>
@if(!empty($meta_seo['title']))
	<meta property="og:title" content="{{ $meta_seo['title'] ?? '' }}"/>
@endif
@if(!empty($meta_seo['description']))
	<meta property="og:description" content="{{ $meta_seo['description'] }}"/>
@endif
@if(!empty($meta_seo['url']))
	<meta property="og:url" content="{{ $meta_seo['url'] }}"/>
@endif
@if(!empty($meta_seo['social_image'] ?? $meta_seo['image'] ?? ''))
	<meta property="og:image" content="{{ RvMedia::url($meta_seo['social_image'] ?? $meta_seo['image']) }}"/>
@endif

{{-- Twitter Meta Tags --}}
	<meta name="twitter:card" content="summary_large_image">
@if(!empty($meta_seo['social_title'] ?? $meta_seo['title'] ?? ''))
	<meta name="twitter:title" content="{{ $meta_seo['social_title'] ?? $meta_seo['title'] }}">
@endif
@if(!empty($meta_seo['social_description'] ?? $meta_seo['description'] ?? ''))
	<meta name="twitter:description" content="{{ ($meta_seo['social_description'] ?? $meta_seo['description']) }}">
@endif
@if(!empty($meta_seo['social_image'] ?? $meta_seo['image'] ?? ''))
	<meta name="twitter:image" content="{{ RvMedia::url($meta_seo['social_image'] ?? $meta_seo['image']) }}"/>
@endif
