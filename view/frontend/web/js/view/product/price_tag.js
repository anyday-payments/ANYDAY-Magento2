define([
    'uiElement',
    'jquery',
    'ko'
], function(Component, $, ko) {
    'use strict';

    let anyDayTag = jQuery('.adtag-info');

    return Component.extend({
        visibleTagElement: ko.observable(false),

        initialize: function () {
            var self = this;
            this.moveTag();
            return this._super();
        },

        moveTag: function () {
            if (anyDayTag.length && window.anydaytag.select_tag && window.anydaytag.name_select_tag) {
                var selectElement = '';
                switch (window.anydaytag.select_tag) {
                    case '3':
                        selectElement = jQuery('.' + window.anydaytag.name_select_tag);
                        break;
                    case '1':
                        selectElement = jQuery('#' + window.anydaytag.name_select_tag);
                        break;
                    case '2':
                        selectElement = jQuery("[name='"+ window.anydaytag.name_select_tag +"']");
                        break;
                }
                if (selectElement.length) {
                    anyDayTag.insertAfter(selectElement);
                }
            }
            this.visibleTagElement(true);
        }
    });
});
