define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Anyday_PaymentAndTrack/cart/summary/price_tag'
        },
        /**
         * @return {*|String}
         */
        tagToken: function () {
            return window.anydaytag.tag_code;
        },
        /**
         * @return {*|String}
         */
        getCurrencyCode:function () {
            return window.anydaytag.currency_code;
        },
        /**
         * @return {*}
         */
        isDisplayed: function () {
            if (window.anydaytag.is_enable) {
                return true;
            }
            return false;
        },

        /**
         * Get pure value.
         */
        getPureValue: function () {
            var totals = quote.getTotals()();

            if (totals) {
                return totals['grand_total'];
            }

            return quote['grand_total'];
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getPureValue();
        },

        moveElement: function () {
            eval(window.anydaytag.inline_css);
        }
    });
});
