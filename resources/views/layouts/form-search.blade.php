<div class="container banner-container">
    <div class="booking-container">
        <div class="tabs">
            <div class="tab active">
                <svg width="28" viewBox="0 0 24 24">
                    <path
                        d="M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z">
                    </path>
                </svg> Xe khách
            </div>
        </div>

        <div class="form-container">
            <form id="booking-form" class="form-container__form">
                @if (empty($dataRequest['from'] ?? '') || ($dataRequest['from'] ?? '') === 'saigon')
                    @php
                        $dataRequest['departureName'] = 'Sài Gòn';
                        $dataRequest['destinationName'] = 'Vũng Tàu';
                    @endphp
                    <input type="hidden" id="departure" name="departure" value="saigon" data-name="Sài Gòn">
                    <input type="hidden" id="destination" name="destination" value="vungtau" data-name="Vũng Tàu">
                @else
                    @php
                        $dataRequest['departureName'] = 'Vũng Tàu';
                        $dataRequest['destinationName'] = 'Sài Gòn';
                    @endphp
                    <input type="hidden" id="departure" name="departure" value="vungtau" data-name="Vũng Tàu">
                    <input type="hidden" id="destination" name="destination" value="saigon" data-name="Sài Gòn">
                @endif
                <div class="form-row form-input">
                    <div class="form-row">
                        <div class="location-inputs">
                            <div class="input-group">
                                <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="#2474E5" />
                                    <circle cx="12" cy="12" r="4" fill="#EDF3FD" />
                                </svg>
                                <div class="input-group__content">
                                    <span class="input-label">Nơi xuất phát</span>
                                    <input type="text" class="input-field" id="departure-display" value="{{ $dataRequest['departureName']}}"
                                        readonly>
                                </div>
                            </div>

                            <div class="swap-btn" id="swap-locations">
                                <svg class="input-icon-switch" fill="#FDEDED" width="24" height="24"
                                    viewBox="0 0 24 24" id="up-down-left-right-arrow" data-name="Flat Line"
                                    xmlns="http://www.w3.org/2000/svg" class="icon flat-line">
                                    <polyline id="primary" points="16 15 20 15 20 11"
                                        style="fill: none; stroke: #2474E5; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;" />
                                    <polyline id="primary-2" data-name="primary" points="8 9 4 9 4 13"
                                        style="fill: none; stroke: #2474E5; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;" />
                                    <path id="primary-3" data-name="primary" d="M9,4,20,15M4,9,15,20"
                                        style="fill: none; stroke: #2474E5; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;" />
                                </svg>
                            </div>

                            <div class="input-group">
                                <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_29066_119690)">
                                        <path
                                            d="M11.9999 0C6.9599 0 2.3999 3.864 2.3999 9.84C2.3999 13.824 5.6039 18.54 11.9999 24C18.3959 18.54 21.5999 13.824 21.5999 9.84C21.5999 3.864 17.0399 0 11.9999 0ZM11.9999 12C10.6799 12 9.5999 10.92 9.5999 9.6C9.5999 8.28 10.6799 7.2 11.9999 7.2C13.3199 7.2 14.3999 8.28 14.3999 9.6C14.3999 10.92 13.3199 12 11.9999 12Z"
                                            fill="#EB5757" />
                                        <circle cx="11.9995" cy="9.59961" r="4" fill="#FDEDED" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_29066_119690">
                                            <rect width="24" height="24" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <div class="input-group__content">
                                    <span class="input-label">Nơi đến</span>
                                    <input type="text" class="input-field" id="destination-display" value="{{ $dataRequest['destinationName']}}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-row__date">
                        <div class="date-inputs {{ !empty($toDate ?? '') ? 'active' : '' }}">
                            <div class="input-group">
                                <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19 4H18V2H16V4H8V2H6V4H5C3.89 4 3.01 4.9 3.01 6L3 20C3 21.1 3.89 22 5 22H19C20.1 22 21 21.1 21 20V6C21 4.9 20.1 4 19 4ZM19 20H5V10H19V20ZM19 8H5V6H19V8ZM12 13H17V18H12V13Z"
                                        fill="#2474E5" />
                                </svg>
                                <div class="input-group__content">
                                    <span class="input-label">Ngày đi</span>
                                    <input type="text" class="date-input" id="departure-date"
                                        placeholder="Chọn ngày đi" value="{{ $dataRequest['fromDate'] ?? '' }}" data-date="{{ $fromDate ?? '' }}" readonly>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-group__show add-date-return">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    <span class="input-label">Thêm ngày về</span>
                                </div>
                                <div class="input-group__hidden">
                                    <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19 4H18V2H16V4H8V2H6V4H5C3.89 4 3.01 4.9 3.01 6L3 20C3 21.1 3.89 22 5 22H19C20.1 22 21 21.1 21 20V6C21 4.9 20.1 4 19 4ZM19 20H5V10H19V20ZM19 8H5V6H19V8ZM12 13H17V18H12V13Z"
                                            fill="#2474E5" />
                                    </svg>
                                    <div class="input-group__content">
                                        <span class="input-label">Ngày về <span class="remove-date"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-x">
                                                    <line x1="18" y1="6" x2="6"
                                                        y2="18" />
                                                    <line x1="6" y1="6" x2="18"
                                                        y2="18" />
                                                </svg></span></span>
                                        <input type="text" class="date-input" id="return-date"
                                            placeholder="Chọn ngày về" value="{{ $dataRequest['toDate'] ?? '' }}" data-date="{{ $toDate ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <button type="submit" class="search-btn">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>
</div>
