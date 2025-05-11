class LanguageManagement {
    init() {
        let languageTable = $('.table-language')

        $(document).on('change', '#language_id', (event) => {
            let language = $(event.currentTarget).find('option:selected').data('language')
            if (typeof language != 'undefined' && language.length > 0) {
                $('#lang_name').val(language[2])
                $('#lang_locale').val(language[0])
                $('#lang_code').val(language[1])
                $(`input[name=lang_rtl][value="${language[3] === 'rtl' ? 1 : 0}"]`).prop('checked', true)
                $('#flag_list').val(language[4]).trigger('change')
                $('#btn-language-submit-edit')
                    .prop('id', 'btn-language-submit')
                    .text($('#btn-language-submit').data('add-language-text'))
            }
        })

        $(document).on('click', '#btn-language-submit', (event) => {
            event.preventDefault()
            let name = $('#lang_name').val()
            let locale = $('#lang_locale').val()
            let code = $('#lang_code').val()
            let flag = $('#flag_list').val()
            let order = $('#lang_order').val()
            let isRTL = $('input[name=lang_rtl]:checked').val()
            LanguageManagement.createOrUpdateLanguage(0, name, locale, code, flag, order, isRTL, 0)
        })

        $(document).on('click', '#btn-language-submit-edit', (event) => {
            event.preventDefault()
            let id = $('#lang_id').val()
            let name = $('#lang_name').val()
            let locale = $('#lang_locale').val()
            let code = $('#lang_code').val()
            let flag = $('#flag_list').val()
            let order = $('#lang_order').val()
            let isRTL = $('input[name=lang_rtl]:checked').val()
            LanguageManagement.createOrUpdateLanguage(id, name, locale, code, flag, order, isRTL, 1)
        })

        languageTable.on('click', '.deleteDialog', (event) => {
            event.preventDefault()

            $('.delete-crud-entry').data('section', $(event.currentTarget).data('section'))
            $('.modal-confirm-delete').modal('show')
        })

        $('.delete-crud-entry').on('click', (event) => {
            event.preventDefault()
            $('.modal-confirm-delete').modal('hide')

            let deleteURL = $(event.currentTarget).data('section')
            DreamTeamCore.showButtonLoading($(this))

            $httpClient
                .make()
                .delete(deleteURL)
                .then(({ data }) => {
                    if (data.data) {
                        languageTable.find(`i[data-id=${data.data}]`).unwrap()
                        $('.tooltip').remove()
                    }
                    languageTable.find(`button[data-section="${deleteURL}"]`).closest('tr').remove()
                    DreamTeamCore.showNotice('success', data.message)
                })
                .finally(() => {
                    DreamTeamCore.hideButtonLoading($(this))
                })
        })

        languageTable.on('click', '.change-status', (event) => {
            event.preventDefault()
            const _self = $(event.currentTarget)

            $httpClient
                .make()
                .post(_self.data('section'))
                .then(({ data }) => {
                    const icon = _self.find('input')
                    icon.prop('checked', false)
                    if(data.data.status == 1) {
                        icon.prop('checked', true)
                    }

                    $('.tooltip').remove()

                    DreamTeamCore.showNotice('success', data.message)
                })
        })

        languageTable.on('click', '.edit-language-button', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)

            $httpClient
                .make()
                .get(_self.data('url'))
                .then(({ data }) => {
                    let language = data.data

                    $('.select-search-full').val(language.code).trigger('change');
                    $('#lang_id').val(language.id)
                    $('#lang_name').val(language.name)
                    $('#lang_locale').val(language.locale)
                    $('#lang_code').val(language.code)
                    $('#flag_list').val(language.flag).trigger('change')
                    $(`input[name=lang_rtl][value="${language.is_rtl ? 1 : 0}"]`).prop('checked', true)
                    $('#lang_order').val(language.order)

                    $('#btn-language-submit')
                        .prop('id', 'btn-language-submit-edit')
                        .text($('#btn-language-submit-edit').data('update-language-text'))

                    window.scroll({
                        top: 0,
                        behavior: 'smooth'
                    });
                })
        })

        $(document).on('submit', 'form.form-setting-language', (event) => {
            event.preventDefault()

            const form = $(event.currentTarget)
            const button = form.find('button[type=submit]')

            DreamTeamCore.showButtonLoading(button)

            $httpClient
                .make()
                .postForm(form.prop('action'), new FormData(form[0]))
                .then(({ data }) => {
                    DreamTeamCore.showNotice('success', data.message)
                    form.removeClass('dirty')
                    window.location.reload()
                })
                .finally(() => {
                    DreamTeamCore.hideButtonLoading(button)
                })
        })
    }

    static formatState(state) {
        if (!state.id || state.element.value.toLowerCase().includes('...')) {
            return state.text
        }

        return $(
            `<div>
                <span class="dropdown-item-indicator">
                    <img src="${$(
                        '#language_flag_path'
                    ).val()}${state.element.value.toLowerCase()}.svg" class="flag" style="height: 16px;" alt="${
                        state.text
                    }">
                </span>
                <span>${state.text}</span>
            </div
        `
        )
    }

    static createOrUpdateLanguage(id, name, locale, code, flag, order, isRTL, edit) {
        const $buttonSubmit = $('#btn-language-submit')

        let url = $buttonSubmit.data('store-url')

        if (edit) {
            url = $('#btn-language-submit-edit').data('update-url') + `?lang_code=${code}`
        }

        DreamTeamCore.showButtonLoading($buttonSubmit, true)

        $httpClient
            .make()
            .post(url, {
                lang_id: id.toString(),
                lang_name: name,
                lang_locale: locale,
                lang_code: code,
                lang_flag: flag,
                lang_order: order,
                lang_is_rtl: isRTL,
            })
            .then(({ data }) => {
                if (edit) {
                    $('.table-language')
                        .find('tr[data-id=' + id + ']')
                        .replaceWith(data.data)
                } else {
                    $('.table-language').append(data.data)
                    $(`.select-search-full option[value="${code}"]`).remove()
                    $(`.select-search-full`).trigger('change')

                    const newOption = new Option(name, locale, false, false);
                    $('.language-selector').append(newOption).trigger('change')
                }
                DreamTeamCore.showNotice('success', data.message)
            })
            .finally(() => {
                $('#language_id').val('').trigger('change')
                $('#lang_name').val('')
                $('#lang_locale').val('')
                $('#lang_code').val('')
                $('input[name=lang_rtl][value="0"]').prop('checked', true)
                $('#flag_list').val('').trigger('change')

                $('#btn-language-submit-edit')
                    .prop('id', 'btn-language-submit')
                    .text($('#btn-language-submit').data('add-language-text'))
                DreamTeamCore.hideButtonLoading($buttonSubmit)
            })
    }
}

$(() => {
    new LanguageManagement().init()
})
