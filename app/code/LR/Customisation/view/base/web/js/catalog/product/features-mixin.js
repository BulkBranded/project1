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
        });
        return $.mageworx.optionFeatures;
    }
});