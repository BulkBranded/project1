define([
    'jquery',
    'mage/url',
    'Magento_Ui/js/modal/modal',
    'mage/cookies'
], function ($, url, modal) {
    'use strict';
    var quotationPdf = function () {
        $(".download-pdf").unbind("click").bind("click", function (e) {
            quoteSubmit();
        });
    };

    function quoteSubmit() {
        var copyHtml = $("#product-options-wrapper");
        $("#product-options-wrapper").insertBefore("#customisation_right");
        if ($('#product_addtocart_form').valid()) {
            var form = $("#product_addtocart_form");
            var data = form.serialize();
            $("#quotation-details-content").html(copyHtml);
            var quoteUrl = url.build("amasty_quote/cart/add");
            $.ajax({
                url: quoteUrl,
                type: 'POST',
                data: data,
                showLoader: true,
                success: function (data) {
                    console.log(data);
                    if (data.suceess === true) {
                        downloadPdf(data);
                    } else {
                        alert("Something went wrong. Please try again");
                    }
                }
            });
        } else {
            $("#product-options-wrapper").appendTo("#quotation-details-content");
            return false;
        }
    }

    function downloadPdf(data) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: $('.page-title-wrapper.product .page-title .base').text(),
            clickableOverlay: false,
            buttons: [
                {
                    text: $.mage.__('Close'),
                    class: '',
                    click: function () {
                        $("#product-options-wrapper").insertBefore("#customisation_right");
                        this.closeModal();
                    }
                }
            ]
        };
        var incrementId = data.increment_id;
        var quoteUrl = data.quoteUrl;

        $.ajax({
            url: data.pdfUrl,
            type: 'POST',
            data: {"uenc": data.uenc, 'form_key': $.mage.cookies.get('form_key')},
            showLoader: true,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                var url = window.URL.createObjectURL(new Blob([data]));
                var a = document.createElement('a');
                a.href = url;
                a.download = 'Bulk_Brand_Quatation_'+incrementId+'.pdf';
                a.click();
                window.URL.revokeObjectURL(url);
                $("#product-options-wrapper").appendTo("#quotation-details-content");
                window.location.href = quoteUrl
            }, complete: function () {

            },
        });
    }

    return quotationPdf;
});
