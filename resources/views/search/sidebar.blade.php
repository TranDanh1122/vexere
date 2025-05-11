@if (checkAgent() == 'mobile')
    <div class="cursor-pointer close-sidebar" >
        <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="#0F1729"></path> </g></svg>
    </div>
@endif
<div class="sidebar bg-white">
    <!-- Location Section -->
    <div class="sidebar-section">
        <div class="section-header">
            <h3 class="font-medium text-gray-800 text-lg">Lọc</h3>
            <a href="" class="text-blue-500 text-sm">Xóa lọc</a>
        </div>
    </div>

    <!-- Time Section -->
    <div class="sidebar-section">
        <div class="section-header" id="timeHeader">
            <h3 class="font-medium text-gray-800">Giờ đi</h3>
            <button class="transform transition-transform duration-200 button-toggle" id="timeToggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                    <path d="m18 15-6-6-6 6" />
                </svg>
            </button>
        </div>
        <div class="section-content" id="timeContent">
            <div id="time-slider"></div>
            <div class="time-picker">
                <span class="time-picker__box">
                    <span>Từ</span>
                    <input type="text" class="time-input" id="start-time" value="00:00" readonly>
                </span>
                <span>-</span>
                <span class="time-picker__box">
                    <span>Đến</span>
                    <input type="text" class="time-input" id="end-time" value="24:00" readonly>
                </span>
            </div>
        </div>
    </div>

    <!-- Transport Companies Section -->
    @if (isset($brands) && count($brands))
        <div class="sidebar-section">
            <div class="section-header" id="companiesHeader">
                <div class="flex items-center">
                    <h3 class="font-medium text-gray-800">Nhà xe</h3>
                    <span
                        class="ml-1 bg-red-500 text-white text-xs font-medium px-1.5 py-0.5 rounded-full hidden"></span>
                </div>
                <button class="transform transition-transform duration-200 button-toggle"
                    id="companiesToggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                        <path d="m18 15-6-6-6 6" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="companiesContent">
                <input type="text" class="search-input" id="company-search"
                    placeholder="Tìm trong danh sách">
                <div class="companies-list max-h-52 overflow-y-auto">
                    @foreach ($brands as $brand)
                        <div class="company-item">
                            <input type="checkbox" id="brands[{{ $brand->id }}]"
                                value="{{ $brand->id }}" class="mr-2">
                            <label for="brands[{{ $brand->id }}]"
                                class="flex-1">{{ $brand->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if (isset($locationStarts) && count($locationStarts))
        <div class="sidebar-section">
            <div class="section-header" id="companiesHeader">
                <div class="flex items-center">
                    <h3 class="font-medium text-gray-800">Điểm đón</h3>
                    <span
                        class="ml-1 bg-red-500 text-white text-xs font-medium px-1.5 py-0.5 rounded-full hidden"></span>
                </div>
                <button class="transform transition-transform duration-200 button-toggle"
                    id="companiesToggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                        <path d="m18 15-6-6-6 6" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="companiesContent">
                <input type="text" class="search-input" id="company-search"
                    placeholder="Tìm trong danh sách">
                <div class="companies-list max-h-52 overflow-y-auto">
                    @foreach ($locationStarts->where('parent_id', 0) as $locationStart)
                        <div class="filter-item">
                            <div class="company-item parent">
                                @if (count($locationStarts->where('parent_id', $locationStart->id)))
                                <span class="open-child">
                                    <svg viewBox="0 0 1024 1024" class=""
                                        data-icon="caret-down" width="1em" height="1em"
                                        fill="currentColor" aria-hidden="true" focusable="false">
                                        <path
                                            d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z">
                                        </path>
                                    </svg>
                                </span>
                                @endif
                                <input type="checkbox" id="locationStarts[{{ $locationStart->id }}]"
                                    value="{{ $locationStart->id }}" class="mr-2">
                                <label for="locationStarts[{{ $locationStart->id }}]"
                                    class="flex-1 items-center">
                                    {{ $locationStart->name }}
                                </label>
                            </div>
                            <div class="list-child">
                                @foreach ($locationStarts->where('parent_id', $locationStart->id) as $locationStart)
                                    <div class="company-item">
                                        <input type="checkbox"
                                            id="locationStarts[{{ $locationStart->id }}]"
                                            value="{{ $locationStart->id }}" class="mr-2">
                                        <label for="locationStarts[{{ $locationStart->id }}]"
                                            class="flex-1">{{ $locationStart->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if (isset($locationReturns) && count($locationReturns))
        <div class="sidebar-section">
            <div class="section-header" id="companiesHeader">
                <div class="flex items-center">
                    <h3 class="font-medium text-gray-800">Điểm trả</h3>
                    <span
                        class="ml-1 bg-red-500 text-white text-xs font-medium px-1.5 py-0.5 rounded-full hidden"></span>
                </div>
                <button class="transform transition-transform duration-200 button-toggle"
                    id="companiesToggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                        <path d="m18 15-6-6-6 6" />
                    </svg>
                </button>
            </div>
            <div class="section-content" id="companiesContent">
                <input type="text" class="search-input" id="company-search"
                    placeholder="Tìm trong danh sách">
                <div class="companies-list max-h-52 overflow-y-auto">
                    @foreach ($locationReturns->where('parent_id', 0) as $locationReturn)
                        <div class="filter-item">
                            <div class="company-item parent">
                                @if (count($locationReturns->where('parent_id', $locationReturn->id)))
                                <span class="open-child">
                                    <svg viewBox="0 0 1024 1024" class=""
                                        data-icon="caret-down" width="1em" height="1em"
                                        fill="currentColor" aria-hidden="true" focusable="false">
                                        <path
                                            d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z">
                                        </path>
                                    </svg>
                                </span>
                                @endif
                                <input type="checkbox" id="locationReturns[{{ $locationReturn->id }}]"
                                    value="{{ $locationReturn->id }}" class="mr-2">
                                <label for="locationReturns[{{ $locationReturn->id }}]"
                                    class="flex-1 items-center">
                                    {{ $locationReturn->name }}
                                </label>
                            </div>
                            <div class="list-child">
                                @foreach ($locationReturns->where('parent_id', $locationReturn->id) as $locationReturn)
                                    <div class="company-item">
                                        <input type="checkbox"
                                            id="locationReturns[{{ $locationReturn->id }}]"
                                            value="{{ $locationReturn->id }}" class="mr-2">
                                        <label for="locationReturns[{{ $locationReturn->id }}]"
                                            class="flex-1">{{ $locationReturn->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <!-- Price Section -->
    <div class="sidebar-section">
        <div class="section-header" id="priceHeader">
            <h3 class="font-medium text-gray-800">Giá vé</h3>
            <button class="transform transition-transform duration-200 button-toggle"
                id="priceToggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                    <path d="m18 15-6-6-6 6" />
                </svg>
            </button>
        </div>
        <div class="section-content" id="priceContent">
            <div id="price-slider"></div>
            <div class="price-picker">
                <span class="price-picker__box">
                    <input type="text" class="price-input" id="min-price" value="0 ₫"
                        readonly>
                </span>
                <span class="price-picker__box">
                    <input type="text" class="price-input" id="max-price" value="2.000.000 ₫"
                        readonly>
                </span>
            </div>
        </div>
    </div>
    {{-- Filter --}}
    @if (isset($filters) && count($filters))
        @foreach ($filters as $filter)
            @continue($filter->filterDetail->isEmpty())
            <div class="sidebar-section">
                <div class="section-header" id="filterHeader">
                    <h3 class="font-medium text-gray-800">{{ $filter->name }}</h3>
                    <button class="transform transition-transform duration-200 button-toggle"
                        id="filterToggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-gray-400">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="section-content" id="filterContent">
                    <input type="text" class="search-input" id="company-search"
                        placeholder="Tìm trong danh sách">
                    <div class="companies-list max-h-52 overflow-y-auto">
                        @foreach ($filter->filterDetail as $option)
                            <div class="filter-item company-item">
                                <input type="checkbox"
                                    id="filter[{{ $filter->id }}][{{ $option->id }}]"
                                    value="{{ $option->id }}" class="mr-2">
                                <label for="filter[{{ $filter->id }}][{{ $option->id }}]"
                                    class="flex-1">{{ $option->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>