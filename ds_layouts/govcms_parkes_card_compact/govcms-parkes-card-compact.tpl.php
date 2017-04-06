<?php
/**
 * @file
 * Compact Card Display Suite layout.
 */

// The layout classes change depending on whether we have a figure or not.
$col_main = 'col-md-12';
if (!empty($figure)) {
  $col_figure = 'col-md-3';
  $col_main   = 'col-md-9';
}

?>

<?php if (!empty($title_suffix['contextual_links'])): ?>
  <?php print render($title_suffix['contextual_links']); ?>
<?php endif; ?>

<article class="govcms-parkes-layout-card-compact row">
  <?php if (!empty($figure)): ?>
  <div class="<?php print $col_figure; ?> govcms-parkes-layout-card-compact__figure">
    <?php print $figure; ?>
  </div>
  <?php endif; ?>

  <div class="<?php print $col_main; ?>">

    <div class="govcms-parkes-layout-card-compact__title"><?php print $title; ?></div>

    <?php if (!empty($meta)): ?>
    <div class="govcms-parkes-layout-card-compact__meta govcms-parkes-body-meta">
      <?php print $meta; ?>
    </div>
    <?php endif; ?>

    <div class="govcms-parkes-layout-card-compact__main">
    <?php print $main; ?>
    </div>

    <?php if (!empty($footer)): ?>
    <footer class="govcms-parkes-layout-card-compact__footer">
      <?php print $footer; ?>
    </footer>
    <?php endif; ?>

  </div>
</article>
