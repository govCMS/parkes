<?php
/**
 * @file
 * Theme settings.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function govcms_parkes_form_system_theme_settings_alter(&$form, &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // When using SVG logos, we need the user to set the maximum width for the
  // logo.  In the header so that it's not made too small by the other header
  // elements. If the container is smaller than this, the logo will scale (on
  // mobile for example).
  $form['logo']['logo_max_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum width'),
    '#default_value' => theme_get_setting('logo_max_width'),
    '#field_suffix' => 'px',
    '#size' => 5,
    '#description' => t('The maximum width of the logo in the header, aspect ratio will be maintained.'),
  );

  // Header options
  $form['header'] = array(
    '#type' => 'fieldset',
    '#title' => t('Header options'),
  );

  $form['header']['govcms_parkes_site_branding'] = array(
    '#type' => 'radios',
    '#title' => t('Site branding'),
    '#options' => array(
      'logo' => t('Site logo'),
      'name' => t('Site name and slogan'),
    ),
    '#default_value' => theme_get_setting('govcms_parkes_site_branding'),
    '#description' => t('This theme supports either a logo in the header or the site name and slogan (if provided).'),
    '#required' => TRUE,
  );
}
