<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
   <div class="post-header clear">
      <?php
      if( get_post_format() ) {
         get_template_part( 'inc/post-formats' );
      } elseif( has_post_thumbnail() ) { ?>

            <?php
            //Dont show the featured image at the top because it forces their image to be a certain
            //size or else it will look stretched.
            //the_post_thumbnail('large-thumb');
            ?>

      <?php } ?>

      <div class="entry-info">
         <?php //masonic_posted_on(); ?>
      </div><!-- .entry-info -->
   </div><!-- .entry-header -->

   <div class="entry-content">


      <?php the_content(); ?>
      <?php
      //wp_link_pages(array('before' => '<div class="page-links">' . __('Pages:', 'masonic'), 'after' => '</div>',));
      ?>
   </div><!-- .entry-content -->
</article><!-- #post-## -->

<?php if (get_the_author_meta('description')): ?>
   <article class="blog-post clear">
      <div class="author-box clear">
         <figure><?php echo get_avatar(get_the_author_meta('user_email'), '120'); ?></figure>
         <div class="author-box-wrap">
            <h2><?php the_author_meta('display_name'); ?></h2>

            <p><?php the_author_meta('description'); ?></p>
            <?php masonic_author_links(); ?>
         </div>
      </div>
   </article>
<?php endif; ?>

