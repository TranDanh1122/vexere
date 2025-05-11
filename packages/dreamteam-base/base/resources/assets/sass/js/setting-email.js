$('.card-header').trigger('click');

$('input[type="checkbox"].toggle-enable-send-mail').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).closest('.email-wrapper').find('.email-content').show(300);
    } else {
        $(this).closest('.email-wrapper').find('.email-content').hide(300);
    }
});

function toggleShowEmailContent(element) {
    element.closest('.email-wrapper').find('.email-content').toggle(300);
}

$('.toggle-show-email-content').on('click', function (e) {
    e.preventDefault();
    toggleShowEmailContent($(this));
});

$('.edit-email-wrapper').on('click', function () {
    toggleShowEmailContent($(this));
})

setTimeout(() => {
    $('.hidden-tab').removeClass('hidden-tab');
    $('.email-content').css('display', 'none');
}, 100);
