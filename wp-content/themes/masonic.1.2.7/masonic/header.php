<?php
/**
 * Theme Header Section for our theme.
 *
 * Displays all of the <head> section and everything up till </header>
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
   <head>

      <script async src="https://www.googletagmanager.com/gtag/js?id=G-YNYXJVKVTS"></script>
      <script>
         window.dataLayer = window.dataLayer || [];
         function gtag(){dataLayer.push(arguments);}
         gtag('js', new Date());

         gtag('config', 'G-YNYXJVKVTS');
      </script>
      <meta charset="<?php bloginfo('charset'); ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="google-site-verification" content="JgLufh2NkXNztUlAjBZx5Yyzh1nuzN-ZvGw1iaXUiqo" />
      <link rel="profile" href="http://gmpg.org/xfn/11">
      <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
                
      <?php wp_head(); ?>

      <!-- google analysis -->
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', '354917260', 'auto');
        ga('require', 'linkid');
        ga('send', 'pageview');

      </script>      
   </head>

   <body <?php body_class(); ?>>

      <!-- Google Tag Manager - must be placed here as per Google Tags-->
      <noscript><iframe src="//www.googletagmanager.com/ns.html?id=G-YNYXJVKVTS"
      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
      <!-- End Google Tag Manager -->
         
      <div id="page" class="hfeed site">
         <a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'masonic'); ?></a>

         <header id="masthead" class="site-header clear">

            
            <div class="header-image">
               <?php if (get_header_image()) : ?>
                  <figure><a href="/blogs/shareit"><img class="header_img_full_width" src="<?php header_image(); ?>" width="<?php echo esc_attr(get_custom_header()->width); ?>" height="200px" alt=""></a>
                  </figure>
               <?php endif; // End header image check. ?>
            </div> <!-- .header-image -->

            
            <div class="menu-wrap">
            <nav class="navigation clear">
               <input type="checkbox" id="masonic-toggle" name="masonic-toggle"/>
               <label for="masonic-toggle" id="masonic-toggle-label" class="fa fa-navicon fa-2x"></label>
               <div class="wrapper clear" id="masonic">
                  <?php
                  if (has_nav_menu('primary')) {
                     wp_nav_menu(array(
                         'theme_location' => 'primary',
                         'items_wrap' => '<ul id="%1$s" class="%2$s wrapper clear">%3$s</ul>',
                         'container' => ''
                     ));                     
                  } else {
                     wp_page_menu(array(
                         'show_home' => true,
                         'menu_class' => ''
                     ));
                  }
                  ?>
                  <!--
                      <div id="dropdown-content" class="dropdown-content">
                        <a href="#">Link 1</a>
                        <a href="#">Link 2</a>
                        <a href="#">Link 3</a>
                      </div>                  
                        -->


                                <?php if ( get_theme_mod( 'masonic_search_icon_display', 1 ) != 0 ) { ?>
                    <div id="sb-search" class="sb-search">
                      <span class="sb-icon-search"><i class="fa fa-search"></i></span>
                    </div>
                <?php } ?>
              </div>
              <?php if ( get_theme_mod( 'masonic_search_icon_display', 1 ) != 0 ) { ?>
                <div id="sb-search-res" class="sb-search-res">
                    <span class="sb-icon-search"><i class="fa fa-search"></i></span>
                </div>
              <?php } ?> 
              </div>

    

              
            </nav>
            </div>            
            <!-- #site-navigation -->

            <div class="menu_div"></div>
            <div class="inner-wrap masonic-search-toggle">
               <?php get_search_form(); ?>
            </div>
            
            <div class="site-branding clear">
               <div class="wrapper site-header-text clear">
                  <?php if (get_theme_mod('masonic_logo')) : ?>
                     <div class="logo-img-holder " >
                        <a  href='<?php echo esc_url(home_url('/')); ?>' title='<?php echo esc_attr(get_bloginfo('name', 'masonic')); ?>' rel='home'><img src='<?php echo esc_url(get_theme_mod('masonic_logo')); ?>' alt='<?php echo esc_attr(get_bloginfo('name', 'masonic')); ?>'></a>
                     </div>
                  <?php endif; ?>
                  <div class="main-header">
                  <?php if ( is_front_page() || is_home() ) : ?>
                     <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                  <?php else : ?>
                     <h3 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h3>
                  <?php endif; ?>
                  <?php
                  $description = get_bloginfo( 'description', 'display' );
                  if ( $description || is_customize_preview() ) : ?>
                     <p class="site-description"><?php echo $description; ?></p>
                  <?php endif;?>
                  </div>
               </div>
            </div><!-- .site-branding -->

            

            <!-- this is the title for the pages -->            
            <?php if (!is_front_page()) { ?>
               <div class="blog-header clear">
                  <article class="wrapper">
                     <div class="blog-site-title">
                        <?php
                        if ('' != masonic_header_title()) {
                           ?>
                           <?php if ( is_home() ) : ?>
                              <h2><?php echo masonic_header_title(); ?></h2>
                           <?php elseif(is_category("make-it")) : ?>
                              <img src="http://www.vaughanpl.info/blogs/shareit/wp-content/uploads/make-it-logo.png" alt="make-it logo" class="category-header-image">
                           <?php elseif(is_category("learn-it")) : ?>
                              <img src="http://www.vaughanpl.info/blogs/shareit/wp-content/uploads/learn-it-logo.png" alt="learn-it logo" class="category-header-image">
                           <?php elseif(is_category("create-it")) : ?>
                              <img src="http://www.vaughanpl.info/blogs/shareit/wp-content/uploads/create-it-logo.png" alt="create-it logo" class="category-header-image">
                           <?php else : ?>
                              <h1><?php echo masonic_header_title() == "Bookings" ? "Request a Booking for a Space or Service" : masonic_header_title(); ?></h1>
                           <?php endif; ?>
                        <?php } ?>
                     </div>

                     <?php if (function_exists('bcn_display')) { ?>
                        <div class="breadcrums" xmlns:v="http://rdf.data-vocabulary.org/#">
                           <?php bcn_display(); ?>
                        </div>
                     <?php } ?>

                  </article>
               </div>
            <?php } ?>


<div style="right: 0px;" id="contact-buttons-bar">
  <button class="contact-button-link show-hide-contact-bar" onclick="abc()">
    <span id="contact-bar-span-id" class="fa fa-angle-right"></span>
  </button>
  <a href="https://www.facebook.com/vaughanpl" class="contact-button-link cb-ancor facebook" title="Like on Facebook">
    <span class="fa fa-facebook"></span>
  </a>
  <a href="https://twitter.com/vaughanpl" class="contact-button-link cb-ancor twitter" title="Follow on Twitter">
    <span class="fa fa-twitter"></span>
  </a>
  <a href="http://www.youtube.com/user/VaughanPL" class="contact-button-link cb-ancor youtube" title="Catch us on Youtube">
    <span class="fa fa-youtube"></span>
  </a>
  <a href="https://www.pinterest.com/vaughanpl/" class="contact-button-link cb-ancor pinterest" title="Check us out on Pinterest">
    <span class="fa fa-pinterest"></span>
  </a>
  <a href="https://www.instagram.com/vaughanpubliclibraries/" class="contact-button-link cb-ancor instagram" title="Join us on Instagram">
    <span class="fa fa-instagram"></span>
  </a>
  <a href="https://www.vaughanpl.info/news_and_events/blogs" class="contact-button-link cb-ancor blog" title="VPL Blog">
    <img src="/img/bloglogo_shareit.png" class="img-responsive">
  </a>
  <a href="https://www.eventbrite.ca/o/vaughan-public-libraries-13595422412" class="contact-button-link cb-ancor eventbrite">
    <img src="/img/eventbrite_logo_shareit.png" alt="Register on Eventbrite" class="img-responsive">
  </a>  
</div>

         </header><!-- #masthead -->