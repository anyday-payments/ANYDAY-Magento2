require([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert, $t) {
    window.anydayValidator = function (endpoint, env_id) {
        env_id = $('[data-ui-id="' + env_id + '"]').val();

        var email = '',password = '';
        email = jQuery('[data-ui-id="text-groups-adpayment-section-groups-anydaypayment-general-fields-anyday-email-value"]').val();
        password = jQuery('[data-ui-id="password-groups-adpayment-section-groups-anydaypayment-general-fields-anyday-password-value"]').val();

        /* Remove previous success message if present */
        if ($(".braintree-credentials-success-message")) {
            $(".braintree-credentials-success-message").remove();
        }

        /* Basic field validation */
        var errors = [];


        if (!email) {
            errors.push($t("Please enter a Email"));
        }

        if (!password) {
            errors.push($t('Please enter a Password'));
        }

        if (errors.length > 0) {
            alert({
                title: $t('ANYDAY Credential Validation Failed'),
                content:  errors.join('<br />')
            });
            return false;
        }

        $(this).text($t("We're validating your credentials...")).attr('disabled', true);

        var self = this;
        jQuery.ajax({
            url: endpoint,
            type: "POST",
            data: JSON.stringify( { data : JSON.stringify({
                email: email,
                password: password,
                type: window.adpayment.type,
                id: window.adpayment.id
            })}),
            showLoader: true,
            beforeSend: function(xhr){
                //Empty to remove magento's default handler
            },
            contentType:"application/json; charset=utf-8",
            dataType:"json"}
            ).done(function (data) {
                result = JSON.parse(data);
                if (result['code'] == 'error') {
                    alert({
                        title: $t('ANYDAY Credential Validation Failed'),
                        content: $t(result['result'])
                    });
                } else {
                    $('<div class="message message-success braintree-credentials-success-message">' + $t("Your credentials are valid.") + '</div>').insertAfter(self);
                    saveParamToWindow(result['priceTagToken']);
                    saveParamToWindowTokenLive(result['live']);
                    saveParamToWindowTokenSandbox(result['sandbox']);
                }

            }).fail(function () {
                alert({
                    title: $t('ANYDAY Credential Validation Failed'),
                    content: $t('Your ANYDAY Credentials could not be validated.')
                });
            }).always(function () {
                $(self).text($t("Validate Credentials")).attr('disabled', false);
            });

        function saveParamToWindow(tagToken) {
            if(window.adpayment.type == 'websites' || window.adpayment.id > 1) {
                jQuery('#payment_us_adpayment_section_anydaypayment_token_general_tag_token_inherit').prop("checked", false);
            }
            jQuery('[data-ui-id="text-groups-adpayment-section-groups-anydaypayment-token-groups-general-fields-tag-token-value"]').val(tagToken);
        }

        function saveParamToWindowTokenLive(liveToken) {
            if(window.adpayment.type == 'websites' || window.adpayment.id > 1) {
                jQuery('#payment_us_adpayment_section_anydaypayment_method_live_inherit').prop("checked", false);
            }
            jQuery('[data-ui-id="text-groups-adpayment-section-groups-anydaypayment-method-fields-live-value"]').val(liveToken);
        }

        function saveParamToWindowTokenSandbox(sandToken) {
            if(window.adpayment.type == 'websites' || window.adpayment.id > 1) {
                jQuery('#payment_us_adpayment_section_anydaypayment_method_sandbox_inherit').prop("checked", false);
            }
            jQuery('[data-ui-id="text-groups-adpayment-section-groups-anydaypayment-method-fields-sandbox-value"]').val(sandToken);
        }
    }
});
