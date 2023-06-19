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
            e ? "exc" === e ? u() : "inc" === e && p() : u();
        }

        function getFormattedPrice (price) {
            return priceUtils.formatPrice(price);
        }

        function finalPrice() {
            var e = localStorage.getItem("promoBrandCurrentVat"),
                qty = $("#qty").val(),
                str = ("exc" === e) ? $('[data-role="priceBox"] .price-including-tax .price').text() : $('[data-role="priceBox"] .price-excluding-tax .price').text(),
                priceValue = str.slice(1, str.length - 3).replace(",", '');

            $('.product-netprice .total-price').text(getFormattedPrice(priceValue * qty));
        }

        function o(e, t, n, i, o) {
            var v = "promoBrandCurrentVat";
            localStorage.setItem(v, o);
            for (var r = Math.max(e.length, t.length, n.length, i.length), a = 0; a < r; a++) void 0 !== e[a] && s(e[a], "icon-tick") && e[a].classList.remove("icon-tick"), void 0 !== t[a] && !1 === s(t[a], "icon-tick") && (t[a].className += " icon-tick"), void 0 !== n[a] && (n[a].style.display = "none"), finalPrice(), void 0 !== i[a] && (i[a].style.display = "inline")
        }
        function r() {
            for (var e = Math.max(a.length, l.length), t = 0; t < e; t++) void 0 !== a[t] && a[t].addEventListener("click", u), void 0 !== l[t] && l[t].addEventListener("click", p)
        }
        function s(e, t) {
            return (" " + e.className + " ").indexOf(" " + t + " ") > -1
        }
    });
});