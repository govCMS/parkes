<?php
/**
 * Implements hook_form_system_theme_settings_alter() function.
 */
function govcms_parkes_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // Create the form using Forms API
  $form['breadcrumb'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Breadcrumb settings'),
  );
  $form['breadcrumb']['govcms_parkes_breadcrumb'] = array(
    '#type'          => 'select',
    '#title'         => t('Display breadcrumb'),
    '#default_value' => theme_get_setting('govcms_parkes_breadcrumb'),
    '#options'       => array(
                          'yes'   => t('Yes'),
                          'admin' => t('Only in admin section'),
                          'no'    => t('No'),
                        ),
  );
  $form['breadcrumb']['breadcrumb_options'] = array(
    '#type' => 'container',
    '#states' => array(
      'invisible' => array(
        ':input[name="govcms_parkes_breadcrumb"]' => array('value' => 'no'),
      ),
    ),
  );

  $form['support'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Accessibility and support settings'),
  );
  $form['support']['govcms_parkes_meta'] = array(
    '#type'          => 'checkboxes',
    '#title'         => t('Add HTML5 and responsive scripts and meta tags to every page.'),
    '#default_value' => theme_get_setting('govcms_parkes_meta'),
    '#options'       => array(
                          'html5' => t('Add HTML5 shim JavaScript to add support to IE 6-8.'),
                          'meta' => t('Add meta tags to support responsive design on mobile devices.'),
                        ),
    '#description'   => t('IE 6-8 require a JavaScript polyfill solution to add basic support of HTML5. Mobile devices require a few meta tags for responsive designs.'),
  );
  $form['themedev'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Theme development settings'),
  );
  $form['themedev']['govcms_parkes_rebuild_registry'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Rebuild theme registry and output template debugging on every page.'),
    '#default_value' => theme_get_setting('govcms_parkes_rebuild_registry'),
    '#description'   => t('During theme development, it can be very useful to continuously <a href="!link">rebuild the theme registry</a> and to output template debugging HTML comments. WARNING: this is a huge performance penalty and must be turned off on production websites.', array('!link' => 'https://drupal.org/node/173880#theme-registry')),
  );
}
