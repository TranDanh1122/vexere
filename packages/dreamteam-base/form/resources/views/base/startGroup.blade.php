<div class="row config-menu">
	<div class="col-lg-12">	
		<div class="card card-info" id="{{$id ?? ''}}_wrap">
			<div class="card-header" data-bs-toggle="collapse" data-parent="#{{$id ?? ''}}_wrap" href="#{{$id ?? ''}}_box" class="collapsed" aria-expanded="false" style="cursor: pointer;">
				<h4 class="card-title">@lang($label ?? '')</h4>
			</div>
			<div class="panel-collapse collapse show" id="{{$id ?? ''}}_box">