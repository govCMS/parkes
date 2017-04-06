<?php
function ds_govcms_parkes_card_compact() {
  return array(
    'label' => t('govCMS Parkes: Card (Compact)'),
    'regions' => array(
      'figure'  => t('Figure'),
      'title' => t('Title'),
      'meta' => t('Meta'),
      'main' => t('Content'),
      'footer' => t('Footer'),
    ),
    'image' => TRUE,
  );
}
