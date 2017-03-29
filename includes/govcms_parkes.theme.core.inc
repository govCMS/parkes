<?php

/**
 * @file
 * Contains the Drupal core theme functions.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */

/**
 * Implements THEME_breadcrumb().
 */
function govcms_parkes_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];

  if (!empty($breadcrumb)) {
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

    // Process breadcrumb for UI KIT format.
    $breadcrumb_list = '<ul class="uikit-link-list uikit-link-list--inline">';

    foreach ($breadcrumb as $link) {
      $breadcrumb_list .= '<li>' . $link . '</li>';
    }
    $breadcrumb_list .= '</ul>';

    // Add UI KIT tag and style to breadcrumb.
    $output .= '<nav class="uikit-breadcrumbs" aria-label="breadcrumb">' . $breadcrumb_list . '</nav>';
    return $output;
  }
}

/**
 * Implements THEME_menu_local_tasks().
 * 
 * @todo review when building tabs
 */
function govcms_parkes_menu_local_tasks($variables) {
  $output = '';

  // Add UI KIT class to the tabs.
  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] .= '<nav class="govcms-parkes-tabs govcms-parkes-tabs--primary"><ul class="tabs primary">';
    $variables['primary']['#suffix'] = '</ul></nav>';
    $output .= drupal_render($variables['primary']);
  }
  
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $variables['secondary']['#prefix'] .= '<nav class="govcms-parkes-tabs govcms-parkes-tabs--secondary"><ul class="tabs secondary">';
    $variables['secondary']['#suffix'] = '</ul></nav>';
    $output .= drupal_render($variables['secondary']);
  }
  
  // Process the resulting markup to add additional active classes
  $output = preg_replace_callback('/(?:class="[^"]*?\b)(active)\b/i', function($matches) {
    return $matches[0] . ' govcms-parkes-tabs-active';
  }, $output);

  return $output;
}

/**
 * Implements THEME_link().
 */
function govcms_parkes_link($variables) {
  // Check link classes.
  if (isset($variables['options']['attributes']['class'])) {
    $classes = $variables['options']['attributes']['class'];

    // Compose the class array if single string given.
    if (!is_array($classes)) {
      $classes = array($classes);
    }

    // The class pairs we need to add.
    $class_pairs = array(
      'active' => 'is-current',
      'active-trail' => 'is-active',
    );

    // Add additional UI KIT classes.
    $variables['options']['attributes']['class'] = _govcms_parkes_active_link($class_pairs, $classes);
  }

  // Default theme_link() function.
  return '<a href="' . check_plain(url($variables['path'], $variables['options'])) . '"' .
  drupal_attributes($variables['options']['attributes']) . '>' . ($variables['options']['html'] ?
    $variables['text'] : check_plain($variables['text'])) . '</a>';
}

/**
 * Implements THEME_pager().
 * 
 * @todo seems like a waste to implement this just for the bit at the bottom
 */
function govcms_parkes_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('« first')), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('‹ previous')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next = theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('next ›')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('last »')), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => array('pager-first'),
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'),
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => array('pager-ellipsis'),
          'data' => '…',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('pager-current'),
            'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => array('pager-ellipsis'),
          'data' => '…',
        );
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => array('pager-next'),
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => array('pager-last'),
        'data' => $li_last,
      );
    }

    // Swap the core pager class with UI KIT inline-links class.
    return '<h2 class="element-invisible">' . t('Pages') . '</h2>' . theme('item_list', array(
      'items' => $items,
      'attributes' => array('class' => array('inline-links')),
    ));
  }
}

/**
 * Implements THEME_status_messages().
 */
function govcms_parkes_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status'  => t('Status message'),
    'error'   => t('Error message'),
    'warning' => t('Warning message'),
  );

  // Map the UI Kit classes to drupal
  $ui_kit_statuses = array(
    'status'  => 'uikit-page-alerts--success',
    'error'   => 'uikit-page-alerts--error',
    'warning' => 'uikit-page-alerts--warning',
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    // Add UI KIT index-link class to the message div.
    $output .= "<div class=\"uikit-page-alerts $ui_kit_statuses[$type]\">\n";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= reset($messages);
    }
    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Implements THEME_item_list().
 */
function govcms_parkes_item_list(&$variables) {
  // Add uikit-link-list class to TOC link list
  if ($variables['attributes']['class'] == 'toc-filter-links') {
    $variables['attributes']['class'] = array('toc-filter-links', 'uikit-link-list');
  }

  // Pass through to normal theme function
  return theme_item_list($variables);
}


/** Form: Generic *************************************************************/

/**
 * Implements THEME_form_element_label().
 */
function govcms_parkes_form_element_label($variables) {
  $element = $variables['element'];

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
    return '';
  }

  $title = filter_xss_admin($element['#title']);

  // If the element is not required, add (optional) to the end of the label, but
  // not to elements that a are children of another element (like single radios
  // in a radio group) and not for disabled fields.
  $optional_label = '';
  if (empty($element['#required']) && empty($element['#disabled'])) {

    // Field it not required, so we'll start with the normal optional label.
    $optional_label = '(optional)';

    // If this form element has multiple parents, then any label would be
    // applied to the parent element so we don't apply it here.
    if (count($element['#array_parents']) > 1) {
      $optional_label = '';
    }

    // If the label ends with a period, we need to put (optional) before that
    // period or it will look strange.
    if (!empty($optional_label) && substr($title, -1) == '.') {
      $title =  substr($title, 0, -1);
      $optional_label .= '.';
    }

  }

  $attributes = array(
    'class' => array()
  );


  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after') {
    $attributes['class'][] = 'option';
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'][] = 'element-invisible';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // Determine the UI Kit input type classes to use
  // @todo - this should be passed in
  switch ($element['#type']) {
    case 'textfield':
      $attributes['class'][] = 'uikit-text-input__label';
      break;
    case 'checkbox':
    case 'radio':
      $attributes['class'][] = 'uikit-control-input__text';
      break;
  }

  $output =  '';
  $output .= '<label' . drupal_attributes($attributes) . '>';
  $output .= $t('!title !optional', array('!title' => $title, '!optional' => $optional_label));

  // Only print the description if this is not a radio or checkbox.  The
  // description breaks the label on single elements.
  if (!in_array($element['#type'], array('radio', 'checkbox')) && !empty($element['#description'])) {
    $output .= '<span class="uikit-text-input__hint">' . $element['#description'] . '</span>';
  }

  $output .= "</label>\n";

  return $output;
}

/**
 * Implements THEME_form_element().
 *
 * Has been overridden:
 *
 * 1: to remove the description blow the element, it's now rendered as part of
 *    the label.
 * 2: to add classes to the form element wrapper for the purposes of control
 *    input styling (checkboxes and radios).
 */
function govcms_parkes_form_element($variables) {
  $element = &$variables['element'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }

  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }

  // Add classes for single checkbox and radio inputs.
  if (in_array($element['#type'], array('radio', 'checkbox'))) {
    $attributes['class'][] = 'uikit-control-input';
  }

  // Open the element wrapper
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  // If this is a single radio or checkbox input, render any description text
  if (in_array($element['#type'], array('radio', 'checkbox')) && !empty($element['#description'])) {
    $output .= '<span class="uikit-text-input__hint">' . $element['#description'] . '</span>';
  }

  // Close the element wrapper
  $output .= "</div>\n";

  return $output;
}


/** Form: Elements ************************************************************/

/**
 * Implements THEME_textfield
 */
function govcms_parkes_textfield($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'text';
  element_set_attributes($element, array('id', 'name', 'value', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));

  // UI KIT: Add classes
  _govcms_parkes_form_element_add_classes($element, 'uikit-text-input');

  $extra = '';
  if ($element['#autocomplete_path'] && !empty($element['#autocomplete_input'])) {
    drupal_add_library('system', 'drupal.autocomplete');
    $element['#attributes']['class'][] = 'form-autocomplete';

    $attributes = array();
    $attributes['type'] = 'hidden';
    $attributes['id'] = $element['#autocomplete_input']['#id'];
    $attributes['value'] = $element['#autocomplete_input']['#url_value'];
    $attributes['disabled'] = 'disabled';
    $attributes['class'][] = 'autocomplete';
    $extra = '<input' . drupal_attributes($attributes) . ' />';
  }

  $output = '<input' . drupal_attributes($element['#attributes']) . ' />';

  return $output . $extra;
}

/**
 * Implements THEME_textarea
 */
function govcms_parkes_textarea($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id', 'name', 'cols', 'rows'));
  _form_set_class($element, array('form-textarea'));

  // UI KIT: Add classes
  _govcms_parkes_form_element_add_classes($element, 'uikit-text-input', 'textarea');

  $wrapper_attributes = array(
    'class' => array('form-textarea-wrapper'),
  );

  // Add resizable behavior.
  if (!empty($element['#resizable'])) {
    drupal_add_library('system', 'drupal.textarea');
    $wrapper_attributes['class'][] = 'resizable';
  }

  $output = '<div' . drupal_attributes($wrapper_attributes) . '>';
  $output .= '<textarea' . drupal_attributes($element['#attributes']) . '>' . check_plain($element['#value']) . '</textarea>';
  $output .= '</div>';
  return $output;
}

/**
 * Implements THEME_password().
 */
function govcms_parkes_password($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'password';
  element_set_attributes($element, array('id', 'name', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));

  // UI KIT: Add classes
  _govcms_parkes_form_element_add_classes($element, 'uikit-text-input');

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * Implements THEME_checkbox().
 */
function govcms_parkes_checkbox($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'checkbox';
  element_set_attributes($element, array('id', 'name', '#return_value' => 'value'));

  // Unchecked checkbox has #value of integer 0.
  if (!empty($element['#checked'])) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, array('form-checkbox'));

  // UI KIT: Add classes
  _govcms_parkes_form_element_add_classes($element, 'uikit-control-input', '', 'input');

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * Implements THEME_radio().
 */
function govcms_parkes_radio($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'radio';
  element_set_attributes($element, array('id', 'name', '#return_value' => 'value'));

  if (isset($element['#return_value']) && $element['#value'] !== FALSE && $element['#value'] == $element['#return_value']) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, array('form-radio'));

  // UI KIT: Add classes
  _govcms_parkes_form_element_add_classes($element, 'uikit-control-input', '', 'input');

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * Implements THEME_submit().
 */
function govcms_parkes_button($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }

  // UI KIT: Add classes
  _govcms_parkes_form_element_add_classes($element, 'uikit-btn');

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * Implements THEME_fieldset().
 */
function govcms_parkes_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element);

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    // Always wrap fieldset legends in a SPAN for CSS positioning.
    $output .= '<legend><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
  }
  $output .= '<div class="fieldset-wrapper">';
  if (!empty($element['#description'])) {
    $output .= '<div class="fieldset-description hint">' . $element['#description'] . '</div>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= '</div>';
  $output .= "</fieldset>\n";
  return $output;
}


/** Helper functions **********************************************************/

/**
 * Adds base UI Kit class to form elements, also adds invalid variant class.
 *
 * @param array $element
 *   A form element
 *
 * @param string $class
 *   The UI Kit class for the form element
 */
function _govcms_parkes_form_element_add_classes(&$element, $base_class, $variant = '', $dependency = ''  ) {
  // Add the UI Kit field base class
  $element['#attributes']['class'][] = $base_class;

  // Add the variant class if we have one
  if (!empty($variant)) {
    $element['#attributes']['class'][] = $base_class . '--' . $variant;
  }

  // Add the dependency class if we have one
  if (!empty($dependency)) {
    $element['#attributes']['class'][] = $base_class . '__' . $dependency;
  }

  // Add the error variant
  if (array_search('error', $element['#attributes']['class'])) {
    $element['#attributes']['class'][] = $base_class . '--invalid';
  }
}
