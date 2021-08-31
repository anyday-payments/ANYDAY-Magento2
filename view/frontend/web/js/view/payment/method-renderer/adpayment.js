define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Anyday_PaymentAndTrack/js/model/redirect-afterorder'
    ],
    function (Component, redirectUrlAny) {
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

            getLogoUrl: function () {
                return window.anydaytag.logo_url;
            }
        });
    }
);
