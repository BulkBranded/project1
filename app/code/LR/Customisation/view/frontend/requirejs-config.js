var config = {
    config: {
        mixins: {
            'Magento_Catalog/js/price-options': {
                'LR_Customisation/js/price-option-mixin': true
            }
        }
    },
    map: {
        '*': {
            'jqueryUiSlider': 'LR_Customisation/js/jquery-ui-slider-pips.min',
            quotationPdf: 'LR_Customisation/js/quotation/custom-js'
        },
    },
    shim: {
        'LR_Customisation/js/jquery-ui-slider-pips.min': {
            deps: ['jquery']
        }
    }
};