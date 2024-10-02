<?php
 /*
 Template Name: bookings
 */

?>

<?php get_header(); ?>


<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">
          
         <?php  get_template_part('content', 'bookings'); ?>

     </div>
      <?php get_sidebar(); ?>
   </div><!-- #container -->
</div><!-- .site-content -->

<?php get_footer(); ?>



<!-- <?php //while (have_posts()) : the_post(); ?>

             

             

<?php //endwhile; // end of the loop. ?> -->