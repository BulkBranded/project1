define([
    'jquery',
    'uiRegistry'
], function ($, uiRegistry) {
    'use strict';

    return function (config, element) {
        var form = $('.amasty-quote-update'),
            customerAttributesForm;

        $(element).click(function (event) {
            event.preventDefault();
            var detailsForm = $('[data-form-js="am-details-form"]'),
                eventData = {isValid: true};

            eventData.isValid = eventData.isValid && form.valid();
            eventData.isValid = eventData.isValid && detailsForm.valid();

            customerAttributesForm = uiRegistry.get('details.customer-attributes');
            if (customerAttributesForm && !customerAttributesForm.validate()) {
                eventData.isValid = false;
                customerAttributesForm.focusInvalid();
            }

            $('body').trigger('validateRequestQuoteForm', eventData);

            if (eventData.isValid) {
                $('<input></input>').attr('type', 'hidden')
                    .attr('name', 'remarks')
                    .attr('value', detailsForm.find('[name="quote_remark"]').val())
                    .appendTo(form);
                $('<input></input>').attr('type', 'hidden')
                    .attr('name', 'update_cart_action')
                    .attr('value', 'submit')
                    .appendTo(form);
                $('<input></input>').attr('type', 'hidden')
                    .attr('name', 'email')
                    .attr('value', detailsForm.find('[name="username"]').val())
                    .appendTo(form);

                detailsForm.find('input, textarea, select').each(function (index, input) {
                    var newInput = $('<input></input>').attr('type', 'hidden')
                        .attr('name', $(input).attr('name'))
                        .attr('value', $(input).val())
                        .appendTo(form);
                    if ($(input).attr('type') === 'file') {
                        newInput.attr('type', 'file');
                        newInput.files = input.files;
                        $(input).removeAttr('name');
                        $('[name="' + newInput.attr('name') + '"]')[0].files = input.files;
                    }
                });

                $(element).attr('disabled', true);

                $('body').trigger('beforeSubmitRequestQuoteForm', {form: form});

                form.submit();
            }
        });
    };
});
