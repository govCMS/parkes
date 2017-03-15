<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */
?>

<header role="banner" class="uikit-header">
  <div class="container">
    <div class="row">
      <?php print render($page['header']); ?>
    </div>
    <div class="row">
      <?php print render($page['navigation']); ?>
    </div>
  </div>
</header>

<?php if ($page['hero']): ?>
<section id="hero" class="container">
<?php print render($page['hero']); ?>
</section>
<?php endif; ?>

<section id="breadcrumbs" class="container">
<?php print $breadcrumb; ?>
</section>

<main role="main" class="container">

  <?php if (!empty($page['content_above'])): ?>
  <div class="row">
    <section id="content-above">
      <?php print render($page['content_above']); ?>
    </section>
  </div>
  <?php endif; ?>
  
  <div class="row">
  
    <?php if (!empty($page['sidebar_left'])): ?>
    <aside class="<?php print $layout_classes['sidebar_left']; ?>" role="complementary">
      <?php print render($page['sidebar_left']); ?>
    </aside>
    <?php endif; ?>

    <article id="content" class="<?php print $layout_classes['content']; ?>">

      <a href="#skip-link" id="skip-content" class="element-invisible">Go to top of page</a>

      <a id="main-content"></a>

      <?php print render($title_prefix); ?>
      <?php if ($title): ?>
      <h1 class="uikit-display-1"><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

      <?php print $messages; ?>
      <?php print render($tabs); ?>
      <?php print render($page['help']); ?>
      <?php if ($action_links): ?>
      <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>

      <?php print render($page['content']); ?>

      <?php print $feed_icons; ?>

    </article>

    <?php if (!empty($page['sidebar_right'])): ?>
    <aside class="<?php print $layout_classes['sidebar_right']; ?>" role="complementary">
      <?php print render($page['sidebar_right']); ?>
    </aside>
    <?php endif; ?>

  </div>

  <?php if (!empty($page['content_below'])): ?>
  <div class="row">
    <section id="content-below">
      <?php print render($page['content_below']); ?>
    </section>
  </div>
  <?php endif; ?>

</main>

<footer role="contentinfo">
  <div class="container">

    <?php if (!empty($page['footer_top'])): ?>
    <div class="row">
      <section id="footer-top">
        <?php print render($page['footer_top']); ?>
      </section>
    </div>
    <?php endif; ?>

    <?php if (!empty($page['footer_bottom'])): ?>
    <div class="row">
      <section id="footer-bottom">
        <?php print render($page['footer_bottom']); ?>
      </section>
    </div>
    <?php endif; ?>

    <?php if (!empty($page['bottom'])): ?>
    <div class="row">
      <section id="bottom">
        <?php print render($page['bottom']); ?>
      </section>
    </div>
    <?php endif; ?>

  </div>
</footer>
