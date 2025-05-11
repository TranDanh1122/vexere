<div class="content-right__lists">
    <!-- Ticket Card -->
    @if (isset($products) && count($products))
        @foreach ($products as $item)
            @php
                $product = $item->product;
                $productLocations = $product->productLocations->where('direction', $item->direction);
                $productLocation = $productLocations->where('type', 'pickup')->sortBy('order')->first();
                $productLocationEnd = $productLocations->where('type', 'dropoff')->sortBy('order')->last();
                // tính số giờ từ $productLocation->time đến $productLocationEnd->time
                if ($productLocation && $productLocationEnd) {
                    $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $productLocation->time);
                    $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $productLocationEnd->time);
                    $duration = $startTime->diffInHours($endTime);
                    $durationMinutes = $startTime->diffInMinutes($endTime);
                    $duration = $duration . 'h' . ($durationMinutes - $duration * 60) . 'm';
                }
                $locations = [];
            @endphp
            <div class="bg-white rounded-lg shadow-md border-box">
                <div class="flex flex-col md:flex-row">
                    <!-- Left: Image -->
                    @if(checkAgent() == 'web')
                        <div class="box-1-3 mb-4 md:mb-0">
                            @include('layouts.image', [
                                'src' => $product->getImage('medium'),
                                'alt' => $product->name,
                                'class' => 'rounded-lg w-full h-auto',
                            ])
                        </div>
                    @endif

                    <!-- Middle: Trip Details -->
                    <div class="box-2-3 md:px-4">
                        @if(checkAgent() == 'web')
                            <div class="flex items-center mb-2">
                                <h2 class="product-brand">{{ $product->brand->name ?? '' }}</h2>
                            </div>

                            <p class="product-name mb-4">{{ $product->name }}</p>
                        @endif
                        @if ($productLocation && $productLocationEnd)
                            @if(checkAgent() == 'web')
                                <!-- Departure Info -->
                                <div class="flex items-start mb-4">
                                    <div class="mr-2 mt-1">
                                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" fill="#484848">
                                            </circle>
                                            <circle cx="12" cy="12" r="4" fill="#EDF3FD">
                                            </circle>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center">
                                            <span
                                                class="product-time">{{ date('H:i', strtotime($productLocation->time)) }}</span>
                                            <span class="mx-2 text-gray-500">•</span>
                                            <span
                                                class="product-address">{{ $productLocation->location->name ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-time-distance">{{ $duration }}</div>
                                <!-- Arrival Info -->
                                <div class="flex items-start product-time-end">
                                    <div class="mr-2 mt-1">
                                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_29066_119690)">
                                                <path
                                                    d="M11.9999 0C6.9599 0 2.3999 3.864 2.3999 9.84C2.3999 13.824 5.6039 18.54 11.9999 24C18.3959 18.54 21.5999 13.824 21.5999 9.84C21.5999 3.864 17.0399 0 11.9999 0ZM11.9999 12C10.6799 12 9.5999 10.92 9.5999 9.6C9.5999 8.28 10.6799 7.2 11.9999 7.2C13.3199 7.2 14.3999 8.28 14.3999 9.6C14.3999 10.92 13.3199 12 11.9999 12Z"
                                                    fill="#484848"></path>
                                                <circle cx="11.9995" cy="9.59961" r="4" fill="#FDEDED"></circle>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_29066_119690">
                                                    <rect width="24" height="24" fill="white">
                                                    </rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center">
                                            <span
                                                class="product-time">{{ date('H:i', strtotime($productLocationEnd->time)) }}</span>
                                            <span class="mx-2 text-gray-500">•</span>
                                            <span
                                                class="product-address">{{ $productLocationEnd->location->name ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Departure Info -->
                                <div class="item-durations">
                                    <div class="item-duration__top flex items-center flex-row justify-between mb-1">
                                        <div class="duration-time">{{ date('H:i', strtotime($productLocation->time)) }}</div>
                                        <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" fill="#2474E5">
                                            </circle>
                                            <circle cx="12" cy="12" r="4" fill="#EDF3FD">
                                            </circle>
                                        </svg>
                                        <div class="duration-border"></div>
                                        <div class="item-duration duration-distance">{{ $duration }}</div>
                                        <div class="duration-border"></div>
                                        <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_29066_119690)">
                                                <path
                                                    d="M11.9999 0C6.9599 0 2.3999 3.864 2.3999 9.84C2.3999 13.824 5.6039 18.54 11.9999 24C18.3959 18.54 21.5999 13.824 21.5999 9.84C21.5999 3.864 17.0399 0 11.9999 0ZM11.9999 12C10.6799 12 9.5999 10.92 9.5999 9.6C9.5999 8.28 10.6799 7.2 11.9999 7.2C13.3199 7.2 14.3999 8.28 14.3999 9.6C14.3999 10.92 13.3199 12 11.9999 12Z"
                                                    fill="#EB5757"></path>
                                                <circle cx="11.9995" cy="9.59961" r="4" fill="#FDEDED"></circle>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_29066_119690">
                                                    <rect width="24" height="24" fill="white">
                                                    </rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <div class="duration-time last">{{ date('H:i', strtotime($productLocationEnd->time)) }}</div>
                                    </div>
                                    <div class="item-duration__bottom flex items-center flex-row justify-between">
                                        <div class="item-duration__address">{{ $productLocation->location->name ?? '' }}</div>
                                        <div class="item-duration__address">{{ $productLocationEnd->location->name ?? '' }}</div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    @if(checkAgent() == 'mobile')
                        <div class="product-content-top mb-4 flex flex-row items-center justify-start">
                            @include('layouts.image', [
                                'src' => $product->getImage('medium'),
                                'alt' => $product->name,
                                'class' => 'rounded-lg w-[50px] h-[50px] rounded-lg overflow-hidden',
                            ])
                            <div class="product-content-right">
                                <h2 class="product-brand">{{ $product->brand->name ?? '' }}</h2>
                                <p class="product-name mb-0">{{ $product->name }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Right: Price & Action -->
                    <div class="box-3-3 flex flex-col items-end justify-between mt-4 md:mt-0">
                        <div class="text-right">
                            <p class="text-3xl font-bold text-gray-800 product-price">
                                {{ formatPrice($product->getPrice()) }}</p>
                            @if ($product->getPriceOld())
                                <p class="text-3xl font-bold text-gray-800 product-price__old">
                                    {{ formatPrice($product->getPriceOld()) }}</p>
                            @endif
                        </div>

                        <div class="mt-4 w-full flex flex-row items-center justify-between item-action">
                            <div
                                class="open-information mt-3 text-blue-600 font-medium text-right flex items-center justify-end cursor-pointer">
                                <span class="product-view">Thông tin chi tiết</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <button
                                class="w-[90px] bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 open-popup"
                                data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
                                data-brand-name="{{ $product->brand->name ?? '' }}"
                                data-product-price="{{ $product->getPrice() }}"
                                data-product-price-format="{{ formatPrice($product->getPrice()) }}">
                                Đặt vé
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-detail">
                    <div class="product-detail__tab">
                        @if ($productLocation && $productLocationEnd)
                            <div class="product-detail__tab-item active" data-tab="product-location">
                            Đón/trả</div>
                        @endif
                        <div class="product-detail__tab-item" data-tab="product-policy">Chính sách
                        </div>
                        @if (count($product->getSlides()))
                            <div class="product-detail__tab-item" data-tab="product-images">Hình ảnh</div>
                        @endif
                    </div>
                    <div class="product-detail__content">
                        @if ($productLocation && $productLocationEnd)
                            <div class="product-detail__content-item product-location active">
                                <div class="product-location__info">
                                    <p class="text-primary-color">Lưu ý</p>
                                    <p class="product-location__info--desc">Các mốc thời gian đón, trả bên
                                        dưới là thời gian dự kiến.<br />Lịch này có thể thay đổi tùy tình
                                        hình thưc tế.</p>
                                </div>
                                <div class="flex items-start mb-2">
                                    <div class="location-item">
                                        <div class="title-tab">Điểm đón</div>
                                        <div class="location-item__list">
                                            @foreach ($productLocations as $location)
                                                @if ($location->type == 'pickup')
                                                    @php
                                                        $name = $location->location->name ?? '';
                                                        $time = date('H:i', strtotime($location->time));
                                                        $locations[] = [
                                                            'type' => 'pickup',
                                                            'id' => $location->id,
                                                            'name' => $name . ($location->transit ? ' (Có trung chuyển)' : ''),
                                                            'address' => $location->location->address ?? '',
                                                            'time' => $time,
                                                            'transit' => $location->transit,
                                                        ];
                                                    @endphp
                                                    <div class="location-item__content flex items-center flex-nowrap">
                                                        <span
                                                            class="product-time">{{ $time }}</span>
                                                        <span class="mx-2 text-gray-500">•</span>
                                                        <span
                                                            class="product-address-item">
                                                            {{ $name }}
                                                            {!! $location->transit ? '<span class="small">(Có trung chuyển)</span>' : '' !!}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="location-item">
                                        <div class="title-tab">Điểm trả</div>
                                        <div class="location-item__list">
                                            @foreach ($productLocations as $location)
                                                @if ($location->type == 'dropoff')
                                                    @php
                                                        $name = $location->location->name ?? '';
                                                        $time = date('H:i', strtotime($location->time));
                                                        $locations[] = [
                                                            'type' => 'dropoff',
                                                            'id' => $location->id,
                                                            'name' => $name . ($location->transit ? ' (Có trung chuyển)' : ''),
                                                            'address' => $location->location->address ?? '',
                                                            'time' => $time,
                                                            'transit' => $location->transit,
                                                        ];
                                                    @endphp
                                                    <div class="location-item__content flex items-center flex-nowrap">
                                                        <span
                                                            class="product-time">{{ $time }}</span>
                                                        <span class="mx-2 text-gray-500">•</span>
                                                        <span
                                                            class="product-address-item">
                                                            {{ $name }}
                                                            {!! $location->transit ? '<span class="small">(Có trung chuyển)</span>' : '' !!}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="info-location" data-locations='{!! json_encode($locations) !!}'></div>
                                </div>
                            </div>
                        @endif
                        <div class="product-detail__content-item product-policy">
                            <div class="title-tab">Chính sách nhà xe</div>
                            <div class="ck-content">{!! $product->detail !!}</div>
                        </div>
                        <div class="product-detail__content-item product-images">
                            @if (count($product->getSlides()))
                                <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                    class="swiper mySwiper2">
                                    <div class="swiper-wrapper">
                                        @foreach ($product->getSlides() as $image)
                                            <div class="swiper-slide">
                                                <img src="{{ getImage($image, 'products', 'large') }}" />
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                                <div thumbsSlider="" class="swiper mySwiper">
                                    <div class="swiper-wrapper">
                                        @foreach ($product->getSlides() as $image)
                                            <div class="swiper-slide">
                                                <img src="{{ getImage($image, 'products', 'small') }}" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="close-information">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                            <g id="Group_4773" data-name="Group 4773" transform="translate(-1055 -590)">
                                <g id="Ellipse_1606" data-name="Ellipse 1606" transform="translate(1055 590)"
                                    fill="#fff" stroke="#f10000" stroke-width="1.5">
                                    <circle cx="10" cy="10" r="10" stroke="none" />
                                    <circle cx="10" cy="10" r="9.25" fill="none" />
                                </g>
                                <line id="Line_881" data-name="Line 881" x2="8" y2="8"
                                    transform="translate(1061 596)" fill="none" stroke="#f10000"
                                    stroke-width="1.5" />
                                <line id="Line_882" data-name="Line 882" y1="8" x2="8"
                                    transform="translate(1061 596)" fill="none" stroke="#f10000"
                                    stroke-width="1.5" />
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
        @if ($products->hasMorePages())
            <div class="flex justify-center mt-4">
                <button class="load-more w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700" data-page="{{ $products->currentPage() + 1 }}">
                    Xem thêm chuyến
                </button>
            </div>
        @endif
    @endif
</div>
