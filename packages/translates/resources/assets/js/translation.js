$(() => {
    $(document).on("click", "td a.editable", (event) => {
        event.preventDefault()
        const _this = $(event.currentTarget);
        _this.closest('tbody').find('td a.editable').show()
        _this.closest('tbody').find('td .editable-container').remove()
        const html = `<span class="editable-container editable-inline" style="white-space: none;"><div><div class="editableform-loading" style="display: none;"></div><form class="form-inline editableform" style=""><div class="control-group form-group"><div><div class="editable-input"><textarea class="form-control input-large" rows="7">${_this.text()}</textarea></div><div class="editable-buttons"><button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="fa fa-check" aria-hidden="true"></i></button><button type="button" class="btn btn-default btn-sm editable-cancel"><i class="fa fa-times" aria-hidden="true"></i></button></div></div></div></form></div></span>`;
        _this.parent().append(html)
        _this.hide()
    });
    $(document).on("click", ".editable-cancel", (event) => {
        const _this = $(event.currentTarget);
        _this.closest('tbody').find('td a.editable').show()
        _this.closest('.editable-container').remove()
    });
    $(document).on("click", "td .editable-submit", (event) => {
        event.preventDefault();

        const $button = $(event.currentTarget);
        const href = $button.closest('td').find('a')
        $httpClient
            .make()
            .withButtonLoading($button)
            .post(href.data('url'), {
                name: href.data('name'),
                value: $button.closest('form').find('textarea').val()
            })
            .then(({ data }) => {
                DreamTeamCore.showSuccess(data.message);
                $button.closest('tbody tr').remove()
                $button.closest('tbody').append(data.data)
            });
    });
    $(document).on("click", ".button-import-groups", (event) => {
        event.preventDefault();

        const $button = $(event.currentTarget);

        $httpClient
            .make()
            .withButtonLoading($button)
            .postForm($button.data("url"))
            .then(({ data }) => {
                DreamTeamCore.showSuccess(data.message);

                if ($button.closest(".modal").length) {
                    $button.closest(".modal").modal("hide");

                    const $table = $(".translations-table .table");

                    if ($table.length) {
                        $table
                            .DataTable()
                            .ajax.url(window.location.href)
                            .load();
                    } else {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
    });
});
