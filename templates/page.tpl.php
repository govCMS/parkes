<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */
?>

<?php
  // Variables
  $sidebar_first  = render($page['sidebar_first']);
  $sidebar_second = render($page['sidebar_second']);
?>

<div class="uikit-body uikit-grid">
  <header class="uikit-header" role="banner">
    <div class="container">
      <div class="row">
        <div class="col-md-1">
          <?php if ($logo): ?>
            <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="header__logo">
              <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="header__logo-image uikit-responsive-media-img" />
            </a>
          <?php endif; ?>
          <?php if ($site_name || $site_slogan): ?>
            <div class="element-invisible header__header-subline">
              <?php if ($site_name): ?>
                <h1><?php print $site_name; ?></h1>
              <?php endif; ?>

              <?php if ($site_slogan): ?>
                <div><?php print $site_slogan; ?></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="col-md-7">
        </div>
        <div class="col-md-4">
          <?php print render($page['header']); ?>
        </div>
      </div>
    </div>
  </header>

  <?php print render($page['navigation']); ?>

  <?php if(!drupal_is_front_page()): ?>
    <!-- Show the title and breadcrumbs when they are not on the homepage -->
    <div class="page-title uikit-header uikit-header--light">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <?php print $breadcrumb; ?>
            <?php print render($title_prefix); ?>
              <?php if ($title): ?>
                <h1 class="uikit-header-heading"><?php print $title; ?></h1>
              <?php endif; ?>
            <?php print render($title_suffix); ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php print render($page['highlighted']); ?>

  <div class="page-layout container">
    <div class="row">
      <?php
        // Render the correct content classes.
        $content_class = 'col-md-12 content-fullwidth';
        if ($sidebar_first && $sidebar_second):
          $content_class = 'col-md-6 content-middle';
        elseif ($sidebar_second):
          $content_class = 'col-md-9 content-left';
        elseif ($sidebar_first):
          $content_class = 'col-md-9 content-right';
        endif;
      ?>

      <?php if ($sidebar_first): ?>
        <aside class="col-md-3 sidebar-left sidebar" role="complementary">
          <?php print $sidebar_first; ?>
        </aside>
      <?php endif; ?>

      <main class="<?php print $content_class; ?>" id="content" role="main">
        <a href="#skip-link" class="visually-hidden visually-hidden--focusable" id="main-content">Back to top</a>
        <?php print $messages; ?>
        <?php print render($tabs); ?>
        <?php print render($page['help']); ?>
        <?php if ($action_links): ?>
          <ul class="action-links"><?php print render($action_links); ?></ul>
        <?php endif; ?>
        <?php print render($page['content_top']); ?>
        <?php print render($page['content']); ?>
        <?php print $feed_icons; ?>
      </main>

      <?php if ($sidebar_second): ?>
        <aside class="col-md-3 sidebar-right sidebar" role="complementary">
          <?php print $sidebar_second; ?>
        </aside>
      <?php endif; ?>
    </div>
  </div>

  <footer class="uikit-footer <?php print $classes; ?>" role="contentinfo">
    <div class="container">

      <div class="uikit-footer__back-to-top row">
        <div class="col-md-12">
          <a href="#skip-link" class="uikit-direction-link uikit-direction-link--up" id="main-menu" tabindex="0">Back to top</a>
        </div>
      </div>
      <div class="uikit-footer__navigation row">
        <?php print render($page['footer_top']); ?>
      </div>
      <div class="uikit-footer__end row">
        <?php print render($page['footer_bottom']); ?>
      </div>
      <div class="row">
        <div class="uikit-footer__logo">
				  <img class="uikit-responsive-media-img" src="/<?php print path_to_theme(); ?>/images/coat-of-arms.png" alt="Commonwealth Coat of Arms crest logo">
        </div>
      </div>
      <div class="row">
        <p>
          <small>© Commonwealth of Australia, <a href="https://raw.githubusercontent.com/govau/uikit/master/LICENSE" rel="external license">MIT licensed</a></small>
        </p>
      </div>
    </div>
  </footer>
</div>
