<h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $isShowReturn ? 'Kết quả chiều về:' : 'Kết quả chiều đi:'}} {{ $products->total() }} chuyến</h1>
<div class="content-right__filters">
    <div class="content-right__filters--checked">
        @if (isset($selectedBrands) && count($selectedBrands))
            @foreach ($brands->whereIn('id', $selectedBrands) as $brand)
                <span class="remove-filter__item">
                    Nhà xe: {{ $brand->name }}
                    <span class="remove-filter" data-key="brands"
                    data-id="{{ $brand->id }}" data-attId="brands[{{ $brand->id }}]">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                    fill="#0F1729"></path>
                            </g>
                        </svg>
                    </span>
                </span>
            @endforeach
        @endif
        @if (isset($locationCheckedStarts) && count($locationCheckedStarts))
            @foreach ($locationStarts->whereIn('id', $locationCheckedStarts)->where('parent_id', 0) as $locationStartItem)
                <span class="remove-filter__item">
                    Điểm đón: {{ $locationStartItem->name }}
                    <span class="remove-filter" data-key="locationStarts"
                    data-id="{{ $locationStartItem->id }}" data-attId="locationStarts[{{ $locationStartItem->id }}]">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                    fill="#0F1729"></path>
                            </g>
                        </svg>
                    </span>
                </span>
                @php
                    $locationStartChildren = $locationStarts->where('parent_id', $locationStartItem->id);
                    $locationStartChildrenChecked = $locationStartChildren->whereIn('id', $locationCheckedStarts);
                @endphp
                @if (count($locationStartChildrenChecked) && count($locationStartChildren) && count($locationStartChildrenChecked) != count($locationStartChildren))
                    @foreach($locationStartChildrenChecked as $child)
                        <span class="remove-filter__item">
                            Điểm đón: {{ $locationStartItem->name }} - {{ $child->name }}
                            <span class="remove-filter" data-key="locationStarts"
                            data-id="{{ $child->id }}" data-attId="locationStarts[{{ $child->id }}]">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                            fill="#0F1729"></path>
                                    </g>
                                </svg>
                            </span>
                        </span>
                    @endforeach
                @endif
            @endforeach
        @endif
        @if (isset($locationCheckedReturns) && count($locationCheckedReturns))
            @foreach ($locationReturns->whereIn('id', $locationCheckedReturns)->where('parent_id', 0) as $locationReturnItem)
                <span class="remove-filter__item">
                    Điểm trả: {{ $locationReturnItem->name }}
                    <span class="remove-filter" data-key="locationReturns"
                    data-id="{{ $locationReturnItem->id }}" data-attId="locationReturns[{{ $locationReturnItem->id }}]">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                    fill="#0F1729"></path>
                            </g>
                        </svg>
                    </span>
                </span>
                @php
                    $locationReturnChildren = $locationReturns->where('parent_id', $locationReturnItem->id);
                    $locationReturnChildrenChecked = $locationReturnChildren->whereIn('id', $locationCheckedReturns);
                @endphp
                @if (count($locationReturnChildrenChecked) && count($locationReturnChildren) && count($locationReturnChildrenChecked) != count($locationReturnChildren))
                    @foreach($locationReturnChildrenChecked as $child)
                        <span class="remove-filter__item">
                            Điểm trả: {{ $locationReturnItem->name }} - {{ $child->name }}
                            <span class="remove-filter" data-key="locationReturns"
                            data-id="{{ $child->id }}" data-attId="locationReturns[{{ $child->id }}]">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                            fill="#0F1729"></path>
                                    </g>
                                </svg>
                            </span>
                        </span>
                    @endforeach
                @endif
            @endforeach
        @endif
        @if (isset($filterDetailIds) && count($filterDetailIds))
            @if (isset($filters) && count($filters))
                @foreach ($filters as $filter)
                    @php
                        $filterDetails = $filter->filterDetail;
                        if (!$filterDetails) continue;
                        $filterDetails = $filterDetails->whereIn('id', $filterDetailIds);
                    @endphp
                    @continue($filterDetails->isEmpty())
                    @foreach ($filterDetails as $filterDetail)
                        <span class="remove-filter__item">
                            {{$filter->name}}: {{ $filterDetail->name }}
                            <span class="remove-filter" data-key="filter"
                            data-id="{{ $filterDetail->id }}" data-filter-id="{{ $filter->id }}" data-attId="filter[{{ $filter->id }}][{{ $filterDetail->id }}]">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                            fill="#0F1729"></path>
                                    </g>
                                </svg>
                            </span>
                        </span>
                    @endforeach
                @endforeach
            @endif
        @endif
        @if(isset($times) && count($times))
            <span class="remove-filter__item">
                Giờ đi: {{ date("H:i", strtotime($times[0])) }} - {{ date("H:i", strtotime($times[1])) }}
                <span class="remove-filter" data-key="times">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                fill="#0F1729"></path>
                        </g>
                    </svg>
                </span>
            </span>
        @endif
        @if(isset($prices) && count($prices))
            <span class="remove-filter__item">
                Giá vé: {{ formatPrice($prices[0]) }} - {{ formatPrice($prices[1]) }}
                <span class="remove-filter" data-key="prices">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                fill="#0F1729"></path>
                        </g>
                    </svg>
                </span>
            </span>
        @endif
    </div>
</div>