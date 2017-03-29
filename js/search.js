/**
 * @file
 * JavaScript related to the site header search form
 */

(function ($, Drupal, window, document, undefined) {

  'use strict';

  Drupal.behaviors.govCMSParkesSearchInlineForm = {

    attach: function (context, settings) {

      // Stash the original button label
      this.originalSearchButtonValue = $('.govcms-parkes-search-inline-form .form-submit').attr('value');

      // Update the form on load and window resize.
      this.updateSearchForm();
      $(window).resize(function () {
        Drupal.behaviors.govCMSParkesSearchInlineForm.updateSearchForm();
      });

    },

    /**
     * Update the label of the search button when the widow is re-sized.
     *
     * The search button label is only shown on desktop. On mobile and tablet
     * an icon is shown on the button to save on space.
     */
    updateSearchForm: function () {
      var breakpoint = Drupal.behaviors.govCMSParkesBreakpoints.get();
      var button_label = '';

      if (breakpoint === 'md' || breakpoint === 'lg') {
        button_label = this.originalSearchButtonValue;
      }

      $('.govcms-parkes-search-inline-form .form-submit').attr('value', button_label);
    },

    /**
     * Contains the original value of the search submit button
     */
    originalSearchButtonValue: ''

  };

})(jQuery, Drupal, this, this.document);
