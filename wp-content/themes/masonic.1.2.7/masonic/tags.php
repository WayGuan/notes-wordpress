<?php /* Template Name: Tag Page */ ?>

<?php get_header(); ?>

<div class="site-content">
   <div id="container" class="wrapper clear">
      <div class="primary">

         <?php while (have_posts()) : the_post(); ?>            
			<article class="blog-post widget widget_tag_cloud">
				<div class="entry-content">
					<div class="tagcloud">
						<?php wp_tag_cloud("smallest=11&largest=11&number=50&orderby=name&order=ASC"); ?>	
					</div>
				</div>
			</article>
         <?php endwhile; // end of the loop. ?>
      </div>
      <?php get_sidebar(); ?>
   </div><!-- #container -->
</div><!-- .site-content -->

<?php get_footer(); ?>