class Currencies {
    constructor() {
        this.template = $('#currency_template').html();
        this.totalItem = 0;

        this.deletedItems = [];

        this.initData();
        this.handleForm();
        this.updateCurrency();
    }

    initData() {
        let _self = this;
        let data = $.parseJSON($('#currencies').html());

        $.each(data, (index, item) => {
            let template = _self.template
                .replace(/__id__/gi, item.id)
                .replace(/__position__/gi, item.order)
                .replace(/__isPrefixSymbolChecked__/gi, (item.is_prefix_symbol == 1 ? 'selected' : ''))
                .replace(/__notIsPrefixSymbolChecked__/gi, (item.is_prefix_symbol == 0 ? 'selected' : ''))
                .replace(/__isDefaultChecked__/gi, (item.is_default == 1 ? 'checked' : ''))
                .replace(/__title__/gi, item.title)
                .replace(/__decimals__/gi, item.decimals)
                .replace(/__exchangeRate__/gi, item.exchange_rate)
                .replace(/__symbol__/gi, item.symbol);

            $('.swatches-container .swatches-list').append(template);

            _self.totalItem++;
        });
    }

    addNewAttribute() {
        let _self = this;

        let template = _self.template
            .replace(/__id__/gi, 0)
            .replace(/__position__/gi, (_self.totalItem))
            .replace(/__isPrefixSymbolChecked__/gi, '')
            .replace(/__notIsPrefixSymbolChecked__/gi, '')
            .replace(/__isDefaultChecked__/gi, (_self.totalItem == 0 ? 'checked' : ''))
            .replace(/__title__/gi, '')
            .replace(/__decimals__/gi, 0)
            .replace(/__exchangeRate__/gi, 1)
            .replace(/__symbol__/gi, '')
        ;

        $('.swatches-container .swatches-list').append(template);

        _self.totalItem++;
    }

    exportData() {
        let data = [];

        $('.swatches-container .swatches-list li').each((index, item) => {
            let $current = $(item);
            data.push({
                id: $current.data('id'),
                is_default: ($current.find('[data-type=is_default] input[type=radio]').is(':checked') ? 1 : 0),
                order: $current.index(),
                title: $current.find('[data-type=title] input').val(),
                symbol: $current.find('[data-type=symbol] input').val(),
                decimals: $current.find('[data-type=decimals] input').val(),
                exchange_rate: $current.find('[data-type=exchange_rate] input').val(),
                is_prefix_symbol: $current.find('[data-type=is_prefix_symbol] select').val()
            });
        });

        return data;
    }

    handleForm() {
        let _self = this;

        $('.swatches-container .swatches-list').sortable();

        $('body')
            .on('submit', '.form-horizontal', () => {
                let data = _self.exportData();

                $('#currencies').val(JSON.stringify(data));

                $('#deleted_currencies').val(JSON.stringify(_self.deletedItems));
            })
            .on('click', '.js-add-new-attribute', event => {
                event.preventDefault();

                _self.addNewAttribute();
            })
            .on('click', '.swatches-container .swatches-list li .remove-item a', event => {
                event.preventDefault();

                let $item = $(event.currentTarget).closest('li');

                _self.deletedItems.push($item.data('id'));

                $item.remove();
            });
    }

    updateCurrency() {
        $(document).on('click', '#btn-update-currencies', function (event) {
            event.preventDefault()
            let _self = $(event.currentTarget);

            const form = $('.main-setting-form');

            $.ajax({
                type:'POST',
                url: form.prop('url'),
                data: form.serialize(),
                success:function(res) {
                    if (res.error) {
                        alertText(res.message, 'error');
                    }
                },
            });

            $.ajax({
                type:'POST',
                url: _self.data('url'),
                beforeSend: function () {
                    _self.addClass('button-loading');
                },
                success:function(res) {
                    if (! res.error) {
                        alertText(res.message, 'success');
                        const data = $.parseJSON(res.data);
                        const template = $('#currency_template').html()
                        let html = '';
                        $('#loading-update-currencies').show();
                        $.each(data, (index, item) => {
                            html += template
                                .replace(/__id__/gi, item.id)
                                .replace(/__position__/gi, item.order)
                                .replace(/__isPrefixSymbolChecked__/gi, (item.is_prefix_symbol == 1 ? 'selected' : ''))
                                .replace(/__notIsPrefixSymbolChecked__/gi, (item.is_prefix_symbol == 0 ? 'selected' : ''))
                                .replace(/__isDefaultChecked__/gi, (item.is_default == 1 ? 'checked' : ''))
                                .replace(/__title__/gi, item.title)
                                .replace(/__decimals__/gi, item.decimals)
                                .replace(/__exchangeRate__/gi, item.exchange_rate)
                                .replace(/__symbol__/gi, item.symbol);

                        });
                        setTimeout(() => {
                            $('.swatches-container .swatches-list').html(html);
                        }, 1000)
                    } else {
                        alertText(res.message, 'error');
                    }
                },
                error: (res) => {
                    alertText(res.message, 'error');
                    _self.removeClass('button-loading');
                },
                complete: function () {
                    _self.removeClass('button-loading');
                },
            });
        })
    }
}

$(window).on('load', () => {
    new Currencies();
});