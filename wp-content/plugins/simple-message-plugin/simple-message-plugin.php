<?php
/*
Plugin Name: Simple Message Plugin
Description: A simple plugin with an admin settings page and frontend shortcode display.
Version: 1.0
Author: Your Name
*/

// Hook to add admin menu item
add_action('admin_menu', 'smp_add_admin_menu');
function smp_add_admin_menu() {
    add_options_page(
        'Simple Message Plugin',    // Page title
        'Simple Message',           // Menu title
        'manage_options',           // Capability
        'simple-message-plugin',    // Menu slug
        'smp_settings_page'         // Callback function
    );
}

// Render the settings page
function smp_settings_page() {
    ?>
    <div class="wrap">
        <h1>Simple Message Plugin Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('smp_settings_group'); // Security fields for the registered setting
            do_settings_sections('simple-message-plugin'); // Settings section
            submit_button(); // Save button
            ?>
        </form>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'smp_register_settings');
function smp_register_settings() {
    register_setting(
        'smp_settings_group',    // Option group
        'smp_message'            // Option name
    );

    add_settings_section(
        'smp_settings_section',  // ID
        'Main Settings',         // Title
        null,                    // Callback (optional)
        'simple-message-plugin'  // Page slug
    );

    add_settings_field(
        'smp_message',           // ID
        'Message to Display',    // Label
        'smp_message_callback',  // Callback
        'simple-message-plugin', // Page slug
        'smp_settings_section'   // Section ID
    );
}

// Callback for the message field
function smp_message_callback() {
    $message = esc_attr(get_option('smp_message'));
    echo '<input type="text" name="smp_message" value="' . $message . '" class="regular-text">';
}

// Shortcode to display the message
// function smp_display_message() {
//     $message = esc_html(get_option('smp_message', 'Hello, World!'));
//     return "<p>$message</p>";
// }

// Shortcode to display different message in admin panel than the web page
function smp_display_message() {
    if (is_admin()) {
        // Message for the admin area
        return "<p>This is an admin-specific message from <b>Simple Message</b> plugin.</p>";
    } else {
        // Message for the frontend
        $message = esc_html(get_option('smp_message', 'Hello, World!'));
        return "<p>$message</p>";
    }
}

add_shortcode('simple_message', 'smp_display_message');




/**
 * Method 1: Display Shortcode Output on a Custom Admin Page
 */

// Add an additional admin menu item to display the shortcode output
add_action('admin_menu', 'smp_add_shortcode_admin_page');
function smp_add_shortcode_admin_page() {
    add_submenu_page(
        'options-general.php',           // Parent menu slug
        'Simple Message Shortcode',      // Page title
        'Message Display',               // Menu title
        'manage_options',                // Capability
        'smp-shortcode-display',         // Menu slug
        'smp_shortcode_display_page'     // Callback function
    );
}

// Display the shortcode output on this custom admin page
function smp_shortcode_display_page() {
    echo '<div class="wrap">';
    echo '<h1>Message Display</h1>';
    echo do_shortcode('[simple_message]'); // Display the shortcode output
    echo '</div>';
}

/**
 * Method 2: Display Shortcode Output in an Admin Widget
 */

// Add a dashboard widget
add_action('wp_dashboard_setup', 'smp_add_dashboard_widget');
function smp_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'smp_dashboard_widget',      // Widget slug
        'Simple Message Widget',     // Title
        'smp_dashboard_widget_display' // Display function
    );
}

// Display the shortcode output in the widget
function smp_dashboard_widget_display() {
    echo '<h3>Your Message:</h3>';
    echo do_shortcode('[simple_message]'); // Display the shortcode output
}
