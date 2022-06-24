define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Anyday_Payment/js/model/redirect-afterorder',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, redirectUrlAny, quote, totals) {
        'use strict';

        let getUrlAnyday = function () {
            redirectUrlAny().done(function (response) {
                console.log(response);
                console.log(JSON.stringify(response));
                let dataJson = JSON.parse(response);
                console.log(dataJson.url);
                //window.location.replace(dataJson.url);
                window.location.href = dataJson.url;
            });
        };

        return Component.extend({
            defaults: {
                template: 'Anyday_Payment/payment/anydaytemplate'
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
                if (window.anydaytag.is_enable && parseFloat(totals.getSegment('grand_total').value) >= 300 && parseFloat(totals.getSegment('grand_total').value) <= 30000) {
                    return true;
                }
                return false;
            },

            /**
             * @return {*}
             */
             isPaymentMethodTagDisplayed: function () {
                if (window.anydaytag.is_payment_method_tag_enabled) {
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

            movePaymentMethodElement: function () {
                eval(window.anydaytag.paymentmethod_inline_css);
            },

            getLogoUrl: function () {
                return window.anydaytag.logo_url;
            }
        });
    }
);
