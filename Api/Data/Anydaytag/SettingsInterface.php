<?php
declare(strict_types=1);

namespace Anyday\Payment\Api\Data\Anydaytag;

interface SettingsInterface
{
    const PATH_ENABLE_TAG_MODULE                  = 'anyday/tagmodule/enable';
    const PATH_ENABLE_PAYMENT_MODULE              = 'anyday/paymentmodule/enable';
    const PATH_TO_TAG_TOKEN                       = 'anyday/tagmodule/tag_token';
    const PATH_ENABLE_TAG_CATEGORY                = 'anyday/tagmodule/category_enable';
    const PATH_TO_INLINECSS_CATEGORY              = 'anyday/tagmodule/category_inline_css';
    const PATH_ENABLE_TAG_PRODUCT                 = 'anyday/tagmodule/product_enable';
    const PATH_TO_INLINECSS_PRODUCT               = 'anyday/tagmodule/product_inline_css';
    const PATH_TO_SELECT_TAG_ELEMENT_PRODUCT      = 'anyday/tagmodule/product_tag_element';
    const PATH_ENABLE_TAG_CART                    = 'anyday/tagmodule/cart_enable';
    const PATH_TO_INLINECSS_CART                  = 'anyday/tagmodule/cart_inline_css';
    const PATH_TO_SELECT_TAG_ELEMENT_CART         = 'anyday/tagmodule/cart_tag_element';
    const PATH_ENABLE_TAG_CHECKOUT                = 'anyday/tagmodule/checkout_enable';
    const PATH_ENABLE_PAYMENT_METHOD_TAG_CHECKOUT = 'anyday/tagmodule/checkout_payment_method_enable';
    const PATH_TO_INLINECSS_CHECKOUT              = 'anyday/tagmodule/checkout_inline_css';
    const PATH_TO_INLINECSS_CHECKOUT_PAYMENT      = 'anyday/tagmodule/checkout_payment_method_inline_css';
    const PATH_TO_SELECT_TAG_ELEMENT_CHECKOUT     = 'anyday/tagmodule/checkout_tag_element';
    const PATH_TO_TOKEN_SANDBOX                   = 'payment/adpaymentmethod/tokensandbox';
    const PATH_TO_TOKEN_LIVE                      = 'payment/adpaymentmethod/tokenlive';
    const PATH_TO_STATUS_AFTER_INVOICE            = 'payment/anyday/status_after_invoice';
    const PATH_TO_PAYMENT_MODE_TYPE               = 'payment/anyday/mode_type';
    const PATH_TO_STATUS_AFTER_PAYMENT            = 'payment/anyday/order_status_payment';
    const PATH_TO_NEW_ORDER_STATUS                = 'payment/anyday/order_status';
}
