<div class="popup " id="popup-booking">
    <div class="overlay fixed inset-0 bg-gray-900 opacity-50 z-10"></div>
    <div class="popup-content rounded-2xl p-0 shadow-sm ">
        <div class="flex justify-between items-center px-4 py-4">
            <div class="text-xl font-bold">Đặt vé xe</div>
            <div class="cursor-pointer close-popup" id="close-popup-booking">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="#0F1729"></path> </g></svg>
            </div>
        </div>
        <div class="popup-body pt-0 border border-gray-200 rounded-lg p-4 flex-1 flex flex-row gap-5 items-start justify-between">
            <div class="flex flex-col gap-2">
                <div class="text-lg font-bold brand-name"></div>
                <div class="text-sm text-gray-500"><span class="product-name"></span></div>
                <div class="text-sm text-gray-500">Từ: <span class="from"></span></div>
                <div class="text-sm text-gray-500">Đến: <span class="to"></span></div>
                <div class="text-sm text-gray-500">Thời gian: <span class="time"></span></div>
                <div class="text-sm text-gray-500">Giá vé: <span class="product-price"></span></div>  
            </div>
            <form class="form-booking">
                <input type="hidden" name="product_id" value="0" class="product-id">
                <div class="mb-4">
                    <label for="from" class="block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input type="text" id="from" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập họ và tên">
                </div>
                <div class="mb-4">
                    <label for="to" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input type="text" id="to" name="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập số điện thoại">
                </div>
                  <div class="mb-4">
                    <label for="cccd" class="block text-sm font-medium text-gray-700">Số căn cước</label>
                    <small>Lưu ý: Hành khách phải cung cấp căn cước công dân (bản chính hoặc bản sao có công chứng) khớp với thông tin này khi lên xe</small>
                    <input type="text" id="cccd" name="cccd" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập số căn cước">
                <div class="pickup-list location-wrapper">
                    <label for="from" class="block text-sm font-medium text-gray-700">Điểm đón</label>
                    <select name="pickup" id="pickup" class="mb-4 select-location mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Chọn điểm đón</option>
                    </select>
                    <div class="pickup-detail hidden location-detail mb-4">
                        <label for="pickup-detail" class="block text-sm font-medium text-gray-700">Điểm đón chi tiết</label>
                        <textarea type="text" id="pickup-detail" name="pickup_detail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập điểm đón chi tiết"></textarea>
                    </div>
                </div>
                <div class="dropoff-list location-wrapper">
                    <label for="from" class="block text-sm font-medium text-gray-700">Điểm trả</label>
                    <select name="dropoff" id="dropoff" class="mb-4 select-location mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Chọn điểm trả</option>
                    </select>
                    <div class="dropoff-detail hidden location-detail mb-4">
                        <label for="dropoff-detail" class="block text-sm font-medium text-gray-700">Điểm trả chi tiết</label>
                        <textarea type="text" id="dropoff-detail" name="dropoff_detail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập điểm trả chi tiết"></textarea>
                    </div>
                </div>
                {{-- số lượng --}}
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                    {{-- Hiển thị ô input bên trái, tính tổng tiền bên phải --}}
                    <div class="flex items-center item-quantity">
                        <input type="hidden" name="price" value="0" class="product-price">
                        {{-- style 2 button 2 bên cho cộng trừ só lượng --}}
                        <button type="button" class="btn-qty btn-minus bg-gray-200 text-gray-700 rounded-l-md px-4 py-2 hover:bg-gray-300 decrease-quantity">-</button>
                        <input type="number" id="quantity" name="quantity" class="quantity mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-center" placeholder="Nhập số lượng" min="1" value="1">
                        <button type="button" class="btn-qty btn-plus bg-gray-200 text-gray-700 rounded-r-md px-4 py-2 hover:bg-gray-300 increase-quantity">+</button>
                    </div>
                    {{-- // hiển thị thổng tiền --}}
                    <div class="flex justify-start mt-2 gap-5">
                        <span class="text-sm text-gray-500 font-bold">Tổng tiền:</span>
                        <span class="text-sm text-gray-500 total-price font-bold">0</span>
                    </div>
                </div>
                <p class="error text-sm italic text-red-500 mt-2 mb-2"></p>
                <button type="submit" class="btn-place-order w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Đặt vé</button>
            </form>
        </div>
    </div>
</div>