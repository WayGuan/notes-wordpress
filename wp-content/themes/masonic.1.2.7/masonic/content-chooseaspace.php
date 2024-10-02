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
  <div class="entry-content">

    <?php the_content(); ?>

       
    <div class="row">
        <div class="col-xs-12">        
            <br>
            <h2 class="text-center">Civic Centre Resource Library</h2>
            <br>
        </div>
        
        <div class="col-xs-12 col-sm-6">
            <a href="/shareit/book-a-space?choice=6" class="full-card-link">
                <div class="card-container expand-on-hover">                                   
                    <h3 class="text-center">Green Screen</h3>     
                    <img class="img-responsive card_image" src="/img/shareit/green_room_200x200.jpg" alt="" />
                    <p class="card_text">Green space for filming and photography purposes. Fully equipped with lighting, Final Cut Pro, Adobe Creative Cloud Suite, and more!</p>                                                                                        
                    
                </div>
            </a>
        </div>
        
        <div class="col-xs-12 col-sm-6">
            <a href="/shareit/book-a-space?choice=2" class="full-card-link">
                <div class="card-container expand-on-hover">                                                   
                    <h3 class="text-center">Recording Studio</h3>  
                    <img class="img-responsive card_image" src="/img/shareit/recording_studio_200x200.jpg" alt="" />
                    <p class="card_text">Live recording space with sound engineer room. Fully equipped with instruments, Pro Tools, Logic Pro X, Adobe Creative Cloud Suite, and DJ station.</p>                                                                                                                                                                                                          
                                                                                                                            
                </div>
            </a>
        </div>


    </div>

      <?php
      wp_link_pages(array(
          'before' => '<div class="page-links">' . __('Pages:', 'masonic'),
          'after' => '</div>',
      ));
      ?>
   </div><!-- .entry-content -->


</article><!-- #post-## -->
