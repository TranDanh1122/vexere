@extends('Core::layouts.app')

@section('title') @lang('Ecommerce::order.order_detail')  @endsection
@section('content')
<div class="row">
	<div class="col-lg-6 col-md-12">
		{{-- Đơn hàng --}}
		<div class="card">
			<div class="card-body p-3">
				<h4 class="card-title">@lang('Ecommerce::order.order_infor')</h4>
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="p-2" style="width: 200px;">@lang('Ecommerce::order.order_code')</th>
							<td class="p-2">{{$order->code??''}}</td>
						</tr>
						<tr>
							<th class="p-2" style="width: 200px;">@lang('Ecommerce::order.order_total')</th>
							<td class="p-2">{{$order->getTotalPrice()}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		{{-- Khách --}}
		@if (isset($customers) && !empty($customers))
			<div class="card">
				<div class="card-body p-3">
					<div>
						<h4 class="card-title">@lang('Ecommerce::order.customer_info')
						</h4>
					</div>
					<table class="table table-bordered">
						<tbody>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Ecommerce::order.customer_name')</th>
								<td class="p-2">{{!empty($customers->name) ? $customers->name : __('Ecommerce::order.no_note') }}</td>
							</tr>
							<tr>
								<th class="p-2" style="width: 200px;">@lang('Ecommerce::order.order_phone')</th>
								<td class="p-2">{{!empty($customers->phone) ? $customers->phone : __('Ecommerce::order.no_note') }}</td>
							</tr>
							{{-- <tr>
								<th class="p-2" style="width: 200px;">@lang('Email')</th>
								<td class="p-2">{{!empty($customers->email) ? $customers->email : __('Ecommerce::order.no_note') }}</td>
							</tr> --}}
						</tbody>
					</table>
				</div>
			</div>
		@endif
	</div>
	<div class="col-lg-6 col-md-12">
		<div class="col-lg-12 p-0">
			<div class="card">
				<div class="card-body p-3">
					<h4 class="card-title">@lang('Ecommerce::order.add_note')</h4>
					<form action="{{ route('admin.orders.admin_note', $order->id) }}" method="POST" onsubmit="addLoadingBtn(document.getElementById('btn-add__note'))">
						@csrf
						<div class="form-group mb-2">
							<textarea name="admin_note" id="admin_note" rows="3" class="form-control" placeholder="@lang('Ecommerce::order.add_note')"></textarea>
						</div>
						<div class="form-group mb-0">
							<button class="btn btn-info btn-sm" id="btn-add__note" type="submit">@lang('Ecommerce::order.add_note')</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-lg-12 p-0">
			<div class="card">
				<div class="card-body p-3">
					<h4 class="card-title">@lang('Ecommerce::order.order_history')</h4>
					<div class="timeline">
						 @php
				            $date_array = [];
				            foreach ($order_histories as $value){
				                $time = date("d-m-Y",strtotime($value->time));
				                if (!in_array($time, $date_array)) {
				                    array_push($date_array, $time);
				                }
				            }
				        @endphp
				        @foreach ($date_array as $date)
							<div class="time-label">
								<span class="bg-red">{{ $date ?? '' }}</span>
							</div>
							@foreach ($order_histories as $value)
								@php
				                    $time = date("d-m-Y",strtotime($value->time));
				                @endphp
				                @if ($date == $time)
									@switch($value->type)
										@case('created')
											<div>
												<i class="fas fa-shopping-cart bg-primary"></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">Khách đặt vé</h3>
												</div>
											</div>
										@break
								        @case('admin_note')
								        	@php
								        		$note = json_decode(base64_decode($value->data ?? ''));
								        	@endphp
											<div>
												<i class="fas fa-pencil-alt bg-warning "></i>
												<div class="timeline-item">
													<span class="time"><i class="fas fa-clock"></i> {{formatTime($value->time, 'H:i:s')}}</span>
													<h3 class="timeline-header">
														<a href="{{ route('admin.admin_users.edit', $value->admin_user_id) }}" target="_blank">{{$admin_users[$value->admin_user_id] ?? ''}}</a>
														@lang('Ecommerce::order.noted')
													</h3>
													<div class="timeline-body" style="white-space: pre;">{{ $note ?? '' }}</div>
												</div>
											</div>
								        @break
									@endswitch
				                @endif
							@endforeach
				        @endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12">

		{{-- Sản phẩm --}}
		@if (isset($order_details) && count($order_details) > 0)
            @php
                $total_price = 0;
            @endphp
			<div class="card">
				<div class="card-body p-3">
					<h4 class="card-title">
						@lang('Ecommerce::order.order_product')
					</h4>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center p-2">@lang('Ecommerce::order.product_name')</th>
								<th class="text-center p-2">Ngày đi</th>
								<th class="text-center p-2">Điểm đón/trả</th>
								<th class="text-center p-2" style="width: 130px;">@lang('Ecommerce::order.product_price')</th>
								<th class="text-center p-2" style="width: 100px;">@lang('Ecommerce::order.product_qty')</th>
								<th class="text-center p-2" style="width: 130px;">@lang('Ecommerce::order.product_total_price')</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($order_details as $item)
								@php
									$product = $item->product;
									$price = $item->price?? 0;
									$quantity = $item->quantity ?? 0;
									$total_price = $total_price+($price*$quantity);
								@endphp
								<tr>
									<td class="p-2">
										<p style="margin-bottom: 2px;">{{ $item->product_name }}
                                        </p>
									</td>
									<td>{{ date('d-m-Y', strtotime($item->start_date)) }}</td>
									<td>
										@if ($item->location_product_id)
											Điểm đón: {{ $item->productLocation->location->name ?? '' }}<br>
											Chi tiết: {{ $item->location_pickup_detail ?? '' }}<br>
										@endif
										@if ($item->return_location_product_id)
											Điểm trả: {{ $item->productLocationReturn->location->name ?? '' }}<br>
											Chi tiết: {{ $item->location_return_detail ?? '' }}<br>
										@endif
									</td>
									<td class="p-2">{{formatPrice($price)}}</td>
									<td class="p-2">{{$quantity}}</td>
									<td class="p-2">{{formatPrice($price*$quantity)}}</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4" class="text-right"><strong>@lang('Ecommerce::order.order_total')</strong></td>
								<td>{{formatPrice($total_price)}}</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		@endif
	</div>
</div>
@endsection
