<?php
if (!session_id()) {
    session_start(); // Start session if not already started
    error_log(print_r($_SESSION, true), 3, ABSPATH . '/logs/logfile_site.log'); 
}

add_filter('lostpassword_url', function($url) {
    // Add custom logic to correct the URL
    $blog_id = get_current_blog_id();
    $_SESSION['current_blog_id'] = $blog_id;
    error_log(print_r($_SESSION, true), 3, ABSPATH . '/logs/logfile_site.log');   
    error_log(print_r($url."\turl\n", true), 3, ABSPATH . '/logs/logfile_site.log');
    return home_url('/wp-login.php?action=lostpassword&blog_id='.$blog_id);
});

add_action('after_password_reset', function($user) {
    error_log(print_r("Password reset action triggered for user:", true), 3, ABSPATH . '/logs/logfile_site.log');
    $subsite_url = get_site_url($user->blog_id); // URL of the subsite
    error_log(print_r($subsite_url."\tsubsite_url\n", true), 3, ABSPATH . '/logs/logfile_site.log');
    wp_redirect($subsite_url . '/wp-login.php'); // Redirect to the subsite login page
    exit;
});

add_action('retrieve_password', function($user_login) {
    // Get the user object
    $user = get_user_by('login', $user_login);
    // $blog_id = get_current_blog_id();
    $blog_id = $_SESSION['current_blog_id'];
    $_REQUEST['redirect_to'] = 'wp-login.php?checkemail=confirm&blog_id='.$blog_id;
    if ($user) {
        // Check if the user is in a subsite
        $subsite_url = get_site_url($user->ID); // URL of the subsite
        
        // Log for debugging (optional)
        // error_log(print_r($user, true), 3, ABSPATH . '/logs/logfile_site.log');
        error_log(print_r($_SESSION, true).'\tretrieve_password\n', 3, ABSPATH . '/logs/logfile_site.log');
        
        // You can customize the email content or perform actions here
    }
});

add_filter('login_message', function($message) {
    // Check if the 'checkemail' query parameter is present
    if (isset($_GET['checkemail']) && $_GET['checkemail'] === 'confirm' && isset($_GET['blog_id'])) {
        // Get the blog ID from the URL
        $blog_id = intval($_GET['blog_id']);
        // $blog_id = intval($_COOKIE['current_blog_id']);
        
        // Construct the correct login URL for the subsite
        $subsite_login_url = get_site_url($blog_id, 'wp-login.php');
        
        // Modify the message to include a custom login link
        $message .= '<p>Click here to login to your subsite: <a href="' . esc_url($subsite_login_url) . '">Login</a></p>';
    }
    
    return $message;
});

// add_filter('network_site_url', function($url, $path, $scheme){
//     error_log(print_r($url . "\tnetwork_site_url\n", true), 3, ABSPATH . '/logs/logfile_site.log');
//     error_log(print_r($path . "\tpath\n", true), 3, ABSPATH . '/logs/logfile_site.log');
//     error_log(print_r($scheme . "\tscheme\n", true), 3, ABSPATH . '/logs/logfile_site.log');
// }, 10, 3);