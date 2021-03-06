/**
 * @file
 * JavaScript to make the current breakpoint available as a Drupal setting and
 * also as a class on the body.
 *
 * This allows JavaScript to be written that is sensitive to breakpoints and
 * also allows the writing of responsive CSS within Drupal when access to the
 * SASS is impossible.
 */

(function ($, Drupal, window, document, undefined) {

  'use strict';

  Drupal.behaviors.govCMSParkesBreakpoints = {

    attach: function (context, settings) {

      this.refreshValue();

      $(window).resize(function () {
        Drupal.behaviors.govCMSParkesBreakpoints.refreshValue();
      });

    },

    /**
     * Updates the value of the breakpoint in the settings array.
     *
     * Additionally triggers the function to update the body class.
     */
    refreshValue: function () {
      Drupal.settings.govCMSParkes.breakpoint = window.getComputedStyle(document.querySelector('body'), ':before').getPropertyValue('content').replace(/\"/g, '');
      this.refreshClass();
    },

    /**
     * Updates the body class.
     */
    refreshClass: function () {
      $('body').removeClass(function (index, className) {
        return (className.match(/(^|\s)govcms-parkes-breakpoint--\S+/g) || []).join(' ');
      }).addClass('govcms-parkes-breakpoint--' + Drupal.settings.govCMSParkes.breakpoint);
    },

    /**
     * Get the current breakpoint
     *
     * @return {String}
     *   Returns the UI Kit identifier for a breakpoint: xs, sm, md, lg
     */
    get: function () {
      return Drupal.settings.govCMSParkes.breakpoint;
    }

  };

})(jQuery, Drupal, this, this.document);
