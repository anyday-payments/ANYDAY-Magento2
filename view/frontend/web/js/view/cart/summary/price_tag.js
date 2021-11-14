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
                return totals['base_grand_total'];
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
            let anyDayTag = jQuery('.adtag-item');
            eval(window.anydaytag.inline_css);
            if (anyDayTag.length && window.anydaytag.select_tag && window.anydaytag.name_select_tag) {
                let selectElement = jQuery(window.anydaytag.name_select_tag);
                if (selectElement.length) {
                    anyDayTag.insertAfter(selectElement);
                }
            }
        }
    });
});
