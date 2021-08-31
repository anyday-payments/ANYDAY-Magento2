/**
 * @api
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
    ],
    function ($,
              quote,
              urlBuilder,
              storage,
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
