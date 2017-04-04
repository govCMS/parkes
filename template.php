<?php

/**
 * @file
 * Contains the theme's functions to override markup.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */

include('includes/govcms_parkes.theme.core.inc');
include('includes/govcms_parkes.theme.contrib.inc');

/** Core hooks ****************************************************************/


/**
 * Implements hook_form_alter().
 */
function govcms_parkes_form_alter(&$form, &$form_state, $form_id) {

  // If this form is a search api form, we want to remove the size attribute
  // on the text input, it makes styling difficult. We also update the
  // placeholder and apply a class to the form for targeting in JS.
  if (strpos($form_id, 'search_api') !== FALSE) {
    $search_api_form_id = $form['id']['#value'];
    unset($form['keys_' . $search_api_form_id]['#size']);
    unset($form['keys_' . $search_api_form_id]['#attributes']['placeholder']);
    $form['#attributes']['class'] = 'govcms-parkes-search-inline-form';
  }

  // Workbench info block has a submit button that's too YUGE
  if ($form_id == 'workbench_moderation_moderate_form') {
    $form['submit']['#attributes']['class'][] = 'govcms-parkes-btn--small';
  }

}

/**
 * Implements hook_css_alter
 */
function govcms_parkes_css_alter(&$css) {
  // Unset some css files we don't want
  unset($css['modules/system/system.menus.css']);
}

/**
 * Implements hook_block_view_alter().
 */
function govcms_parkes_block_view_alter(&$data, $block) {

  // For the menu in the navigation bar, we want to rebuild the classes to
  // support our navigation UI
  // @todo support core menu blocks too
  if ($block->module == 'menu_block' && $block->region == 'navigation') {
    $data['content']['#content']['#theme_wrappers'] = array('menu_tree__global_navigation');
    _govcms_parkes_process_global_navigation_links($data['content']['#content']);
  }
}


/** Core pre-process functions ************************************************/

/**
 * Implements THEME_preprocess_html().
 */
function govcms_parkes_preprocess_html(&$variables) {
  // Make sure that we have our util module enabled
  if (!module_exists('govcms_parkes_util')) {
    drupal_set_message(t('The govCMS Parkes theme requires the govCMS Parkes Util module to function correctly.  Please enable this module.'), 'error');
  }

  // Add some body classes to get the body styling and grid
  $variables['classes_array'][] = 'uikit-body';
  $variables['classes_array'][] = 'uikit-grid';

  // Load the font UI kit uses
  drupal_add_css('https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700&subset=latin-ext', array('type' => 'external'));
}

/**
 * Implements THEME_preprocess_node().
 */
function govcms_parkes_preprocess_node(&$variables) {
  $variables['submitted'] = '<div class="govcms-parkes-node-submitted govcms-parkes-body-meta">' . t('Submitted by !author on <time>!date</time>', array('!date' => $variables['date'], '!author' => $variables['name']));
}

/**
 * Implements THEME_preprocess_page().
 */
function govcms_parkes_preprocess_page(&$variables) {
  $variables['page']['header'] = _govcms_parkes_preprocess_region_header($variables['page']['header']);

  // Determine the bootstrap classes to use for the main content area and the
  // left and right sidebars
  $has_left_sidebar  = !empty($variables['page']['sidebar_left']);
  $has_right_sidebar = !empty($variables['page']['sidebar_right']);

  switch (TRUE) {
    case ($has_left_sidebar && $has_right_sidebar):
      $variables['layout_classes'] = array(
        'sidebar_left'  => 'col-md-3',
        'content'       => 'col-md-6',
        'sidebar_right' => 'col-md-3',
      );
      break;

    case ($has_left_sidebar && !$has_right_sidebar):
      $variables['layout_classes'] = array(
        'sidebar_left'  => 'col-md-3',
        'content'       => 'col-md-9',
        'sidebar_right' => '',
      );
      break;

    case (!$has_left_sidebar && $has_right_sidebar):
      $variables['layout_classes'] = array(
        'sidebar_left'  => '',
        'content'       => 'col-md-9',
        'sidebar_right' => 'col-md-3',
      );
      break;

    default:
      $variables['layout_classes'] = array(
        'sidebar_left'  => '',
        'content'       => 'col-md-12',
        'sidebar_right' => '',

      );
      break;
  }

  // Footer crest
  // We use drupal_get_path() istead of theme_path() in case we're "subthemed"
  $variables['crest'] = theme('image', array(
    'path'       => drupal_get_path('theme', 'govcms_parkes') . '/images/coat-of-arms.png',
    'alt'        => t('Commonwealth Coat of Arms crest logo'),
    'attributes' => array('class' => array('uikit-responsive-media-img', 'govcms-parkes-footer__crest')),
  ));

  // Copyright
  $variables['copyright'] = '<div class="govcms-parkes-footer__details"><small>' . filter_xss(variable_get('govcms_parkes_footer_details', GOVCMS_PARKES_FOOTER_DETAILS)) . '</small></div>';

}

/**
 * Implements THEME_preprocess_maintenance_page().
 */
function govcms_parkes_preprocess_maintenance_page(&$variables) {
  $variables['header'] = _govcms_parkes_preprocess_region_header();
}

/**
 * Implements THEME_preprocess_block().
 */
function govcms_parkes_preprocess_block(&$variables) {

  $block = $variables['block'];

  // Add some classes to the block title and content wrapper
  $variables['title_attributes_array']['class'] = 'block__title';
  $variables['content_attributes_array']['class'] = 'block__content content';

  // Drupal menu blocks and the Menu Block module's blocks share the same
  // template file to apply the <nav> element.  We also switch template file if
  // the block is in a sidebar.
  if (
    in_array($block->module, array('menu', 'menu_block'))
    || ($block->module == 'system' && $block->delta == 'main-menu')
  ) {
    if (in_array($block->region, array('sidebar_left', 'sidebar_right'))) {
      array_unshift($variables['theme_hook_suggestions'], 'block__menu_generic_sidebar');
    }
    else {
      array_unshift($variables['theme_hook_suggestions'], 'block__menu_generic__' . $block->region);
      array_unshift($variables['theme_hook_suggestions'], 'block__menu_generic');
    }
  }

}


/** Helper functions **********************************************************/

/**
 * Gets the header content together.
 *
 * Turn the logo from a URL into an image within a link, and also scale it so
 * that it's no taller than specified in the theme settings.
 *
 * This may be useful to users who do not have the ability to adjust the image
 * size. It also allows the use of svg images where the ability set the image
 * size to a maximum height is useful.
 *
 * This can be overridden in CSS at various breakpoints if required for those
 * users who want to customise the theme.
 *
 * @param string $header_content
 *   The content that should go in the header's content region.
 *
 * @return string
 *   The header region content.
 *
 * @see govcms_parkes_preprocess_page().
 */
function _govcms_parkes_preprocess_region_header($header_content = '') {

  $output          = '';
  $branding_option = theme_get_setting('govcms_parkes_site_branding');
  $site_name       = variable_get('site_name', '');

  switch ($branding_option) {

    case 'logo':

      // Get the logo
      $logo_path = theme_get_setting('logo');

      // Create the image using theme_image().
      $link_text = theme('image', array(
        'path'       => $logo_path,
        'alt'        => t('@site_name logo', array('@site_name' => $site_name)),
        'title'      => filter_xss($site_name),
        'attributes' => array('class' => array('uikit-responsive-img')),
      ));

      $output .= l($link_text, '<front>', array('html' => TRUE, 'attributes' => array('class' => array('govcms-parkes-header-logo-link'))));

      break;

    case 'name':
      $site_slogan = variable_get('site_slogan', '');

      $link_text = '<h1 class="uikit-header-heading">' . $site_name . '</h1>';

      $output .= l($link_text, '<front>', array('html' => TRUE, 'attributes' => array('class' => array('uikit-header__logo'))));

      // Do we want to show a site slogan too?
      if (!empty($site_slogan)) {
        $output .= '<span class="uikit-header-subline">' . filter_xss($site_slogan) . '</span>';
      }

      break;

  }

  if (!empty($header_content)) {
    $output .= '<div class="govcms-parkes-header__content">';
    $output .= (is_array($header_content)) ? drupal_render($header_content) : $header_content;
    $output .= '</div>';
  }

  return $output;
}

/**
 * Helper function to add UI KIT link class to link.
 *
 * @param $class_pairs
 *   The pairs of Drupal class and UI KIT class.
 *
 * @param $classes
 *   Origin class array from Drupal.
 *
 * @return array
 *   Class array.
 */
function _govcms_parkes_active_link($class_pairs, $classes) {
  foreach ($class_pairs as $needle => $additional_class) {
    if (in_array($needle, $classes)) {
      $classes[] = $additional_class;
    }
  }
  return $classes;
}

/**
 * Renders all of the grid based Panel layouts.
 *
 * This function is called from the Panels layout's include files.
 *
 * @param array $variables
 *   The $variables array made available in the layout template file.
 *
 * @return string
 *   The panel markup
 */
function _govcms_parkes_render_panel_layout($variables) {
  $attributes = array('class' => 'layout__' . $variables['classes']);
  if (!empty($variables['css_id'])) {
    $attributes['id'] = $variables['css_id'];
  }

  $output  = '';
  $output .= '<div' . drupal_attributes($attributes) . '>';
  $output .= _govcms_parkes_render_panel_layout_build_grid($variables['layout']['grid'], $variables['content']);
  $output .= '</div>';

  return $output;
}

/**
 * Builds Panel layout markup based on Bootstrap classes
 *
 * @see _govcms_parkes_render_panel_layout().
 *
 * @param array $grid
 *
 * @param array $content
 *
 * @return string
 */
function _govcms_parkes_render_panel_layout_build_grid($grid, $content) {
  $output  = '';

  foreach ($grid as $row => $columns) {

    $output .= '  <div class="row">';

    foreach ($columns as $key => $data) {

      // $data is an array of child rows/cols and grid info, $key is a delta
      if (is_array($data)) {
        $output .= '<div class="' . $data['grid'] . '">';
        $output .= _govcms_parkes_render_panel_layout_build_grid($data['children'], $content);
        $output .= '</div>';
      }

      // $data is grid class, $key is the panel machine name
      else {
        $attributes = array(
          'class' => array(
            'layout__region',
            'layout__region--' . $key,
            $data,
          ),
        );

        $output .= '    <div' . drupal_attributes($attributes) . '>';
        $output .= $content[$key];
        $output .= '    </div>';
      }
    }

    $output .= '  </div>';

  }

  return $output;
}

/**
 * Prepares a panels layout plugin array.
 *
 * @param string $human_name
 *   The human readable name of the layout
 *
 * @param string $machine_name
 *   The machine name of the layout
 *
 * @param array $rows_cols
 *   The region definitions in a nested array of rows and columns.
 *
 * @param string $category
 *   The category this layout belongs to, defaults to 'UI Kit'.
 *
 * @return array
 *   The Panels plugin definition
 */
function _govcms_parkes_prepare_panel_layout_array($human_name, $machine_name, $rows_cols, $category = null) {
  if (empty($category)) {
    $category = t('UI Kit');
  }

  $plugin = array(
    'title'     => $human_name,
    'category'  => $category,
    'icon'      => $machine_name . '.png',
    'theme'     => $machine_name,
    'regions'   => array(),
    'bootstrap' => array(),
  );

  $data = _govcms_parkes_prepare_panel_layout_array_extract_layout($rows_cols);

  $plugin = array_merge($plugin, $data);

  return $plugin;
}

/**
 * Extracts the region and grid configuration from a nested Panels layout
 * declaration.
 *
 * @see _govcms_parkes_prepare_panel_layout_array().
 *
 * @param array $rows_cols
 *   An nested array of row and column data
 *
 * @return array
 *   An array with two keys 'regions' and 'grid'.
 */
function _govcms_parkes_prepare_panel_layout_array_extract_layout($rows_cols) {

  $retval = array(
    'regions' => array(),
    'grid'    => array(),
  );

  foreach ($rows_cols as $delta => $row) {

    $retval['grid'][$delta] = array();

    foreach ($row as $key => $data) {

      // If data contains a name key, this is a panel pane
      if (!empty($data['name'])) {
        $retval['regions'][$key] = $data['name'];
      }

      // If data contains a grid key, this is part of the grid
      if (!empty($data['grid'])) {
        $retval['grid'][$delta][$key] = $data['grid'];
      }

      // if data contains children, there is a sub-grid
      if (!empty($data['children'])) {
        $returned = _govcms_parkes_prepare_panel_layout_array_extract_layout($data['children']);
        $retval['grid'][$delta][$key] = array(
          'grid'     => $retval['grid'][$delta][$key],
          'children' => array($returned['grid'][$delta]),
        );
        $retval['regions'] += $returned['regions'];
      }
    }
  }

  return $retval;
}

/**
 * Process a tree of links to provide classes for the main menu to function.
 *
 * @param array $links
 */
function _govcms_parkes_process_global_navigation_links(&$links) {

  foreach ($links as &$link) {

    // Check that this is actually a link
    if (empty($link['#href'])) {
      continue;
    }

    // Replace classes with our versions
    foreach ($link['#attributes']['class'] as &$class) {
      if ($class == 'active-trail') {
        $class = 'govcms-parkes-global-nav-sub-menu__active-item';
      }
    }

    // Remove classes we don't need
    $remove = array('leaf', 'first', 'expanded', 'last', 'active');
    $link['#attributes']['class'] = array_filter($link['#attributes']['class'], function($class) use ($remove) {
      return (!in_array($class, $remove) && !(strpos($class, 'menu-mlid-') !== FALSE));
    });

    // If we have an empty class array, remove it
    if (empty($link['#attributes']['class'])) {
      unset($link['#attributes']['class']);
    }


    // If this is a parent menu link, add sub-menu wrapper div, add parent class
    // and then recursively process the children.
    if (!empty($link['#below'])) {
      $link['#attributes']['class'][] = 'govcms-parkes-global-nav-sub-menu__parent';
      $link['#below']['#prefix'] = '<div class="govcms-parkes-global-nav-sub-menu">';
      $link['#below']['#suffix'] = '</div>';

      // Render this menu using the custom theme function for global navigation
      // link lists
      $link['#below']['#theme_wrappers'] = array('menu_tree__global_navigation');

      _govcms_parkes_process_global_navigation_links($link['#below']);
    }

  }

}
