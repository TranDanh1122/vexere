{{-- 
    @include('Form::custom.audio_file', [
        'name'              => $item['name'],
        'value'             => $item['value'],
        'required'          => $item['required'],
        'label'             => 'Chọn file âm thanh',
        'title_btn'         => 'Chọn file âm thanh',
    ]);
    Lưu ý thêm định dạng file audio mp3,.. tại config/DreamTeamMedia
--}}

<div class="form-group row">
    @if ($has_full == true)
        <label for="{{ $name??'' }}" class="col-lg-12 col-form-label">@if($required==1) * @endif @lang($label??'')</label>
        <div class="col-lg-12">
    @else
        <label for="{{ $name??'' }}" class="col-lg-2 col-md-12 col-form-label">@if($required==1) * @endif @lang($label??'')</label>
        <div class="col-lg-8 col-md-12">
    @endif
        <div class="image-box">
        	<button type="button" class="btn btn-primary btn-sm image-box__btn" 
                data-image="{{ route('media.index', [
                    'uploads' => 'direct',
                    'field_id' => $name??'',
                    'only' => 'file'
                ]) }}"
            >@lang('Translate::form.choose') @lang($title_btn??$label??'')</button>
            <div class="image-box__content" id="{{$name??''}}_box">
                @php
                    $value = old($name)??$value;
                @endphp
                <div class="item file-item" data-delete_box id="{{ $name??'' }}" style="width: auto; height: 40px;">
                    @if(isset($value) && $value != '')
                    <audio class="audio_file" controls>
                        <source src="{{ $value }}">
                    </audio>
                    @endif
                    <input type="hidden" value="{{ $value??'' }}" name="{{$name??''}}" id="input-{{ $name??'' }}">
                    <span class="item-delete {{ $value == '' ? 'hide' : '' }}" data-delete_noremove><i class="fa fa-trash"></i></span>
                </div>
            </div>
        </div>
    </div>
    <style>
        #audio_file {
            border: none;
        }
        .item-delete.hide {
            display: none;
        }
    </style>
    <script>
        $(document).ready(function() {
            {{-- Click nút xóa ảnh thì sẽ trả về giá trị ảnh mặc định và value rỗng --}}
            $('body').on('click', '*[data-delete_noremove]', function() {
                $(this).closest('*[data-delete_box]').find('img').attr('src', '{{ getImageDefault() }}');
                $(this).closest('*[data-delete_box]').find('input').val('');
                $(this).closest('*[data-delete_box]').find('audio').remove();
                $(this).hide();
                getSrc();
            });
            {{-- Nếu bắt buộc --}}
            @if ($required==1)
                $('body').on('click','button[type=submit]', function(e) {
                    if($('input[name={{$name??''}}]').length == 0) {
                        e.preventDefault();
                        $('#{{$name??''}}_box').find('.error').remove();
                        $('#{{$name??''}}_box').append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')'));
                        openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
                    }
                });
            @endif
            getSrc();
            function getSrc(){
                var src = $('#input-{{ $name??'' }}').val();
                if(src == '') {
                    setTimeout(function () {//Không có thông báo mới nên sẽ request lại sau 1 khoảng tg để check
                        getSrc();
                    },10);
                } else if($('.audio_file').length <= 0 && src != '' && src != undefined) {
                    $('#input-{{ $name??'' }}').before('<audio controls><source src="'+src+'"></audio>');
                    $('.item-delete').show();
                }
            }
        });
    </script>
</div>