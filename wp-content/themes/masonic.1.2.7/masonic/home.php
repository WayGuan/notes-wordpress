
<?php
 /*
 Template Name: home
 */

?>

<?php get_header(); ?>
<?php do_action('slideshow_deploy', '68'); ?>
<div class="site-content" style="padding-bottom: 300px;">
   <div id="container" class="wrapper clear">
		<div id="home-main">
		<div class="entry-content">
       <?php if (have_posts()) : the_post(); ?>

            <?php //get_template_part('content', 'home'); ?>
            <?php the_content(); ?>


         <?php endif; // end of the loop. ?>
		</div>
		</div>

   </div><!-- #container -->
   <!-- <div class="wrapper">
      <?php //masonic_paging_nav(); ?>
   </div> -->




</div><!-- #site-content -->

<?php get_footer(); ?>