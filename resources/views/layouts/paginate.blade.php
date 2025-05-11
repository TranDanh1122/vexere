@if ($paginator->lastPage() > 1)
<nav>
	<div class="pagination__numbers pagination">
	    @if(!$paginator->onFirstPage())
	        <div class="paginate_item" data-href="{{ $paginator->url(1) }}">
	        	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="14" height="14"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160zm352-160l-160 160c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L269.3 256 406.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0z" fill="{{ $config_general['color_main'] ?? '#000' }}"/></svg>
	        </div>
	    @else
	        <div class="paginate_item" data-href="">
	        	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="14" height="14"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160zm352-160l-160 160c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L269.3 256 406.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0z" fill="{{ $config_general['color_main'] ?? '#000' }}"/></svg>
	        </div>
	    @endif

	    @if($paginator->currentPage() > 3)
	        <div class="paginate_item" data-href="{{ $paginator->url(1) }}">1</div>
	    @endif

	    @if($paginator->currentPage() > 4)
	        <div class="paginate_dot">...</div>
	    @endif

	    @foreach(range(1, $paginator->lastPage()) as $i)
	        @if($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
	            @if ($i == $paginator->currentPage())
	                <div class="paginate_item active" data-href="{{ $paginator->url($i) }}">{{ $i }}</div>
	            @else
	                <div class="paginate_item" data-href="{{ $paginator->url($i) }}">{{ $i }}</div>
	            @endif
	        @endif
	    @endforeach

	    @if($paginator->currentPage() < $paginator->lastPage() - 3)
	        <div class="paginate_dot">...</div>
	    @endif

	    @if($paginator->currentPage() < $paginator->lastPage() - 2)
	        <div class="paginate_item" data-href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</div>
	    @endif

	    @if ($paginator->hasMorePages())
	        <div class="paginate_item" data-href="{{ $paginator->url($paginator->currentPage()+1) }}" class="next">
	        	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="14" height="14"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L370.7 256 233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L178.7 256 41.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z" fill="{{ $config_general['color_main'] ?? '#000' }}"/></svg>
	        </div>
	    @else
	        <div class="paginate_item" data-href="">
	        	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="14" height="14"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L370.7 256 233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L178.7 256 41.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z" fill="{{ $config_general['color_main'] ?? '#000' }}"/></svg>
	        </div>
	    @endif
	</div>
</nav>
@endif

