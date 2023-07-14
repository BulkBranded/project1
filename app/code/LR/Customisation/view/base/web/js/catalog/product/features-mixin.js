define([
    'jquery',
    'Magento_Catalog/js/price-utils'
], function ($,utils) {
    'use strict';
    return function (widget) {
        $.widget('mageworx.optionFeatures', widget, {
            /**
             *
             * @param $option
             * @param optionConfig
             * @param extendedOptionsConfig
             * @param valueDescriptionEnabled
             * @private
             */
            _addValueDescription: function _addValueDescription($option, optionConfig, extendedOptionsConfig, valueDescriptionEnabled) {
                var self = this,
                    $options = $option.find('.product-custom-option');

                $options.filter('select').each(function (index, element) {
                    var $element = $(element),
                        optionId = utils.findOptionId($element),
                        value = extendedOptionsConfig[optionId]['values'];
                        $options = $option.addClass(extendedOptionsConfig[optionId].custom_class);
                    if ($element.attr('multiple') && !$element.hasClass('mageworx-swatch')) {
                        return;
                    }

                    if (typeof value == 'undefined' || _.isEmpty(value)) {
                        return;
                    }

                    if ($element.hasClass('mageworx-swatch')) {
                        var $swatches = $element.parent().find('.mageworx-swatch-option');

                        $swatches.each(function (swatchKey, swatchValue) {
                            var valueId = $(swatchValue).attr('data-option-type-id'),
                                tooltipImage = self.getTooltipImageHtml(value[valueId]),
                                title = '<div class="title">' + value[valueId]['title'] + '</div>',
                                stockMessage = '';

                            if (!_.isEmpty(optionConfig[optionId][valueId]['stockMessage'])) {
                                stockMessage = '<div class="info">'
                                    + optionConfig[optionId][valueId]['stockMessage']
                                    + '</div>';
                            }

                            if (valueDescriptionEnabled) {
                                if (!_.isUndefined(value[valueId]) &&
                                    (!_.isEmpty(value[valueId]['description']) ||
                                        !_.isEmpty(value[valueId]['images_data']['tooltip_image']))
                                ) {
                                    var description = '';
                                    if (!_.isEmpty(value[valueId]['description'])) {
                                        description = value[valueId]['description'];
                                    }
                                    self.prepareTooltipDescription($(swatchValue), tooltipImage, title, stockMessage, description);
                                }
                            } else {
                                if (!_.isUndefined(value[valueId]) &&
                                    !_.isEmpty(value[valueId]['images_data']['tooltip_image'])
                                ) {
                                    self.prepareTooltipDescription($(swatchValue), tooltipImage, title, stockMessage);
                                }
                            }
                        });
                    } else {
                        var $image = $('<img>', {
                            src: self.options.question_image,
                            alt: 'tooltip',
                            "class": 'option-select-tooltip-' + optionId,
                            width: '16px',
                            height: '16px',
                            style: 'display: none'
                        });

                        $element.parent().prepend($image);
                        $element.on('change', function (e) {
                            var valueId = $element.val(),
                                tooltipImage = self.getTooltipImageHtml(value[valueId]);

                            if (valueDescriptionEnabled) {
                                if (!_.isUndefined(value[valueId]) &&
                                    (!_.isEmpty(value[valueId]['description']) ||
                                        !_.isEmpty(value[valueId]['images_data']['tooltip_image']))
                                ) {
                                    self.prepareTooltipDescription($image, tooltipImage, '', '', value[valueId]['description']);
                                    $image.show();
                                } else {
                                    $image.hide();
                                }
                            } else {
                                if (!_.isUndefined(value[valueId]) &&
                                    !_.isEmpty(value[valueId]['images_data']['tooltip_image'])
                                ) {
                                    self.prepareTooltipDescription($image, tooltipImage);
                                    $image.show();
                                } else {
                                    $image.hide();
                                }
                            }
                        });
                    }

                    if ($element.val()) {
                        $element.trigger('change');
                    }
                });

                $options.filter('input[type="radio"], input[type="checkbox"]').each(function (index, element) {
                    var $element = $(element),
                        optionId = utils.findOptionId($element),
                        optionConfig = extendedOptionsConfig[optionId],
                        value = extendedOptionsConfig[optionId]['values'];

                    if (typeof value == 'undefined' || !value) {
                        return;
                    }

                    var valueId = $element.val(),
                        tooltipImage = self.getTooltipImageHtml(value[valueId]),
                        $image = self.getTooltipImageForOptionValue(valueId);

                    if (valueDescriptionEnabled) {
                        if (!_.isUndefined(value[valueId]) &&
                            (!_.isEmpty(value[valueId]['description']) ||
                                !_.isEmpty(value[valueId]['images_data']['tooltip_image']))
                        ) {
                            var description = value[valueId]['description'];
                            $element.parent().append($image);
                            self.prepareTooltipDescription($image, tooltipImage, '', '', description);
                        }
                    } else {
                        if (!_.isUndefined(value[valueId]) &&
                            !_.isEmpty(value[valueId]['images_data']['tooltip_image'])
                        ) {
                            $element.parent().append($image);
                            self.prepareTooltipDescription($image, tooltipImage);
                        }
                    }
                });
            },

            /**
             *
             * @param $element
             * @param tooltipImage
             * @param title
             * @param stockMessage
             * @param description
             */
            prepareTooltipDescription: function prepareTooltipDescription(
                $element,
                tooltipImage = '',
                title = '',
                stockMessage = '',
                description = '',
            ) {
                $element.qtip({
                    content: {
                        text: tooltipImage + title + stockMessage + description
                    },
                    style: {
                        classes: 'qtip-light'
                    },
                    position: {
                        target: false
                    }
                });
            },

             /**
             * Collect Option's Price
             *
             * @param {array} optionConfigCurrent
             * @param {number} optionId
             * @param {number} valueId
             * @private
             */
            collectOptionPriceAndQty: function calculateOptionsPrice(optionConfigCurrent, optionId, valueId) {
                this.actualPriceInclTax = 0;
                this.actualPriceExclTax = 0;

                var config = this.base.options,
                    isOneTime = this.base.isOneTimeOption(optionId),
                    productQty = $(config.productQtySelector).val(),
                    qty = !_.isUndefined(optionConfigCurrent['qty']) ? optionConfigCurrent['qty'] : 1;
                this.getActualPrice(optionId, valueId, qty);
                if (productQty == 0) {
                    productQty = 1;
                }

                var oldPriceAmountInclTax = parseFloat(optionConfigCurrent.prices.oldPrice.amount_incl_tax),
                    oldPriceAmountExclTax = parseFloat(optionConfigCurrent.prices.oldPrice.amount_excl_tax),
                    finalPriceAmount = parseFloat(optionConfigCurrent.prices.finalPrice.amount),
                    basePriceAmount = parseFloat(optionConfigCurrent.prices.basePrice.amount),
                    actualFinalPrice = this.actualPriceInclTax ? this.actualPriceInclTax : finalPriceAmount,
                    actualBasePrice = this.actualPriceExclTax ? this.actualPriceExclTax : basePriceAmount,
                    actualFinalPricePerItem = this.actualPriceInclTax ? this.actualPriceInclTax : finalPriceAmount,
                    actualBasePricePerItem = this.actualPriceExclTax ? this.actualPriceExclTax : basePriceAmount,
                    oldPriceInclTax = !isNaN(oldPriceAmountInclTax) ? oldPriceAmountInclTax : this.actualPriceInclTax,
                    oldPriceExclTax = !isNaN(oldPriceAmountExclTax) ? oldPriceAmountExclTax : this.actualPriceExclTax,
                    oldPricePerItemInclTax = oldPriceInclTax,
                    oldPricePerItemExclTax = oldPriceExclTax;

                if (isOneTime) {
                    actualFinalPricePerItem = actualFinalPricePerItem / productQty;
                    actualBasePricePerItem = actualBasePricePerItem / productQty;
                    oldPricePerItemInclTax = oldPricePerItemInclTax / productQty;
                    oldPricePerItemExclTax = oldPricePerItemExclTax / productQty;
                }

                if (!isOneTime
                    && (this.options.product_price_display_mode === 'final_price'
                        || this.options.additional_product_price_display_mode === 'final_price'
                    )
                ) {
                    actualFinalPrice *= productQty;
                    actualBasePrice *= productQty;
                    oldPriceInclTax *= productQty;
                    oldPriceExclTax *= productQty;
                    
                }

                this.optionFinalPricePerItem += actualFinalPricePerItem * qty;
                this.optionBasePricePerItem += actualBasePricePerItem * qty;
                this.optionOldPricePerItemInclTax += oldPricePerItemInclTax * qty;
                this.optionOldPricePerItemExclTax += oldPricePerItemExclTax * qty;

                this.optionFinalPrice += actualFinalPrice * qty;
                this.optionBasePrice += actualBasePrice * qty;
                this.optionOldPriceInclTax += oldPriceInclTax * qty;
                this.optionOldPriceExclTax += oldPriceExclTax * qty;

                var localStorageOfPrice = localStorage.getItem("promoBrandCurrentVat"),
                    finalTierPrice = ("exc" === localStorageOfPrice) ? actualBasePrice : actualFinalPrice;

                if (finalTierPrice > 0) {
                    $('.product-qty .qty-number span').text(productQty);
                    $('.product-netprice .total-price').text(utils.formatPrice(finalTierPrice));
                    if("exc" === localStorageOfPrice) {
                        $('.product-customisation [data-role="priceBox"] .price-excluding-tax').removeClass('hide');
                        $('.product-customisation [data-role="priceBox"] .price-including-tax').addClass('hide');
                    } else {
                        $('.product-customisation [data-role="priceBox"] .price-excluding-tax').addClass('hide');
                        $('.product-customisation [data-role="priceBox"] .price-including-tax').removeClass('hide');
                    }
                }
            },

            /**
             * Triggers each time after the all updates when option was changed (from the base.js)
             * @param base
             * @param productConfig
             */
            applyChanges: function (base, productConfig) {
                this.base = base;

                var isAbsolutePriceUsed = true;
                if (_.isUndefined(productConfig.absolute_price) || productConfig.absolute_price == "0") {
                    isAbsolutePriceUsed = false;
                }

                if (productConfig.type_id == 'configurable' && !isAbsolutePriceUsed) {


                    var form = this.base.getFormElement(),
                        qty = $("#qty").val(),
                        config = this.base.options,
                        options = $(config.optionsSelector, form);

                    options.filter('select').each(function (index, element) {
                        var $element = $(element),
                            optionId = utils.findOptionId($element),
                            optionConfig = config.optionConfig && config.optionConfig[optionId],
                            values = $element.val();

                        if (_.isUndefined(values) || !values) {
                            return;
                        }

                        if (!Array.isArray(values)) {
                            values = [values];
                        }

                        $(values).each(function (i, valueId) {
                            if (_.isUndefined(optionConfig[valueId])) {
                                if ($element.closest('.field').css('display') == 'none') {
                                    $element.val('');
                                    return;
                                }
                            }
                        });
                    });

                    options.filter('input[type="radio"], input[type="checkbox"]').each(function (index, element) {
                        var $element = $(element),
                            optionId = utils.findOptionId($element),
                            valueId = $element.val();

                        if (!$element.is(':checked')) {
                            return;
                        }

                        if (typeof valueId == 'undefined' || !valueId) {
                            return;
                        }

                        if ($element.closest('.field').css('display') == 'none') {
                            $element.val('');
                            return;
                        }
                    });

                    options.filter('input[type="text"], textarea, input[type="file"]').each(function (index, element) {
                        var $element = $(element),
                            optionId = utils.findOptionId($element),
                            value = $element.val();

                        if (typeof value == 'undefined' || !value) {
                            if ($('#delete-options_' + optionId + '_file').length < 1) {
                                return;
                            }
                        }

                        if ($element.closest('.field').css('display') == 'none') {
                            $element.val('');
                            return;
                        }
                    });
                    var e = localStorage.getItem("promoBrandCurrentVat"),
                        str = ("exc" === e) ? $(
                        '.product-customisation [data-role="priceBox"] .price-excluding-tax .price'
                        ) : $(
                        '.product-customisation [data-role="priceBox"] .price-including-tax .price'
                        ),
                        price = str.text().slice(1).replace(",", '');

                    if($())
                    
                    $('.product-qty .qty-number span').text(qty);
                    $('.product-netprice .total-price').text(utils.formatPrice(price * qty));
                    return;
                }

                if (_.isUndefined(productConfig.isUsedDynamicOptions)) {
                    productConfig.isUsedDynamicOptions = false;
                }

                this.initProductPrice(productConfig);
                this.calculateSelectedOptionsPrice();
                this.applyProductPriceDisplayMode();

                if (!isAbsolutePriceUsed ||
                    (isAbsolutePriceUsed && this.optionBasePrice <= 0) ||
                    productConfig.isUsedDynamicOptions
                ) {
                    this.productDefaultRegularPriceExclTax += parseFloat(this.optionOldPricePerItemExclTax);
                    this.productDefaultFinalPriceExclTax += parseFloat(this.optionBasePricePerItem);
                    this.productDefaultRegularPriceInclTax += parseFloat(this.optionOldPricePerItemInclTax);
                    this.productDefaultFinalPriceInclTax += parseFloat(this.optionFinalPricePerItem);

                    this.productPerItemRegularPriceExclTax += parseFloat(this.optionOldPricePerItemExclTax);
                    this.productPerItemFinalPriceExclTax += parseFloat(this.optionBasePricePerItem);
                    this.productPerItemRegularPriceInclTax += parseFloat(this.optionOldPricePerItemInclTax);
                    this.productPerItemFinalPriceInclTax += parseFloat(this.optionFinalPricePerItem);

                    this.productTotalRegularPriceExclTax += parseFloat(this.optionOldPriceExclTax);
                    this.productTotalFinalPriceExclTax += parseFloat(this.optionBasePrice);
                    this.productTotalRegularPriceInclTax += parseFloat(this.optionOldPriceInclTax);
                    this.productTotalFinalPriceInclTax += parseFloat(this.optionFinalPrice);
                } else {
                    this.productDefaultRegularPriceExclTax = parseFloat(this.optionOldPricePerItemExclTax);
                    this.productDefaultFinalPriceExclTax = parseFloat(this.optionBasePricePerItem);
                    this.productDefaultRegularPriceInclTax = parseFloat(this.optionOldPricePerItemInclTax);
                    this.productDefaultFinalPriceInclTax = parseFloat(this.optionFinalPricePerItem);

                    this.productPerItemRegularPriceExclTax = parseFloat(this.optionOldPricePerItemExclTax);
                    this.productPerItemFinalPriceExclTax = parseFloat(this.optionBasePricePerItem);
                    this.productPerItemRegularPriceInclTax = parseFloat(this.optionOldPricePerItemInclTax);
                    this.productPerItemFinalPriceInclTax = parseFloat(this.optionFinalPricePerItem);

                    this.productTotalRegularPriceExclTax = parseFloat(this.optionOldPriceExclTax);
                    this.productTotalFinalPriceExclTax = parseFloat(this.optionBasePrice);
                    this.productTotalRegularPriceInclTax = parseFloat(this.optionOldPriceInclTax);
                    this.productTotalFinalPriceInclTax = parseFloat(this.optionFinalPrice);
                }

                // Set product prices according to price's display mode on the product view page
                // 1 - without tax
                // 2 - with tax
                // 3 - both (with and without tax)
                if (base.getPriceDisplayMode() == 1) {
                    if (this.options.product_price_display_mode === 'per_item') {
                        base.setProductRegularPrice(this.productPerItemRegularPriceExclTax);
                        base.setProductFinalPrice(this.productPerItemFinalPriceExclTax);
                    } else if (this.options.product_price_display_mode === 'final_price') {
                        base.setProductRegularPrice(this.productTotalRegularPriceExclTax);
                        base.setProductFinalPrice(this.productTotalFinalPriceExclTax);
                    } else if (this.options.product_price_display_mode === 'disabled') {
                        base.setProductRegularPrice(this.productDefaultRegularPriceExclTax);
                        base.setProductFinalPrice(this.productDefaultFinalPriceExclTax);
                    }
                    if (this.options.additional_product_price_display_mode === 'per_item') {
                        base.setAdditionalProductRegularPrice(this.productPerItemRegularPriceExclTax);
                        base.setAdditionalProductFinalPrice(this.productPerItemFinalPriceExclTax);
                    } else if (this.options.additional_product_price_display_mode === 'final_price') {
                        base.setAdditionalProductRegularPrice(this.productTotalRegularPriceExclTax);
                        base.setAdditionalProductFinalPrice(this.productTotalFinalPriceExclTax);
                    } else if (this.options.additional_product_price_display_mode === 'disabled') {
                        base.setAdditionalProductRegularPrice(this.productDefaultRegularPriceExclTax);
                        base.setAdditionalProductFinalPrice(this.productDefaultFinalPriceExclTax);
                    }
                } else {
                    if (this.options.product_price_display_mode === 'per_item') {
                        base.setProductRegularPrice(this.productPerItemRegularPriceInclTax);
                        base.setProductFinalPrice(this.productPerItemFinalPriceInclTax);
                    } else if (this.options.product_price_display_mode === 'final_price') {
                        base.setProductRegularPrice(this.productTotalRegularPriceInclTax);
                        base.setProductFinalPrice(this.productTotalFinalPriceInclTax);
                    } else if (this.options.product_price_display_mode === 'disabled') {
                        base.setProductRegularPrice(this.productDefaultRegularPriceInclTax);
                        base.setProductFinalPrice(this.productDefaultFinalPriceInclTax);
                    }
                    if (this.options.additional_product_price_display_mode === 'per_item') {
                        base.setAdditionalProductRegularPrice(this.productPerItemRegularPriceInclTax);
                        base.setAdditionalProductFinalPrice(this.productPerItemFinalPriceInclTax);
                    } else if (this.options.additional_product_price_display_mode === 'final_price') {
                        base.setAdditionalProductRegularPrice(this.productTotalRegularPriceInclTax);
                        base.setAdditionalProductFinalPrice(this.productTotalFinalPriceInclTax);
                    } else {
                        base.setAdditionalProductRegularPrice(this.productDefaultRegularPriceInclTax);
                        base.setAdditionalProductFinalPrice(this.productDefaultFinalPriceInclTax);
                    }
                }
                if (this.options.product_price_display_mode === 'per_item') {
                    base.setProductPriceExclTax(this.productPerItemFinalPriceExclTax);
                } else if (this.options.product_price_display_mode === 'final_price') {
                    base.setProductPriceExclTax(this.productTotalFinalPriceExclTax);
                } else if (this.options.product_price_display_mode === 'disabled') {
                    base.setProductPriceExclTax(this.productDefaultFinalPriceExclTax);
                }
                if (this.options.additional_product_price_display_mode === 'per_item') {
                    base.setAdditionalProductPriceExclTax(this.productPerItemFinalPriceExclTax);
                } else if (this.options.additional_product_price_display_mode === 'final_price') {
                    base.setAdditionalProductPriceExclTax(this.productTotalFinalPriceExclTax);
                } else {
                    base.setAdditionalProductPriceExclTax(this.productDefaultFinalPriceExclTax);
                }
            },
        });
        return $.mageworx.optionFeatures;
    }
});