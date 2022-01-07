define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ], function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'anyday',
                component: 'Anyday_Payment/js/view/payment/method-renderer/adpayment'
            }
        );

        return Component.extend({});
    }
);
