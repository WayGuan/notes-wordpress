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
            <h2 class="text-center">Civic Centre Resource Library</h2>
            <br>
            
        </div>

        <div class="col-xs-12 col-sm-6">
            <a href="/shareit/book-a-service?choice=7" class="full-card-link">
                <div class="card-container expand-on-hover">                                   
                    <h3 class="text-center">3D Printer</h3>        
                    <img class="img-responsive card_image" src="/img/shareit/ccrl_3d_printer_200x200.jpg" alt="" />
                    <p class="card_text">German RepRap X350 Pro printers. All print times must be reserved online.</p>                                        
                                                                                                                            
                </div>
            </a>                    
        </div>
    </div>
        
    <div class="row">
        <div class="col-xs-12">        
            <br>
            <h2 class="text-center">Pierre Berton Resource Library</h2>
            <br>
        </div>
        
        <div class="col-xs-12 col-sm-6">
            <a href="/shareit/book-a-service?choice=14" class="full-card-link">
                <div class="card-container expand-on-hover">                                   
                    <h3 class="text-center">3D Printer</h3>     
                    <img class="img-responsive card_image" src="/img/shareit/pbrl_3d_printer_200x200.jpg" alt="" />
                    <p class="card_text">Use our 3D printers to make real objects likes toys, prototypes, game figures, educational equipment, etc. in one or two different colours.</p>                                                                                        
                                                                                                                            
                </div>
            </a>
        </div>
        
        <div class="col-xs-12 col-sm-6">
            <a href="/shareit/book-a-service?choice=27" class="full-card-link">
                <div class="card-container expand-on-hover">                                                   
                    <h3 class="text-center">Carvey Machine</h3>  
                    <img class="img-responsive card_image" src="/img/shareit/carvey_machine_200x200.jpg" alt="" />
                    <p class="card_text">A 3D/CNC carving desktop machine that allows you to make quality objects out of a variety of materials including wood, plastic and metal.</p>                                                                                                                                                                                                          
                                                                                                                            
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
