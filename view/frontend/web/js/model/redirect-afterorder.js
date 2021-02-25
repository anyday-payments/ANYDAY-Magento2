/**
 * @api
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/full-screen-loader',
    ],
    function ($,
              quote,
              urlBuilder,
              storage,
              errorProcessor,
              customer,
              methodConverter,
              paymentService,
              fullScreenLoader,
              _) {
        'use strict';

        return function () {
            let serviceUrl;


            serviceUrl = urlBuilder.createUrl('/anyday-url/:cartId/', {
                cartId: quote.getQuoteId()
            });
            fullScreenLoader.startLoader();
            return storage.get(
                serviceUrl, false
            );
        };
    }
);
