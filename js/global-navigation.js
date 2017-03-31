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
(function ($, Drupal, window, document, undefined) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.tofuSiteNavigation = {
    attach: function (context, settings) {

      // Do any initialisation work
      this.init();

      // Toggle menu
      $('.govcms-parkes-global-nav-toggle a').click(function (event) {
        event.preventDefault();
        Drupal.behaviors.tofuSiteNavigation.toggleMenu();
        return false;
      });

      // React to menu clicks for parent items in the active link set.
      $('.govcms-parkes-global-nav-sub-menu__parent > a').click(function (event) {
        if (Drupal.behaviors.tofuSiteNavigation.isOpen()) {
          event.preventDefault();
          if ($(this).parent().parent().hasClass('govcms-parkes-global-nav-sub-menu--active-tree')) {
            Drupal.behaviors.tofuSiteNavigation.openSubMenu($(this).parent());
          }
          return false;
        }
      });

      // React to sub-menu close clicks
      $('.govcms-parkes-global-nav-sub-menu__close > a').click(function (event) {
        if (Drupal.behaviors.tofuSiteNavigation.isOpen()) {
          event.preventDefault();
          if ($(this).parent().parent().hasClass('govcms-parkes-global-nav-sub-menu--active-tree')) {
            Drupal.behaviors.tofuSiteNavigation.closeSubMenu($(this).parent());
          }
          return false;
        }
      });

      // Respond to window resizing
      $(window).resize(function () {
        Drupal.behaviors.tofuSiteNavigation.windowResized();
      });

    },

    // The initial position of the menu to the left of the page in %
    positionInitial: 100,

    // The current position of the menu to the left of the page in %
    positionCurrent: 100,

    // Initialise the menu
    init: function () {
      var close = '<li class="govcms-parkes-global-nav-sub-menu__close"><a href="#">Back</li>';
      $('.govcms-parkes-global-nav-sub-menu ul').prepend(close);
      // $('.govcms-parkes-global-nav nav a').materialripple();
    },

    // Open the menu when the toggle button is clicked
    openMenu: function () {
      $('.govcms-parkes-global-nav nav').addClass('govcms-parkes-global-nav--open');
      $('.govcms-parkes-global-nav nav > ul').addClass('govcms-parkes-global-nav-sub-menu--active-tree');
      this.slide('left');
    },

    // Close the menu when the toggle button is clicked
    closeMenu: function () {
      this.slide('right', this.positionInitial, function () {
        $('.govcms-parkes-global-nav-sub-menu--active-tree').removeClass('govcms-parkes-global-nav-sub-menu--active-tree');
        $('.parent--open').removeClass('parent--open');
        $('.govcms-parkes-global-nav-sub-menu--open').removeClass('govcms-parkes-global-nav-sub-menu--open');
        $('.govcms-parkes-global-nav nav').removeClass('govcms-parkes-global-nav--open');
      });
    },

    // Slide the menu
    slide: function (direction, slide_to, callback) {

      // Determine where we are sliding the menus to
      if (slide_to === undefined || slide_to === null) {
        if (direction === 'left') {
          this.positionCurrent -= 90;
        }
        else {
          this.positionCurrent += 90;
        }
      }
      else {
        this.positionCurrent = slide_to;
      }

      // Slide the menus
      $('.govcms-parkes-global-nav nav').animate({left: this.positionCurrent + '%'}, {duration: 500, complete: callback});
    },

    // Open a sub-menu
    openSubMenu: function (element) {
      $(element).addClass('parent--open');
      $(element).children('.govcms-parkes-global-nav-sub-menu').addClass('govcms-parkes-global-nav-sub-menu--open');
      $('.govcms-parkes-global-nav-sub-menu--active-tree').removeClass('govcms-parkes-global-nav-sub-menu--active-tree');
      $(element).children('.govcms-parkes-global-nav-sub-menu').children('ul').addClass('govcms-parkes-global-nav-sub-menu--active-tree');
      this.slide('left', null, function () {
        var a = $(element).children('.govcms-parkes-global-nav-sub-menu').children('ul').children('.govcms-parkes-global-nav-sub-menu__close').children('a');
        Drupal.behaviors.tofuSiteNavigation.focusOnLink(a);
      });
    },

    // Closes a sub-menu
    closeSubMenu: function (element) {
      this.slide('right', null, function () {
        $(element).parent().parent().parent().removeClass('parent--open');
        $(element).parent().parent().removeClass('govcms-parkes-global-nav-sub-menu--open');
        $('.govcms-parkes-global-nav-sub-menu--active-tree').removeClass('govcms-parkes-global-nav-sub-menu--active-tree');
        $(element).parent().parent().parent().parent().addClass('govcms-parkes-global-nav-sub-menu--active-tree');
        var a = $(element).parent().parent().parent().children('a');
        Drupal.behaviors.tofuSiteNavigation.focusOnLink(a);
      });
    },

    // Opens or closes menu depending on state
    toggleMenu: function () {
      $('.govcms-parkes-global-nav-toggle a').toggleClass('govcms-parkes-global-nav-toggle--open');
      if (this.isOpen()) {
        this.closeMenu();
      }
      else {
        this.openMenu();
      }
    },

    // Determines if the mobile menu it open or not
    isOpen: function () {
      return $('.govcms-parkes-global-nav nav').hasClass('govcms-parkes-global-nav--open');
    },

    // Respond to window resize when the mobile menu should revert to
    // desktop without any mobile states
    windowResized: function () {
      if (this.isOpen() && !$('.govcms-parkes-global-nav-toggle').is(':visible')) {
        this.closeMenu();
      }
    },

    // Focus on a link when a submenu has been opened or closed
    focusOnLink: function (link) {

      // If the sub-menu that the link is in is currently not visible in the
      // viewport, then scroll the menu so they use can see it.
      if (!Drupal.behaviors.govCMSParkes.isElementInViewport(link)) {
        $('html, body').animate({
          scrollTop: link.offset().top
        }, 500, 'swing', function () {
          link.focus();
        });
      }

      // Otherwise just focus on the link
      else {
        link.focus();
      }
    }

  };

})(jQuery, Drupal, this, this.document);
