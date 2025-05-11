$(document).ready(function () {
    $('.generate-thumbnails-trigger-button').on('click', (event) => {
        event.preventDefault()
        const _self = $(event.currentTarget)
        const defaultText = _self.text()

        _self.text(_self.data('saving'))

        const $form = _self.closest('form')

        $httpClient
            .make()
            .postForm($form.prop('action'), new FormData($form[0]))
            .then(() => $('#generate-thumbnails-modal').modal('show'))
            .finally(() => {
                _self.text(defaultText)
            })
    })

    $('#generate-thumbnails-button').on('click', (event) => {
        event.preventDefault()
        let _self = $(event.currentTarget)

        window.addLoadingBtn(_self)

        const $form = _self.closest('form')

        $httpClient
            .make()
            .post($form.prop('action'))
            .then(({ data }) => DreamTeamCore.showSuccess(data.message))
            .finally(() => {
                window.removeLoadingBtn(_self)
                _self.closest('.modal').modal('hide')
            })
    })

    $(document).on('change', '.check-all', (event) => {
        let _self = $(event.currentTarget)
        let set = _self.attr('data-set')
        let checked = _self.prop('checked')
        $(set).each((index, el) => {
            if (checked) {
                $(el).prop('checked', true)
            } else {
                $(el).prop('checked', false)
            }
        })
    })

    $(document).on('change', 'input[name="media_compressed_size"]', function() {
        if ($(this).prop('checked')) {
            $('.thumbnail-wraper').removeClass('d-none');
        } else {
            $('.thumbnail-wraper').addClass('d-none');
        }
    })
})
