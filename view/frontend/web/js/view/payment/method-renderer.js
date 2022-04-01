define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list',
    'Magento_Checkout/js/model/totals'
], function (Component, rendererList, totals) {
    'use strict';
        if (parseFloat(totals.getSegment('grand_total').value) >= 300) {
            rendererList.push(
                {
                    type: 'anyday',
                    component: 'Anyday_Payment/js/view/payment/method-renderer/adpayment'
                }
            );
        }
        return Component.extend({});
    }
);
