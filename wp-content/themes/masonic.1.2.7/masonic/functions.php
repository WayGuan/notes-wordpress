<?php
/**
 * Masonic functions and definitions
 *
 * @package ThemeGrill
 * @subpackage Masonic
 * @since 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
   $content_width = 755; /* pixels */
}

//add search box 
/*add_filter('wp_nav_menu_items','add_search_box_to_menu', 10, 2);
function add_search_box_to_menu( $items, $args ) {
    if( $args->theme_location == 'primary' )
        return $items.get_search_form();

    return $items;
}
*/
/*
add_filter('wp_nav_menu_items','add_search_box', 10, 2);
function add_search_box($items, $args) {
 
        ob_start();
        get_search_form();
        $searchform = ob_get_contents();
        ob_end_clean();
 
        $items .= '<li>' . $searchform . '</li>';
 
    return $items;
}*/



function cxc_upload_file_data(){

    $cxc_upload_dir = wp_upload_dir();
    $cxc_success = false;
    $cxc_messages = '';

    if ( ! empty( $cxc_upload_dir['basedir'] ) ) {

        $custom_directory = '/blogs/bookings/';
        $cxc_user_dirname = ABSPATH . 'bookings/'; // Use ABSPATH to get the absolute path
        $cxc_user_baseurl = site_url('bookings/'); // Set the custom base URL



        if ( ! file_exists( $cxc_user_dirname ) ) {
            wp_mkdir_p( $cxc_user_dirname );
        }

        $cxc_filename = wp_unique_filename( $cxc_user_dirname, $_FILES['file']['name'] );
        $cxc_success = move_uploaded_file( $_FILES['file']['tmp_name'], $cxc_user_dirname .''. $cxc_filename);
        $cxc_image_url = $cxc_user_baseurl .''. $cxc_filename;

        if( !empty( $cxc_success ) ){
            $cxc_success = true;
        }else{
            $cxc_success = false;
        }

        $cxc_messages = array( 'success' => $cxc_success, 'cxc_image_url' => $cxc_filename );
        wp_send_json( $cxc_messages );
    }
}
add_action('wp_ajax_cxc_upload_file_data', 'cxc_upload_file_data');
add_action('wp_ajax_nopriv_cxc_upload_file_data', 'cxc_upload_file_data');

function getReservedTimes3DPrinter(){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT date FROM createit_lab_reservations WHERE lab_name = '3d_station_1' AND date > CurDate()");
  
  return $rows; 

}


function getReservedTimes(){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT lab_name, date, time_slot FROM createit_lab_reservations WHERE date > CurDate()");
  
  return $rows; 

}							
function getLabs(){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT * FROM createit_lab_stations WHERE active=1 ORDER BY full_name");
  
  return $rows; 

}

function checkLabs($id){
  global $wpdb;  
  $rows = $wpdb->get_results("SELECT * FROM createit_lab_stations WHERE active=1 AND id = $id");
  
  return $rows; 
}


function getBranchInformation($id){
  global $wpdb;  
  $rows = $wpdb->get_results("SELECT Branches.BranchName FROM Branches WHERE BranchID = $id");
  
  return $rows; 
}



function getReservations($data){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT * FROM createit_lab_reservations where date='$data' ORDER BY lab_name, time_slot");
  
  return $rows; 
}

function checkLabStation($lab_branch, $lab_name){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT createit_lab_stations.id FROM createit_lab_stations where short_name = '$lab_name' and branch_id = '$lab_branch'");
  
  return $rows; 

}

function checkLibraryCard($lab_name, $library_card, $reservation_date){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT createit_lab_reservations.id FROM createit_lab_reservations where lab_name = '$lab_name' and card = '$library_card' and date='$reservation_date'");
  
  return $rows; 

}

function checkName($lab_name, $first_name, $last_name, $reservation_date){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT createit_lab_reservations.id FROM createit_lab_reservations where lab_name = '$lab_name' and first_name = '$first_name' and last_name = '$last_name' and date='$reservation_date'");
  
  return $rows; 

}

/*function checkReservation($lab_name, $reservation_date, $time_slot){
  global $wpdb;
  $rows = $wpdb->get_results("SELECT `date`, time_slot FROM createit_lab_reservations WHERE lab_name = '$lab_name' AND `date` = '$reservation_date' AND time_slot = '$time_slot'");
  
  return $rows; 

}*/

function checkReservation($lab_name, $reservation_date, $time_slot){
  global $wpdb;
  /*$rows = $wpdb->get_results("SELECT `date`, time_slot FROM createit_lab_reservations WHERE lab_name = '$lab_name' AND `date` = '$reservation_date' AND time_slot = '$time_slot'");
*/
  $rows = $wpdb->get_results($wpdb->prepare("SELECT `date`, time_slot FROM createit_lab_reservations WHERE lab_name = %s AND `date` = %s AND time_slot = %s", array($lab_name, $booking_date, $time_slot)));
  
  return $rows; 

}

function checkReservation_new($lab_name, $booking_date, $time_slot){
  global $wpdb;
  $rows = array();

  if(!empty($time_slot))
  {
      if ($time_slot == "1000") {
          $rows = $wpdb->get_results($wpdb->prepare("SELECT `date`, time_slot FROM createit_lab_reservations 
                                               WHERE lab_name = %s 
                                               AND `date` = %s 
                                               AND time_slot IN (%s, %s, %s)",
                                               array($lab_name, $booking_date, $time_slot, "10am", "12pm")));          
      }
      elseif ($time_slot == "1330") {
          $rows = $wpdb->get_results($wpdb->prepare("SELECT `date`, time_slot FROM createit_lab_reservations 
                                               WHERE lab_name = %s 
                                               AND `date` = %s 
                                               AND time_slot IN (%s, %s, %s, %s, %s)",
                                               array($lab_name, $booking_date, $time_slot, "12pm", "2pm", "4pm", "4-5pm")));             
      }
      elseif ($time_slot == "1700") {
          $rows = $wpdb->get_results($wpdb->prepare("SELECT `date`, time_slot FROM createit_lab_reservations 
                                               WHERE lab_name = %s 
                                               AND `date` = %s 
                                               AND time_slot IN (%s, %s)",
                                               array($lab_name, $booking_date, $time_slot, "6pm")));             

      }
      else
      {
          $rows = $wpdb->get_results($wpdb->prepare("SELECT `date`, time_slot FROM createit_lab_reservations WHERE lab_name = %s AND `date` = %s AND time_slot = %s",
                                      array($lab_name, $booking_date, $time_slot)));
      }
  }
  
  return $rows;
}


function insertStatsData($itemID) {
    global $wpdb;
    
    $rows = $wpdb->get_results("INSERT INTO SiteStatistics (ItemID, DateReceived, TimeReceived) VALUES (".$itemID.", CURDATE(), CURTIME())");  
    
    return $rows;             
}


function getPrograms(){
  global $wpdb;
  $rows = $wpdb->get_results(
    "SELECT ATL_Events.ScheduledFor, ATL_Event_Time.StartDate, ATL_Event_Time.StartTime, ATL_Event_Time.EndTime, ATL_Programs.ProgramName, ATL_Programs.ProgramID 
    FROM ATL_Programs 
    JOIN ATL_Events ON ATL_Programs.ProgramID = ATL_Events.ProgramID 
    JOIN ATL_Schedule ON ATL_Events.ScheduledFor = ATL_Events.ScheduledFor 
    JOIN ATL_Program_Category ON ATL_Program_Category.ProgramID = ATL_Programs.ProgramID
    JOIN ATL_Categories ON ATL_Categories.CategoryID = ATL_Program_Category.CategoryID 
    JOIN ATL_Event_Time ON ATL_Events.EventID = ATL_Event_Time.EventID 
    WHERE 
    ATL_Categories.CategoryID = 37 AND 
    ATL_Event_Time.StartDate >= DATE(NOW()) AND 
    ATL_Event_Time.EndDate >= DATE(NOW()) AND 
    ATL_Programs.Status = 1 AND 
    ATL_Events.StatusInBranch = 2 AND 
    ATL_Events.PublicityStatus = 1 
    GROUP BY 
    ATL_Programs.ProgramName
    ORDER BY
    ATL_Events.ScheduledFor ASC,
    ATL_Event_Time.StartDate ASC,
    ATL_Event_Time.StartTime ASC
    LIMIT 5;"
  );

  echo "<ul>";
  foreach ($rows as $obj) :
    $nice_date = new Datetime($obj->StartDate);
    $nice_date = $nice_date->format("M d");
    echo "<li>" . $nice_date . " | <a href='http://www.vaughanpl.info/programs/view/". $obj->ProgramID . "' target='_blank'>" .$obj->ProgramName. "</a></li>";
  endforeach;
  echo "</ul>";
}


function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Share It! Blog';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/site-login-logo.jpg);
            width:100%;
            background-size: contain;
            
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


function my_myme_types($mime_types){
  $mime_types['stl'] = 'application/octet-stream'; //Adding svg extension
  return $mime_types;
}
add_filter('upload_mimes', 'my_myme_types', 1, 1);


/* this action and the following filter are required to account for 
sticky posts on the front page, where we want to maintain 3 posts per page */
add_action( 'pre_get_posts', function ( $q ) 
{

    if ( $q->is_main_query() && $q->is_home() ) {

        $count_stickies = count( get_option( 'sticky_posts' ) );
        $ppp = get_option( 'posts_per_page' );
        $offset = ( $count_stickies <= $ppp ) ? ( $ppp - ( $ppp - $count_stickies ) ) : $ppp;

        if (!$q->is_paged()) {
          $q->set('posts_per_page', ( $ppp - $offset ));
        } else {
          $offset = ( ($q->query_vars['paged']-1) * $ppp ) - $offset;
          $q->set('posts_per_page',$ppp);
          $q->set('offset',$offset);
        }

    }

});    

add_filter( 'found_posts', function ( $found_posts, $q ) 
{

    if( $q->is_main_query() && $q->is_home() ) {

        $count_stickies = count( get_option( 'sticky_posts' ) );
        $ppp = get_option( 'posts_per_page' );
        $offset = ( $count_stickies <= $ppp ) ? ( $ppp - ( $ppp - $count_stickies ) ) : $ppp;        

        $found_posts = $found_posts + $offset;
    }
    return $found_posts;

}, 10, 2 );  

//add our javascript and css files  
function wptuts_scripts_with_jquery()
{

    // Register the script like this for a theme:
    wp_register_script( 'custom-script', get_template_directory_uri() . '/js/jquery.contact-buttons.js', array( 'jquery' ) );
 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'custom-script' );

    // Register the script like this for a theme:
    wp_register_script( 'custom-script-bs', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ) );
 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'custom-script-bs' );

    // Register the script like this for a theme:
    wp_register_script( 'custom-script2', get_template_directory_uri() . '/js/social-media-buttons.js', array( 'jquery' ) );
 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'custom-script2' );
    
    // Register the script like this for a theme:
    wp_register_script( 'custom-script3', get_template_directory_uri() . '/js/zebra_datepicker.js' );
 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'custom-script3' );
}
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_with_jquery' );

function wptuts_styles_with_the_lot()
{

    // Register the style like this for a theme:
    wp_register_style( 'custom-style', get_template_directory_uri() . '/css/contact-buttons.css', array(), '1', 'all' );
    wp_register_style( 'custom-style-bs', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1', 'all' );
    wp_register_style( 'custom-style2', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', array(), '1', 'all' );
    wp_register_style( 'custom-style3', get_template_directory_uri() . '/css/zebra_datepicker.css', array(), '1', 'all' );
    wp_register_style( 'custom-style4', get_template_directory_uri() . '/css/jquery-ui.css', array(), '1', 'all' );
 
    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'custom-style' );
    wp_enqueue_style( 'custom-style-bs' );
    wp_enqueue_style( 'custom-style2' );
    wp_enqueue_style( 'custom-style3' );
    wp_enqueue_style( 'custom-style4' );
}
add_action( 'wp_enqueue_scripts', 'wptuts_styles_with_the_lot' );






//limit # of posts per category
add_filter('pre_get_posts', 'limit_category_posts');
function limit_category_posts($query){
    if ($query->is_category) {
        $query->set('posts_per_page', 4);
    }    
    return $query;
}






if (!function_exists('masonic_setup')) :

   /**
    * Sets up theme defaults and registers support for various WordPress features.
    *
    * Note that this function is hooked into the after_setup_theme hook, which
    * runs before the init hook. The init hook is too late for some features, such
    * as indicating support for post thumbnails.
    */
   function masonic_setup() {

      /*
       * Make theme available for translation.
       * Translations can be filed in the /languages/ directory.
       * If you're building a theme based on masonic, use a find and replace
       * to change 'masonic' to the name of your theme in all the template files
       */
      load_theme_textdomain('masonic', get_template_directory() . '/languages');

      // Add default posts and comments RSS feed links to head.
      add_theme_support('automatic-feed-links');

      /*
       * Let WordPress manage the document title.
       * By adding theme support, we declare that this theme does not use a
       * hard-coded <title> tag in the document head, and expect WordPress to
       * provide it for us.
       */
      add_theme_support('title-tag');

      /*
       * Enable support for Post Thumbnails on posts and pages.
       *
       * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
       */
      add_theme_support('post-thumbnails');
      if ( get_theme_mod( 'masonic_featured_image_cropping', 1 ) == 0 ) {
         add_image_size('small-thumb', 350, 9999, true);
      } else {
         add_image_size('small-thumb', 350, 205, true);
      }
      //add_image_size('large-thumb', 570, 255, true);
      add_image_size('large-thumb', 800, 400, true);

      // This theme uses wp_nav_menu() in one location.
      register_nav_menus(array(
          'primary' => __('Primary Menu', 'masonic'),
      ));

      /*
       * Switch default core markup for search form, comment form, and comments
       * to output valid HTML5.
       */
      add_theme_support('html5', array(
          'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
      ));

      /*
       * Enable support for Post Formats.
       * See http://codex.wordpress.org/Post_Formats
       */
      add_theme_support( 'post-formats', array(
         'video', 'gallery',
      ) );

      // Set up the WordPress core custom background feature.
      add_theme_support('custom-background', apply_filters('masonic_custom_background_args', array(
          'default-color' => 'ffffff',
          'default-image' => '',
      )));
   }

endif; // masonic_setup
add_action('after_setup_theme', 'masonic_setup');

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function masonic_widgets_init() {
   register_sidebar(array(
       'name' => __('Right Sidebar', 'masonic'),
       'id' => 'right-sidebar',
       'description' => '',
       'before_widget' => '<aside id="%1$s" class="blog-post widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<div class="widget-title"><h3>',
       'after_title' => '</h3></div>',
   ));
   register_sidebar(array(
       'name' => __('Footer Sidebar One', 'masonic'),
       'id' => 'footer-sidebar-one',
       'description' => '',
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<div class="widget-title"><h3>',
       'after_title' => '</h3></div>',
   ));
   register_sidebar(array(
       'name' => __('Footer Sidebar Two', 'masonic'),
       'id' => 'footer-sidebar-two',
       'description' => '',
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<div class="widget-title"><h3>',
       'after_title' => '</h3></div>',
   ));
   register_sidebar(array(
       'name' => __('Footer Sidebar Three', 'masonic'),
       'id' => 'footer-sidebar-three',
       'description' => '',
       'before_widget' => '<aside id="%1$s" class="widget %2$s">',
       'after_widget' => '</aside>',
       'before_title' => '<div class="widget-title"><h3>',
       'after_title' => '</h3></div>',
   ));
}

add_action('widgets_init', 'masonic_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function masonic_scripts() {
   wp_enqueue_style('masonic-style', get_stylesheet_uri());

   wp_enqueue_style('masonic-google-fonts', '//fonts.googleapis.com/css?family=Open+Sans:400,300italic,700');

   wp_enqueue_style('masonic-font-awesome', get_template_directory_uri() . '/font-awesome/css/font-awesome.min.css');

   if (is_front_page() || is_archive() || is_home() || is_search()) {
      wp_enqueue_script('masonic-setting', get_template_directory_uri() . '/js/masonry-setting.js', array('jquery-masonry', 'jquery'), '20150106', true);
   }

   wp_enqueue_script('masonic-search-toggle', get_template_directory_uri() . '/js/search-toggle.js', array('jquery'), '20150106', true);

   wp_enqueue_script('masonic-fitvids', get_template_directory_uri() . '/js/fitvids/jquery.fitvids.js', array('jquery'), '20150331', true);

   wp_enqueue_script('masonic-fitvids-setting', get_template_directory_uri() . '/js/fitvids/fitvids-setting.js', array('masonic-fitvids'), '20150331', true);

   wp_enqueue_script('masonic-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true);

   wp_enqueue_script('masonic-bxslider', get_template_directory_uri() . '/js/jquery.bxslider/jquery.bxslider.min.js', array('jquery'), '20130115', true);

   $masonic_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
   if(preg_match('/(?i)msie [1-8]/',$masonic_user_agent)) {
      wp_enqueue_script( 'html5', get_template_directory_uri() . '/js/html5shiv.min.js', true );
   }

   if (is_singular() && comments_open() && get_option('thread_comments')) {
      wp_enqueue_script('comment-reply');
   }
}

add_action('wp_enqueue_scripts', 'masonic_scripts');

/**
 * Removing the default style of wordpress gallery
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Filtering the size to be full from thumbnail to be used in WordPress gallery as a default size
 */
function masonic_gallery_atts( $out, $pairs, $atts ) {
   $atts = shortcode_atts( array(
   'size' => 'small-thumb',
   ), $atts );

   $out['size'] = $atts['size'];

   return $out;
}
function masonic_gallery_output( $output ) {
   add_filter( 'shortcode_atts_gallery', 'masonic_gallery_atts', 10, 3 );
}
add_action('masonic_gallery_images','masonic_gallery_output');

/**
 * Sets the post excerpt length to 40 words.
 *
 * function tied to the excerpt_length filter hook.
 *
 * @uses filter excerpt_length
 */
function masonic_excerpt_length($length) {
   return 40;
}

add_filter('excerpt_length', 'masonic_excerpt_length');

/**
 * Returns a "Continue Reading" link for excerpts
 */
function masonic_read_more($more) {
   return '...';
}

add_filter('excerpt_more', 'masonic_read_more');

/**
 * function to show the footer info, copyright information
 */
if (!function_exists('masonic_footer_copyright')) :

   function masonic_footer_copyright() {
     /* $wp_link = '<a href="' . 'http://wordpress.org' . '" target="_blank" title="' . esc_attr__('WordPress', 'masonic') . '"><span>' . __('WordPress', 'masonic') . '</span></a>';

      $tg_link = '<a href="' . 'http://themegrill.com/themes/masonic' . '" target="_blank" title="' . esc_attr__('ThemeGrill', 'masonic') . '" rel="author"><span>' . __('ThemeGrill', 'masonic') . '</span></a>';

      $default_footer_value = sprintf(__('Powered by %s', 'masonic'), $wp_link) . ' <br> ' . sprintf(__('Theme: %2$s by %1$s', 'masonic'), $tg_link, 'Masonic');
      */

      $default_footer_value =  '<a href="http://www.vaughanpl.info/">VPL Home</a>';
      $default_footer_value .= ' | ';
      $default_footer_value .= '<a href="http://www.vaughanpl.info/contact">Contact VPL</a>';
      $default_footer_value .= ' | ';
      $default_footer_value .= '<a href="http://www.vaughanpl.info/about/website_privacy">Privacy Statement</a>';
      $default_footer_value .= ' | ';
      $default_footer_value .= "&copy Copyright Vaughan Public Libraries 2019";
            

      $masonic_footer_copyright = $default_footer_value;
      echo $masonic_footer_copyright;
   }

endif;
add_action('masonic_footer_copyright', 'masonic_footer_copyright', 10);

// Adding the ID and CLASS attributes to the first <ul> occurence in wp_page_menu for supporting the default menu
function masonic_add_menuclass($ul) {
   return preg_replace('/<ul>/', '<ul id="menu" class="menu wrapper">', $ul);
}

add_filter('wp_page_menu', 'masonic_add_menuclass');

// Adding the support for the entry-title tag for Google Rich Snippets
function masonic_add_mod_hatom_data($content) {
   $title = get_the_title();
   if (is_single()) {
      $content .= '<div class="extra-hatom-entry-title"><span class="entry-title">' . $title . '</span></div>';
   }
   return $content;
}

add_filter('the_content', 'masonic_add_mod_hatom_data');

/*
 * Creating responsive video for posts/pages
 */
if ( !function_exists('masonic_responsive_video') ) :
   function masonic_responsive_video( $html, $url, $attr, $post_ID ) {
       return '<div class="responsive-video">'.$html.'</div>';
   }
   add_filter( 'embed_oembed_html', 'masonic_responsive_video', 10, 4 ) ;
endif;

/*
 * Adding the custom meta box for supporting the post formats
 */
function masonic_post_format_meta_box() {
   add_meta_box( 'post-video-url', __('Video URL', 'masonic'), 'masonic_post_format_video_url', 'post', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'masonic_post_format_meta_box' );

function masonic_post_format_video_url( $post ) {
   $video_post_id = get_post_custom( $post->ID );
   $video_post_url = isset( $video_post_id['video_url'] ) ? esc_attr( $video_post_id['video_url'][0] ) : '';
   wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
   ?>
      <p>
         <input type="text" class="widefat" name="video_url" id="video_url" value="<?php echo $video_post_url; ?>" />
      </p>
   <?php
}

function masonic_post_meta_save( $post_id ) {
   if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

   // checking if the nonce isn't there, or we can't verify it, then we should return
   if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

   // checking if the current user can't edit this post, then we should return
   if( !current_user_can( 'edit_posts' ) ) return;

   // saving the data in meta box
   // saving the video url in the meta box
   if( isset( $_POST['video_url'] ) ) {
      update_post_meta( $post_id, 'video_url', esc_url_raw( $_POST['video_url'] ) );
   }
}

add_action( 'save_post', 'masonic_post_meta_save' );

function masonic_meta_box_display_toggle() {
   $custom_script = '
   <script type="text/javascript">
      jQuery(document).ready(function() {
         // hide the div by default
         jQuery( "#post-video-url" ).hide();

         // if post format is selected, then, display the respective div
         if(jQuery( "#post-format-video" ).is( ":checked" ))
            jQuery( "#post-video-url" ).show();

         // hiding the default post format type input box by default
         jQuery( "input[name=\"post_format\"]" ).change(function() {
            jQuery( "#post-video-url" ).hide();
         });

         // if post format is selected, then, display the respective input div
         jQuery( "input#post-format-video" ).change( function() {
            jQuery( "#post-video-url" ).show();
         });
      });
   </script>
   ';

   return print $custom_script;
}

add_action( 'admin_footer', 'masonic_meta_box_display_toggle' );

/* Adding Framekiller JavaScript */

function add_framekiller_script() {
    echo '<script type="text/javascript">
        if (top !== self) {
            top.location = self.location;
        }
    </script>';
}
add_action('wp_head', 'add_framekiller_script');

/* after forcing strong passwords, hide the weak password confirm option */
function hide_weak_password_confirm_script() {
    echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function(event) { 
                var elements = document.getElementsByClassName("pw-weak");
                console.log(elements);
                var requiredElement = elements[0];
                requiredElement.remove();
            });
        </script>';
}
add_action('admin_head','hide_weak_password_confirm_script');
add_action('login_enqueue_scripts','hide_weak_password_confirm_script');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Post Formats additions.
 */
require get_template_directory() . '/inc/post-formats.php';

/**
 * Assign the Masonic version to a variable.
 */
$theme            = wp_get_theme( 'masonic' );
$masonic_version = $theme['Version'];

/* Calling in the admin area for the Welcome Page */
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/class-masonic-admin.php';
}
