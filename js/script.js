/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.govCMSParkes = {

    /**
     * Anything that needs to respond to dom ready or bind to dom events should
     * go in here.  All heavy lifting should be farmed out to functions.
     */
    attach: function (context, settings) {

      // Setup an initial settings object for other JS code to put things in
      Drupal.settings.govCMSParkes = {};

    },

    /**
     * Determines if an element is within the viewport
     *
     * @param element
     *
     * @returns {boolean}
     *
     * @see http://stackoverflow.com/questions/123999
     */
    isElementInViewport: function (element) {

      // Special bonus for those using jQuery
      element = element[0];

      var rect = element.getBoundingClientRect();

      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= $(window).height() &&
        rect.right <= $(window).width()
      );
    }
  };

})(jQuery, Drupal, this, this.document);
