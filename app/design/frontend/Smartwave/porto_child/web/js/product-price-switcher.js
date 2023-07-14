require(['jquery', 'Magento_Catalog/js/price-utils', 'mage/translate'], function($, priceUtils){
    $(document).ready( function ($) {
        var divCheckingInterval = setInterval(function(){
            if ($('body').is(".catalog-product-view")) {
                i();
                clearInterval(divCheckingInterval);
            }
        }, 100);

        $(document).ajaxStop(function () {
            i();
        });
        
        function i() {
            var v = "promoBrandCurrentVat";
                l = document.getElementsByClassName("js-vat-switch--exc"), 
                a = document.getElementsByClassName("js-vat-switch--inc"), 
                d = document.getElementsByClassName("price-excluding-tax"), 
                c = document.getElementsByClassName("price-including-tax"), 
                u = o.bind(null, l, a, d, c, "exc"), 
                p = o.bind(null, a, l, c, d, "inc"),
            r();

            var e = localStorage.getItem(v);

            e ? ("exc" === e) ? u() : p() : u();
        }

        function getFormattedPrice (price) {
            return priceUtils.formatPrice(price);
        }

        function finalPrice() {
            var e = localStorage.getItem("promoBrandCurrentVat"),
                qty = $("#qty").val(),
                str = ("exc" === e) ? $('.product-customisation [data-role="priceBox"] .price-excluding-tax .price').text() : $('.product-customisation [data-role="priceBox"] .price-including-tax .price').text(),
                priceValue = str.slice(1).replace(",", '');

                if("exc" === e) {
                    console.log('Productwefwefewfew')
                    $('.product-customisation [data-role="priceBox"] .price-excluding-tax').removeClass('hide');
                    $('.product-customisation [data-role="priceBox"] .price-including-tax').addClass('hide');
                } else {
                    $('.product-customisation [data-role="priceBox"] .price-excluding-tax').addClass('hide');
                    $('.product-customisation [data-role="priceBox"] .price-including-tax').removeClass('hide');
                }

            $('.product-netprice .total-price').text(getFormattedPrice(priceValue * qty));
        }

        function o(e, t, n, i, o) {
            var v = "promoBrandCurrentVat";
            localStorage.setItem(v, o);

            var r = Math.max(e.length, t.length, n.length, i.length);

            for (var a = 0; a < r; a++) {
                void 0 !== e[a] && s(t[a], "icon-tick") && t[a].classList.remove("icon-tick");
                void 0 !== t[a] && !1 === s(e[a], "icon-tick") && (e[a].className += " icon-tick");
                void 0 !== i[a] && (i[a].style.display = "none");
                void 0 !== n[a] && (n[a].style.display = "inline");
                finalPrice();
            }
        }
        function r() {
            for (var e = Math.max(a.length, l.length), t = 0; t < e; t++) void 0 !== l[t] && l[t].addEventListener("click", u), void 0 !== a[t] && a[t].addEventListener("click", p)
        }
        function s(e, t) {
            return (" " + e.className + " ").indexOf(" " + t + " ") > -1
        }
    });
});