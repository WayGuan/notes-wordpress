<?php
/**
 * The template for displaying Archive page.
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */



//we want to go straight to the post if it is just one post in the category
$category = get_queried_object();
//var_dump($category->term_id);

//query via the category
$args = array(
    'cat' => $category->term_id
);
$the_query = new WP_Query( $args );

//check how many posts in the category
if($the_query->found_posts == 1){
  while (have_posts()) : the_post();
    wp_redirect(get_permalink( $post->post_parent ));
    exit;
  endwhile;           
}

?>

<?php get_header(); ?>

<div class="site-content">
  <div class="primary">
   <div id="container" class="wrapper clear">

      <?php if (have_posts()) : ?>
          
         <?php /* Start the Loop */ ?>
         <?php while (have_posts()) : the_post(); ?>

            <?php
            /* Include the Post-Format-specific template for the content.
             * If you want to override this in a child theme, then include a file
             * called content-___.php (where ___ is the Post Format name) and that will be used instead.
             */
            get_template_part('content-new', get_post_format());
            ?>

         <?php endwhile; ?>
         
      <?php else : ?>

         <?php get_template_part('content', 'none'); ?>

      <?php endif; ?>
     </div><!-- #container -->

     <div class="wrapper">
     
      <?php masonic_paging_nav(); ?>
   </div>
   </div><!-- .primary -->

    <?php get_sidebar(); ?>
   
   
</div><!-- .site-content -->

<?php get_footer(); ?>