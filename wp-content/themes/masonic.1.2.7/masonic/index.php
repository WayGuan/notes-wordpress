<?php
/**
 * Theme Index Section for our theme.
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */
?>

<?php get_header(); ?>
<?php do_action('slideshow_deploy', '68'); ?>
<div class="site-content">
   <div id="container" class="wrapper clear">

      <?php if (have_posts()) : ?>
        <!-- Featured Articles -->
        <?php if (is_front_page() && !is_paged()){
          //for the front page, we show 1 post per category, which should be the most recent post.
            //$categories = get_categories();

            $do_not_duplicate = array();

            //these cat ID's correspond to the make it, create it, learn it category ID's.
            $cat_ids = array(13, 15, 16);
            foreach ( $cat_ids as $category ) {
              $num_posts_on_frontpage = 0;
              $args = array(
                'cat' => $category,
                'post_type' => 'post',
                'posts_per_page' => '1',
                'post_not_in' => $do_not_duplicate
              );

              $query = new WP_Query( $args );

              while ($query->have_posts() && $num_posts_on_frontpage < 1) : $query->the_post();
                $do_not_duplicate[] = $post->ID;
                $num_posts_on_frontpage++;
                get_template_part('content', get_post_format());
              endwhile;

            }

            ?>
            </div>
            <div class="wrapper clear">
            <?php
            //show the first page
            while (have_posts()) : the_post();
              //show only if not duplicate
              if(!in_array($post->ID, $do_not_duplicate)){
                get_template_part('content', get_post_format());
              }

            endwhile;

          }else{
        ?>


        <?php } ?>
      <?php else : ?>

         <?php get_template_part('content', 'none'); ?>

      <?php endif; ?>

   </div><!-- #container -->
   <div class="wrapper">
      <?php masonic_paging_nav(); ?>
   </div>




</div><!-- #site-content -->

<?php get_footer(); ?>