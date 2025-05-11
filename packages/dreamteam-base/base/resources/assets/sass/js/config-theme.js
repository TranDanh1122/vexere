document.addEventListener('DOMContentLoaded', function () {
    $('.tab-list__item-custom').on('click', function () {
        const tabId = $(this).attr('data-id');
        $('.tab-content-custom').addClass('hidden');
        $('.tab-content-custom.' + tabId).removeClass('hidden');
        $('.tab-list__item-custom').removeClass('active');
        $(this).addClass('active');
        window.scrollTo(0, 0);
    });

    $('.tab-list__item-custom').first().trigger('click');

    const otherSettingInput = $('.tab-content-custom.other').find('input');
    if (otherSettingInput.length === 0) {
        $('.tab-content-custom.other').remove();
        $('.tab-list__item-custom[data-id="other"]').remove();
    }

    $('[name="style[font_family]"]').on('change', function () {
        $('#style-body-preview').css('font-family', $(this).val());

        for (let i = 1; i <= 6; i++) {
            $(`[name="style[h${i}_font_family]"]`).trigger('change');
        }
    });

    $('[name="style[body_font_weight]"]').on('change', function () {
        $('#style-body-preview').css('font-weight', $(this).val());
    });

    $('[name="style[desktop][font_size]"]').on('change', function () {
        $('#style-body-preview').css('font-size', $(this).val());
    });

    $('[name="style[letter_spacing]"]').on('change', function () {
        $('#style-body-preview').css('letter-spacing', $(this).val());
    });

    $('[name="style[color_body]"]').on('change', function () {
        $('#style-body-preview').css('color', $(this).val());
    });

    for (let i = 1; i <= 6; i++) {
        $(`[name="style[h${i}_font_family]"]`).on('change', function () {
            let fontName = $(this).val();
            fontName = fontName ? fontName : $('[name="style[font_family]"]').val();
            $('#style-body-preview-h' + i).css('font-family', fontName);
        });

        $(`[name="style[h${i}_font_weight]"]`).on('change', function () {
            $('#style-body-preview-h' + i).css('font-weight', $(this).val());
        });

        $(`[name="style[h${i}_font_size]"]`).on('change', function () {
            $('#style-body-preview-h' + i).css('font-size', $(this).val());
        });

        $(`[name="style[h${i}_font_color]"]`).on('change', function () {
            $('#style-body-preview-h' + i).css('color', $(this).val());
        });

        $(`[name="style[h${i}_text_transform]"]`).on('change', function () {
            $('#style-body-preview-h' + i).css('text-transform', $(this).val());
        });
    }
});
