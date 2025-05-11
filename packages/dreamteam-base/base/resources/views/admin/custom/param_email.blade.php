<div class="param-email">
	@foreach($param as $key => $value)
	<ul>
		<li>{!! $value !!}</li>
		<li>{{!! $key !!}}</li>
	</ul>
	@endforeach
</div>