<?php
/**
 * @file
 * Returns the HTML for the footer region.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728140
 */
?>
<?php if ($content): ?>
  <nav id="nav" class="<?php print $classes; ?>" >
    <div class="container">
      <div class="row">
        <?php print $content; ?>
      </div>
    </div>
  </nav>
<?php endif; ?>
