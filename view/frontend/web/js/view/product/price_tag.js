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
            this.moveTag();
            return this._super();
        },

        moveTag: function () {
            if (anyDayTag.length && window.anydaytag.select_tag && window.anydaytag.name_select_tag) {
                var selectElement = jQuery(window.anydaytag.name_select_tag);
                if (selectElement.length) {
                    anyDayTag.insertAfter(selectElement);
                }
            }
            this.visibleTagElement(true);
        }
    });
});
