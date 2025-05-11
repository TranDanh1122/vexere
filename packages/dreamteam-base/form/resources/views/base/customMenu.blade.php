@php
	$tableLink = [];
	$menuStores = menu_store()->getAll();
@endphp
<div class="row config-menu">
	<div class="col-lg-12 p-0">	
		<div class="card card-info" id="{{$name ?? ''}}_wrap">
			<div class="card-header" data-bs-toggle="collapse" data-parent="#{{$name ?? ''}}_wrap" href="#{{$name ?? ''}}_box" class="collapsed" aria-expanded="false" style="cursor: pointer;">
				<h4 class="card-title">@lang($label ?? '')</h4>
			</div>
			<div class="panel-collapse collapse show" id="{{$name ?? ''}}_box">
				<p class="helper p-3 m-0">{{ __('Translate::form.menu.note') }}</p>
				<div class="card-body row">

					<div class="col-xl-4 col-lg-4 col-md-12 p-0">
						@include('Form::base.components.customMenuSitebar', [ 
							'type' => 'custom_link',
							'label' => __('Translate::form.menu.custom_link'),
						])

						@if (!empty($menuStores) && count($menuStores))
							@foreach ($menuStores as $table => $option)
								@include('Form::base.components.customMenuSitebar', [ 
									'type' => 'table_link',
									'label' => __($option['name'] ?? ''),
									'table' => $table,
									'option' => [],
								])
							@endforeach
						@endif
					</div>

					<div class="col-xl-6 col-lg-8 col-md-12">
						<div class="nestable">
							<input type="hidden" name="{{ $name ?? '' }}" value="">
							<div class="nestable-action">
								<p class="nestable-action__text">@lang('Translate::form.menu.structure')</p>
								{{-- <div class="nestable-action__group">
						        	<button type="button" class="nestable-action__btn plus" data-nestable_action="expandAll" href="#{{ $name ?? '' }}"><i class="fas fa-plus"></i></button>
						        	<button type="button" class="nestable-action__btn minus" data-nestable_action="collapseAll" href="#{{ $name ?? '' }}"><i class="fas fa-minus"></i></button>
								</div> --}}
							</div>
							
							<div class="dd" id="{{ $name ?? '' }}">
							    <ol class="dd-list">
							    	@if (isset($value) && !empty($value))
							    		@php
								    		$value = json_decode($value);
								    		$data = [ 
									    		'datas' => $value ?? [],
									    		'menu_link' => [],
									    		'table_link' => $tableLink ?? []
									    	];
								    	@endphp
								    	@include('Form::base.components.customMenuValue', $data)
							    	@endif
							    </ol>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

<script>
	jQuery(document).ready(function() {
		// Sinh nestable vaf update lại input mỗi khi thay đổi
		$('#{{ $name ?? '' }}').nestable({
	        group: 1
	    }).on('change', function() {
	    	{{$name?? ''}}_value = window.JSON.stringify($('#{{ $name ?? '' }}').nestable('serialize'));
			console.log('change 2')
	    	$('input[name={{ $name ?? '' }}]').val({{$name?? ''}}_value);
	    });
    	$('input[name={{ $name ?? '' }}]').val(window.JSON.stringify($('#{{ $name ?? '' }}').nestable('serialize')));

	    changeMenu('#{{ $name ?? '' }}');
	});
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});
	 $('body').on('click','.suggest_list_menu li',function() {
        let link = $(this).data('link'); 
        let name_type = $(this).data('name');
        let id = $(this).data('id');
		if (!checkEmpty(id)) {
			$(this).closest('.dd-item').attr('data-id', id).data('id', id);
		}
		let htmlText = `
			<p class="edit-link">
				${link}
				<span type="button" class="edit-btn"><i class="fas fa-edit"></i></span>
			</p>
		`;
        $(this).closest('.form-group').find('.suggest_menu').val(name_type).change();
        $(this).closest('.form-group').find('.suggest_menu').hide()
        $(this).closest('.form-group').find('.suggest_menu').before(htmlText)
        if(!$(this).closest('.dd-item').length) {
        	$(this).closest('.card-body').find('.menu-name').val(name_type).change();
        }
        $(this).closest('.form-group').find('.menu-link').val(link).change();
        $(this).closest('.form-group').find('.menu-id').val(id).change();
        $('.suggest_list_menu ul').empty();
        $('.suggest_list_menu').css('display', 'none');
    });
	var suggest = null;
    function suggestMenu(element) {
        var _this = $(element);
        var keyword = _this.val();
        var table = _this.data('table');
        clearTimeout(suggest);
        var data = {
            'keyword': keyword,
            'table': table,
            suggest_locale: true
        };
        if (keyword.length > 0) {
    		_this.closest('.sussget-menu_parent').addClass('active')
            suggest = setTimeout(function() {
                $.ajax({
                    type: 'POST',
                    data: data,
                    url: '{{ route('admin.ajax.sussget_menu', ['lang_locale' => $recordLangLocale ?? $lang_locale ?? Request()->lang_locale ?? \App::getLocale()]) }}',
                    success: function (result) {
        				_this.parent().find('.suggest_list_menu').css('display', 'block');
                    	_this.closest('.sussget-menu_parent').removeClass('active')
                    	if(result.error) {
                    		alertText(result.message, 'error')
                    		return
                    	}
                        if (result.count) {
                            _this.closest('.form-group').find('.suggest_list_menu ul').empty();
                            let html = ''
                            $.each(result.data, (index, item) => {
                                html += `
                                    <li data-id="${item.id}" data-link="${item.url}" data-name="${item.name}">${item.name}</li>
                                `
                            })
                            _this.parent().find('.suggest_list_menu ul').append(html);
                            _this.closest('.form-group').find('.suggest_list_menu').css('display','block');
                        } else {
                            let html = `
                                    <span class="empty_suggest">{{ __('Translate::form.menu.empty') }}</span>
                                `
                            _this.closest('.form-group').find('.suggest_list_menu ul').empty();
                            _this.closest('.form-group').find('.suggest_list_menu ul').append(html);
                            _this.closest('.form-group').find('.suggest_list_menu').css('display','block');
                        }
                    },
                    error: function() {
                    	_this.closest('.sussget-menu_parent').removeClass('active')
                    }
                });
            },2000);
        } else {
            $('.suggest_list_menu').css('display','none');
        }
    };
</script>
