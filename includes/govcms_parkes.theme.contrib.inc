<?php

/**
 * @file
 * Contains the contib module theme functions.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */

/**
 * Implement THEME_toc_filter().
 */
function govcms_parkes_toc_filter($variables) {
  $output = '';
  $output .= '<nav class="uikit-inpage-nav-links">';
  $output .= '<h2 id="uikit-inpage-nav-links__heading uikit-display-5">' . t('Contents') . '</h2>';
  $output .= $variables['content'];
  $output .= '</nav>';
  return $output;
}

/**
 * Implements THEME_toc_filter_back_to_top().
 */
function govcms_parkes_toc_filter_back_to_top($variables) {
  return '<a class="toc-back-to-index uikit-direction-link uikit-direction-link--up" href="#">' . t('Back to contents') . '</a>';
}
