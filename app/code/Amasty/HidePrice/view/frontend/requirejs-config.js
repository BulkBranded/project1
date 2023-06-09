var config = {
    map: {
        '*': {
            'amasty-fancyambox' : 'Amasty_HidePrice/js/fancyambox/jquery.fancyambox.min'
        }
    },
    config: {
        mixins: {
            'mage/validation': {
                'Amasty_HidePrice/js/validation-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Amasty_HidePrice/js/swatch-renderer-mixin': true
            },
            'Amasty_Conf/js/swatch-renderer': {
                'Amasty_HidePrice/js/swatch-renderer-mixin': true
            }
        }
    }
};
