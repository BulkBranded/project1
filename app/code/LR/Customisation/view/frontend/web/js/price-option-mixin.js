define(['jquery', 'priceUtils', 'priceBox'], function ($, utils) {
    'use strict';

    function getFormattedPrice (price) {
        return utils.formatPrice(price);
    };

    function defaultGetOptionValue(element, optionsConfig) {
        var changes = {},
            optionValue = element.val(),
            optionId = utils.findOptionId(element[0]),
            optionName = element.prop('name'),
            optionType = element.prop('type'),
            optionConfig = optionsConfig[optionId],
            optionHash = optionName;

        switch (optionType) {
            case 'text':
            case 'textarea':
                changes[optionHash] = optionValue ? optionConfig.prices : {};
                break;

            case 'radio':
                if (element.is(':checked')) {
                    changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                }
                break;

            case 'select-one':
                changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                break;

            case 'select-multiple':
                _.each(optionConfig, function (row, optionValueCode) {
                    optionHash = optionName + '##' + optionValueCode;
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? row.prices : {};
                });
                break;

            case 'checkbox':
                optionHash = optionName + '##' + optionValue;
                changes[optionHash] = element.is(':checked') ? optionConfig[optionValue].prices : {};
                break;

            case 'file':
                // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                changes[optionHash] = optionValue || element.prop('disabled') ? optionConfig.prices : {};
                break;
        }

        return changes;
    };

    var priceWidgetMixin = {
        _create: function createPriceOptions() {
            this._super(this);

            var form = this.element,
                options = $(this.options.optionsSelector, form),
                priceBox = $(this.options.priceHolderSelector, $(this.options.optionsSelector).element);

            if (priceBox.data('magePriceBox') &&
                priceBox.priceBox('option') &&
                priceBox.priceBox('option').priceConfig
            ) {
                if (priceBox.priceBox('option').priceConfig.optionTemplate) {
                    this._setOption('optionTemplate', priceBox.priceBox('option').priceConfig.optionTemplate);
                }
                this._setOption('priceFormat', priceBox.priceBox('option').priceConfig.priceFormat);
            }

            var resetButton = $('.product-option-reset');
            resetButton.on('click', this._onOptionChanged.bind(this));
        },

        _onOptionChanged: function onOptionChanged(event) {
            var changes = {},
                option = $(event.target),
                handler = this.options.optionHandlers[option.data('role')];

            if($(event.target).is('span') || $(event.target).is('button')) {
                Array.from($('.product-custom-option')).forEach(element => {
                    changes[$(element).prop('name')] = {};
                });
            } else {
                if (handler && handler instanceof Function) {
                    changes = handler(option, this.options.optionConfig, this);
                } else {
                    changes = defaultGetOptionValue(option, this.options.optionConfig);
                }
            }

            $(this.options.priceHolderSelector).trigger('updatePrice', changes);

            var e = localStorage.getItem("promoBrandCurrentVat"),
                qty = $("#qty").val(),
                str = ("exc" === e) ? $(
                    '[data-role="priceBox"] .price-excluding-tax .price'
                    ).text() : $(
                    '[data-role="priceBox"] .price-including-tax .price'
                    ).text(),
                priceValue = str.slice(1, str.length - 3).replace(",", '');
            $('.product-netprice .total-price').text(getFormattedPrice(priceValue * qty));
        }
    };

    return function (targetWidget) {
        $.widget('mage.price', targetWidget, priceWidgetMixin);
        return $.mage.price;
    };
});
