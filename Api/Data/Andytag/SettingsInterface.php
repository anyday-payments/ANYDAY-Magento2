<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Api\Data\Andytag;

interface SettingsInterface
{
    const PATH_ENABLE_TAG_MODULE                = 'anyday/tagmodule/enable';
    const PATH_ENABLE_PAYMENT_MODULE            = 'anyday/paymentmodule/enable';
    const PATH_TO_TAG_TOKEN                     = 'anyday/tagmodule/tag_token';
    const PATH_ENABLE_TAG_CATEGORY              = 'anyday/tagmodule/category/enable';
    const PATH_TO_INLINECSS_CATEGORY            = 'anyday/tagmodule/category/inline_css';
    const PATH_ENABLE_TAG_PRODUCT               = 'anyday/tagmodule/product/enable';
    const PATH_TO_INLINECSS_PRODUCT             = 'anyday/tagmodule/product/inline_css';
    const PATH_TO_SELECT_TYPE_ELEMENT_PRODUCT   = 'anyday/tagmodule/product/select_type_element';
    const PATH_TO_SELECT_TAG_ELEMENT_PRODUCT    = 'anyday/tagmodule/product/tag_element';
    const PATH_ENABLE_TAG_CART                  = 'anyday/tagmodule/cart/enable';
    const PATH_TO_INLINECSS_CART                = 'anyday/tagmodule/cart/inline_css';
    const PATH_TO_SELECT_TYPE_ELEMENT_CART      = 'anyday/tagmodule/cart/select_type_element';
    const PATH_TO_SELECT_TAG_ELEMENT_CART       = 'anyday/tagmodule/cart/tag_element';
    const PATH_ENABLE_TAG_CHECKOUT              = 'anyday/tagmodule/checkout/enable';
    const PATH_TO_INLINECSS_CHECKOUT            = 'anyday/tagmodule/checkout/inline_css';
    const PATH_TO_SELECT_TYPE_ELEMENT_CHECKOUT  = 'anyday/tagmodule/checkout/select_type_element';
    const PATH_TO_SELECT_TAG_ELEMENT_CHECKOUT   = 'anyday/tagmodule/checkout/tag_element';
    const PATH_TO_TOKEN_SANDBOX                 = 'payment/adpaymentmethod/tokensandbox';
    const PATH_TO_TOKEN_LIVE                    = 'payment/adpaymentmethod/tokenlive';
    const PATH_TO_STATUS_AFTER_INVOICE          = 'payment/anyday/status_after_invoice';
    const PATH_TO_PAYMENT_MODE_TYPE             = 'payment/anyday/mode_type';
    const PATH_TO_STATUS_AFTER_PAYMENT          = 'payment/anyday/order_status_payment';
    const PATH_TO_NEW_ORDER_STATUS              = 'payment/anyday/order_status';
}
