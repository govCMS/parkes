/**
 * @file
 * Basic or utility functions or code that are required for other theme scripts.
 */

(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.govCMSParkes = {

    /**
     * Anything that needs to respond to dom ready or bind to dom events should
     * go in here.  All heavy lifting should be farmed out to functions.
     *
     * @param context
     *   Reference to the DOM in play, initially this is the full DOM, this may
     *   be part of the DOM if this is an ajax call.
     *
     * @param settings
     *   The Drupal JS settings.
     */
    attach: function (context, settings) {

      // Setup an initial settings object for other JS code to put things in
      Drupal.settings.govCMSParkes = {};

    },

    /**
     * Determines if an element is within the viewport.
     *
     * @param element
     *
     * @returns {boolean}
     *
     * @see http://stackoverflow.com/questions/123999
     */
    isElementInViewport: function (element) {

      var rect = element[0].getBoundingClientRect();

      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= $(window).height() &&
        rect.right <= $(window).width()
      );
    },

    /**
     * Converts a CSS pixel based value (eg 500px) and returns an integer (eg
     * 500).
     *
     * @param value
     *   The value to convert
     *
     * @return {Number}
     *   The converted value
     */
    convertCSSPxToInt: function (value) {
      return parseInt(value);
    }
  };

})(jQuery, Drupal, this, this.document);
