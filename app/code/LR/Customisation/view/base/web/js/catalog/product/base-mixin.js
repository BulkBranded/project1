define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'uiRegistry',
    'underscore',
    'mage/template',
    'jquery/ui'
], function ($, utils, registry, _, mageTemplate) {
    'use strict';

    return function (widget) {
        $.widget('mageworx.optionBase', widget, {

            /**
             * Make changes to select options
             * @param options
             * @param opConfig
             */
            _updateSelectOptions: function(options, opConfig)
            {
                var self = this;
                options.each(function (index, element) {
                    var $element = $(element);

                    if ($element.hasClass('datetime-picker') ||
                        $element.hasClass('text-input') ||
                        $element.hasClass('input-text') ||
                        $element.attr('type') == 'file'
                    ) {
                        return true;
                    }

                    var optionId = utils.findOptionId($element),
                        optionConfig = opConfig[optionId];

                    $element.find('option').each(function (idx, option) {
                        var $option = $(option),
                            optionValue = $option.val();

                        if (!optionValue && optionValue !== 0) {
                            return;
                        }

                        var title = optionConfig[optionValue] && optionConfig[optionValue].name,
                            valuePrice = utils.formatPrice(optionConfig[optionValue].prices.finalPrice.amount),
                            stockMessage = '',
                            specialPriceDisplayNode = '';
 
                        if (optionConfig[optionValue]) {
                            if (!_.isEmpty(optionConfig[optionValue].special_price_display_node)) {
                                specialPriceDisplayNode = optionConfig[optionValue].special_price_display_node;
                            }
                            if (!_.isEmpty(optionConfig[optionValue].stockMessage)) {
                                stockMessage = optionConfig[optionValue].stockMessage;
                            }
                            if (!_.isEmpty(optionConfig[optionValue].title)) {
                                title = optionConfig[optionValue].title;
                            }
                            if (!_.isEmpty(optionConfig[optionValue].valuePrice)) {
                                valuePrice = optionConfig[optionValue].valuePrice;
                            }
                        }
                        if (specialPriceDisplayNode) {
                            $option.text(title + ' ' + specialPriceDisplayNode + ' ' + stockMessage);
                        } else if (stockMessage) {
                            if (parseFloat(optionConfig[optionValue].prices.finalPrice.amount) > 0) {
                                $option.text(title + ' +' + valuePrice + ' ' + stockMessage);
                            } else {
                                $option.text(title + stockMessage);
                            }
                        }

                        $option.text(title + stockMessage);
                    });
                });
            },

            /**
             * Make changes to select options
             * @param options
             * @param opConfig
             */
            _updateInputOptions: function(options, opConfig)
            {
                var self = this;
                options.each(function (index, element) {
                    var $element = $(element);

                    if ($element.hasClass('datetime-picker') ||
                        $element.hasClass('text-input') ||
                        $element.hasClass('input-text') ||
                        $element.attr('type') == 'file'
                    ) {
                        return true;
                    }

                    var optionId = utils.findOptionId($element),
                        optionValue = $element.val();

                    if (!optionValue && optionValue !== 0) {
                        return;
                    }

                    var optionConfig = opConfig[optionId],
                        title = optionConfig[optionValue] && optionConfig[optionValue].name,
                        valuePrice = utils.formatPrice(optionConfig[optionValue].prices.finalPrice.amount),
                        stockMessage = '',
                        specialPriceDisplayNode = '';

                    if (optionConfig[optionValue]) {
                        if (!_.isEmpty(optionConfig[optionValue].special_price_display_node)) {
                            specialPriceDisplayNode = optionConfig[optionValue].special_price_display_node;
                        }
                        if (!_.isEmpty(optionConfig[optionValue].stockMessage)) {
                            stockMessage = optionConfig[optionValue].stockMessage;
                        }
                        if (!_.isEmpty(optionConfig[optionValue].title)) {
                            title = optionConfig[optionValue].title;
                        }
                        if (!_.isEmpty(optionConfig[optionValue].valuePrice)) {
                            valuePrice = optionConfig[optionValue].valuePrice;
                        }
                    }
                    if (specialPriceDisplayNode) {
                        $element.next('label').text(title + ' ' + specialPriceDisplayNode + ' ' + stockMessage);
                    } else if (stockMessage) {
                        if (parseFloat(optionConfig[optionValue].prices.finalPrice.amount) > 0) {
                            $element.next('label').text(title + ' +' + valuePrice + ' ' + stockMessage);
                        } else {
                            $element.next('label').text(title + stockMessage);
                        }
                    }

                    $element.next('label').text(title  + stockMessage);
                });
            },

        });
        return $.mageworx.optionBase;
    };

 });
 