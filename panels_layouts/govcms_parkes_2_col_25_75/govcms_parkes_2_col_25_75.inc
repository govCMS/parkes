<?php

/**
 * @file
 * Panels layout with 2 columns the first 25% and the second 75% using UI Kit
 * grid.
 *
 * +-----------+
 * |   |       |
 * |   |       |
 * |   |       |
 * |   |       |
 * |   |       |
 * |   |       |
 * +-----------+
 */

$human_name   = '2 col - 25% 75%';
$machine_name = 'govcms_parkes_2_col_25_75';

$rows_cols = array(
  array(
    'left'  => array('name' => t('Left'),  'grid' => 'col-md-3'),
    'right' => array('name' => t('Right'), 'grid' => 'col-md-9'),
  ),
);

/******************************************************************************/

// All Panels layouts are prepared and rendered by helper functions in
// template.php. We include template.php file manually if the functions are not
// defined as the panel preview can be rendered in the admin theme.
if (!function_exists('_govcms_parkes_render_panel_layout')) {
  include_once(DRUPAL_ROOT . '/' . drupal_get_path('theme', 'govcms_parkes') . '/template.php');
}

$plugin = _govcms_parkes_prepare_panel_layout_array($human_name, $machine_name, $rows_cols);
