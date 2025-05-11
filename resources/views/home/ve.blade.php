        @if( $customer)
        @foreach ($customer->order as $order)
        <div class="relative flex items-start justify-between bg-gray-100 border group text-neutral-800 w-full p-3 border-neutral-500 hover:shadow-lg rounded-md hover:shadow-neutral-200">
            <div class="w-1/2">
                <h2 class="text-sm font-bold ">Tên: {{ $customer->name ?? '' }}</h2>
                <p class="text-sm">Sđt: {{$customer-> phone ?? ''}}</p>
                <p class="text-sm font-medium">Cccd: {{$customer -> cccd ?? ''}}</p>
            </div>
            <div class="w-1/2">
                <p class="text-sm font-medium"> Chuyến: {{$order->orderDetail->first()->direction == "vt_sg" ? "Vũng Tàu - Sài Gòn" : "Sài Gòn - Vũng Tàu"}} ({{$order->orderDetail->first()->product->brand->name}})</p>
                <p class="text-sm font-medium"> Thời gian: {{date('d/m/Y' , strtotime($order->orderDetail->first()->start_date ?? 'now')) }}</p>
                <p class="text-sm font-medium">Đón tại: {{$order->orderDetail->first()->productLocation->location->name ?? ''}}</p>
                <p class="text-sm font-medium">Xuống xe: {{$order->orderDetail->first()->productLocationReturn->location->name ?? ''}}</p>
            </div>
            <a href="{{ route('app.ajax.ticket.download', $order->code) }}"  class="search-button w-[90px] bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 ml-auto">Tải</a>
        </div>
        @endforeach
        @endif