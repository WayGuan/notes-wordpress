<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */
?>

<div class="secondary">
   <?php if (!dynamic_sidebar('right-sidebar')) : ?>
      <aside id="search" class="blog-post widget widget_search">
         <?php get_search_form(); ?>
      </aside>

      <aside id="archives" class="blog-post widget widget_archive">
         <div class="widget-title"><h3><?php _e('Archives', 'masonic'); ?></h3></div>
         <ul>
            <?php wp_get_archives(array('type' => 'monthly')); ?>
         </ul>
      </aside>

      <aside id="meta" class="blog-post widget widget_meta">
         <div class="widget-title"><h3><?php _e('Meta', 'masonic'); ?></h3></div>
         <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php wp_meta(); ?>
         </ul>
      </aside>
   <?php endif; ?>

   <?php //if ( function_exists('wp_tag_cloud') ) : ?>
    <!--<aside id="tag_cloud_widget" class="blog-post widget widget_tag_cloud">
        <div class="widget-title"><h3>Tags</h3></div>
        <div class="tagcloud">
        <?php wp_tag_cloud("smallest=11&largest=11&number=20&orderby=count&order=DESC"); ?>
        </div>
        <br><br>
        <strong><a href='/shareit/tags-page' id="more_tag_id">More Tags...</a></strong>
    </aside> -->
  <?php //endif; ?>
</div>