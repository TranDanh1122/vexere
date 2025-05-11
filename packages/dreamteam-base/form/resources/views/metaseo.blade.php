@php
	$seo_title = '';
    $seo_description = '';
    $seo_html_head = '';
    $seo_robots = 'Index,Follow';
    $social_title = '';
    $social_description = '';
    $social_image = '';
	if (isset($type) && isset($type_id)) {
		$seo = \DB::table('seos')->where('type', $type)->where('type_id', $type_id)->first();
		$seo_title = $seo->title??'';
	    $seo_description = $seo->description??'';
	    $seo_html_head = $seo->html_head??'';
	    $seo_robots = $seo->robots??'Index,Follow';
	    $social_title = $seo->social_title??'';
	    $social_description = $seo->social_description??'';
	    $social_image = $seo->social_image??'';
	}
	if(isset($homePageSeo)) {
		$seo_title = $homePageSeo['meta_title'] ?? '';
	    $seo_description = $homePageSeo['meta_description'] ?? '';
		$seo_robots = $homePageSeo['meta_robots'] ?? 'Index,Follow';
		$social_title = $homePageSeo['social_title'] ?? '';
		$social_description = $homePageSeo['social_description'] ?? '';
	    $social_image = $homePageSeo['social_image'] ?? '';
	    $seo_html_head = $homePageSeo['html_head'] ?? '';
	}
@endphp
<div class="row">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">@lang('Translate::form.metaseo.title_card')</h4>
		        <div class="row tab-seo">
					<div class="col-lg-12">
						<ul class="tab-list">
							<li class="tabseo-list__item active" data-active="google">SEO Google</li>
							<li class="tabseo-list__item" data-active="social">@lang('Translate::form.metaseo.social')</li>
							<li class="tabseo-list__item" data-active="html">@lang('Translate::form.metaseo.seo_html_head')</li>
						</ul>
						<div class="tab-content tab-content__google">
							<div class="mb-3">
						        <label style="margin: 0">Meta title</label>
						        <span class="helper"><span class="count_word">0</span> @lang('Translate::form.metaseo.meta_title_desc')</span>
				                <input type="text" class="form-control" data-check_length data-review="meta_title" name="meta_title" id="meta_title" placeholder="" value='{!! $seo_title !!}'>
				            </div>
				            <div class="mb-3">
						        <label style="margin: 0">Meta Description</label>
						        <span class="helper"><span class="count_word">0</span> @lang('Translate::form.metaseo.meta_description_desc')</span>
					            <textarea class="form-control" data-check_length data-review="meta_description" name="meta_description" id="meta_description" placeholder="" rows="3">{!! $seo_description !!}</textarea>
					        </div>
					        <div class="mb-3 ">
						        <label>Meta Robots</label>
						        @php
									$option_seo_robots = [
										'Index,Follow' 		=> 'Index,Follow',
										'Noindex,Nofollow' 	=> 'Noindex,Nofollow',
										'Index,Nofollow' 	=> 'Index,Nofollow',
										'Noindex,Follow' 	=> 'Noindex,Follow',
									];
								@endphp
								<select id="meta_robots" name="meta_robots" class="form-control form-select">
									@foreach ($option_seo_robots as $key => $robots)
					                	<option value="{!! $key !!}"
											@if ($key == $seo_robots) selected @endif
					                	>{!! $robots !!}</option>
									@endforeach
					            </select>
					        </div>
							@if (!isset($homePageSeo) && !isset($url))
								<div class="mb-3 row toggle-on-off-checkbox" style="position: relative;">
									<label for="is_custom_canonical" class="col-md-4 col-form-label">{{ trans('Core::admin.custom_canonical') }}</label>
									<div class=" col-md-8  form-switch form-switch-lg">
										<input type="checkbox" class="form-check-input" name="is_custom_canonical" id="is_custom_canonical" value="1" {{ (isset($seo) && $seo->is_custom_canonical == 1) ? 'checked=""' : '' }} style="margin-top: 6px;left: 0;">
									</div>
								</div>
								<div class="mb-3 on-of-checkbox-config {{ (isset($seo) && $seo->is_custom_canonical == 1) ? '' : 'hide' }}">
									<input type="text" class="form-control" name="canonical" id="canonical" placeholder="Canonical url" value='{!!$seo->canonical ?? '' !!}'>
								</div>
								<div class="mb-3 row" style="position: relative;">
									<label for="show_on_sitemap" class="col-md-4 col-form-label">Sitemap</label>
									<div class=" col-md-8  form-switch form-switch-lg">
										<input type="checkbox" class="form-check-input" name="show_on_sitemap" value="1" {{ (!isset($seo) || $seo->show_on_sitemap == 1) ? 'checked=""' : '' }} style="margin-top: 6px;left: 0;">
									</div>
								</div>
							@endif
						</div>
						<div class="tab-content tab-content__social">
							@include('Form::base.image', [
					        	'name'				=> 'social_image',
								'value' 			=> $social_image,
								'required' 			=> 0,
								'label' 			=> 'Social Image',
								'title_btn' 		=> 'Chọn ảnh',
								'has_row' 			=> false,
								'class_col' 		=> 'col-lg-12',
					        ])
							<div class="mb-3">
						        <label style="margin: 0">Social Title</label>
						        <span class="helper">@lang('Translate::form.metaseo.social_title_desc')</span>
				                <input type="text" class="form-control" name="social_title" id="social_title" placeholder="" value="{!! $social_title !!}">
				            </div>
				            <div class="mb-3">
						        <label style="margin: 0">Social Description</label>
						        <span class="helper">@lang('Translate::form.metaseo.social_description_desc')</span>
					            <textarea class="form-control" name="social_description" id="social_description" placeholder="" rows="3">{!! $social_description !!}</textarea>
					        </div>
						</div>
						<div class="tab-content tab-content__html">
							<div class="mb-3">
						        <label style="margin: 0">{{ __('Translate::form.metaseo.add_code_head') }}</label>
						        <span class="helper">{{ __('Translate::form.metaseo.add_code_desc') }}</span>
					            <textarea class="form-control" name="html_head" id="html_head" placeholder="" rows="3">{!! $seo_html_head !!}</textarea>
					        </div>
						</div>
				    </div>
				</div>
			</div>
		</div>

	</div>
	@if(!isset($hide_review))
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">@lang('Translate::form.metaseo.preview')</h4>
				<p class="card-title-desc" style="margin-bottom: 15px;">@lang('Translate::form.metaseo.content_preview')</p>
				<div class="preview_snippet_main row">
		            <h3 class="preview_snippet_title"></h3>
		            <p class="preview_snippet_link">
		            	@if(isset($homePageSeo))
		            		<span>{{ route('app.home.'.\App::getLocale()) }}</span>
		            	@elseif(isset($url))
		            		<a class="btn btn-xs btn-warning" href="{{ $url }}">{{ $url }}</a>
		            	@else
			            	<span></span>
				            <a class="btn btn-xs btn-warning" href="#slug" id="seo-edit_slug">@lang('Translate::form.metaseo.edit_slug')</a>
			            @endif
			        </p>
		            <p class="preview_snippet_des"></p>
		        </div>
			</div>
		</div>
	</div>
	@endif
</div>
<script>
	$(document).ready(function(){
        var class_active = $('.tabseo-list__item.active').data('active');
        $('.tab-seo .tab-content').removeClass('active');
        $('.tab-seo .tab-content__'+class_active).addClass('active');
        
        $('.tabseo-list__item').on('click', function(){
            var class_active = $(this).data('active');
            $('.tabseo-list__item').removeClass('active');
            $(this).addClass('active');
            $('.tab-seo .tab-content').removeClass('active');
            $('.tab-seo .tab-content__'+class_active).addClass('active');
        });
    });
	$(document).ready(function() {
		$('body').on('keyup change', '*[data-check_length]', function(){
			value = $(this).val();
			length = value.length;
			$(this).closest('.mb-3').find('.count_word').html(length);

			var data_review = $(this).data('review');
			if(data_review == 'meta_title'){
				$('.preview_snippet_title').html(value);
			}else{
				$('.preview_snippet_des').html(value);
			}
		});
		$('body').on('click', '#seo-edit_slug', function(e){
			e.preventDefault()
			$('html, body').animate({
				scrollTop: $('#slug').position().top
			}, 500)
		});
		if($('.preview_snippet_link').length && $('#slug').length) {
	        setInterval(function(){ 
	            $slug = $('.slug-content span').text();
	            $('.preview_snippet_link span').html($slug);
	        }, 2000);
	    }
		$('*[data-check_length]').keyup();
		let metaTitle = $('#meta_title').val();
		let metaDesc = $('#meta_description').val();
		let metaDescContent = '{{ cutString(removeHtml($data_edit->detail ?? ''), 150) }}'
		if (checkEmpty(metaTitle)) {
			$('.preview_snippet_title').html($('#name').val());
		}
		if (checkEmpty(metaDesc)) {
			$('.preview_snippet_des').html(metaDescContent);
		}
		$('body').on('keyup change', '#name', function(){
			let metaTitle = $('#meta_title').val();
			if (checkEmpty(metaTitle)) metaTitle = $(this).val();
			$('.preview_snippet_title').html(metaTitle);
		});
		$('body').on('change', '#is_custom_canonical', function(){
			if (!$(this).prop('checked')) {
				$('#canonical').val('').change();
			}
		});
		$('#canonical').on('keyup change', function() {
			$(this).parent().find('.error').remove();
        	$(this).css('border', '1px solid #ced4da');
		})
		$('body').on('click', 'button[type="submit"]', function(e){
			$('#canonical').parent().find('.error').remove();
        	$('#canonical').css('border', '1px solid #ced4da');
			if ($('#is_custom_canonical').prop('checked') && checkEmpty($('#canonical').val())) {
				e.preventDefault();
				$('html, body').animate({
					scrollTop: $('.footer').position().top
				})
				$('#canonical').css('border', '1px solid #ced4da');
				$('#canonical').parent().append(formHelper("{{ __('Core::admin.general.require', ['name' => 'Canonical']) }}"));
			}
		});
	});
</script>