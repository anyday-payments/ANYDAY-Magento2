define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Anyday_PaymentAndTrack/js/model/redirect-afterorder',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, redirectUrlAny, quote) {
        'use strict';

        let selfThis = this;

        let getUrlAnyday = function () {
            redirectUrlAny().done(function (response) {
                console.log(response);
                console.log(JSON.stringify(response));
                let dataJson = JSON.parse(response);
                //window.location.replace(dataJson.url);
                window.location.href = dataJson.url;
            });
        };

        return Component.extend({
            defaults: {
                template: 'Anyday_PaymentAndTrack/payment/anydaytemplate'
            },

            /**
             * After place order callback
             */
            afterPlaceOrder: function () {
                this.redirectAfterPlaceOrder = false;
                getUrlAnyday();
                // Override this function and put after place order logic here
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
                eval(window.anydaytag.inline_css);
            },

            getLogoUrl: function () {
                return window.anydaytag.logo_url;
            }
        });
    }
);
