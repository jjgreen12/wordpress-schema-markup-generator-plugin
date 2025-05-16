<?php
/**
 * Plugin Name: Schema Stunt Cock
 * Description: Advanced schema markup generator for WordPress
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add menu item
function ssc_add_admin_menu() {
    add_menu_page(
        'Schema Stunt Cock',
        'Schema Stunt Cock',
        'manage_options',
        'schema-stunt-cock',
        'ssc_admin_page',
        'dashicons-chart-area'
    );
}
add_action('admin_menu', 'ssc_add_admin_menu');

// Register scripts and styles
function ssc_enqueue_assets() {
    if (isset($_GET['page']) && $_GET['page'] === 'schema-stunt-cock') {
        $plugin_url = plugin_dir_url(__FILE__);
        
        // Enqueue built assets
        wp_enqueue_style('ssc-styles', $plugin_url . 'dist/assets/index.css');
        wp_enqueue_script('ssc-scripts', $plugin_url . 'dist/assets/index.js', array(), '1.0.0', true);
        
        // Add WordPress data to window object
        wp_localize_script('ssc-scripts', 'sscData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssc_nonce'),
            'pages' => get_pages()
        ));
    }
}
add_action('admin_enqueue_scripts', 'ssc_enqueue_assets');

// Admin page content
function ssc_admin_page() {
    ?>
    <div class="wrap">
        <div id="root"></div>
    </div>
    <?php
}

// Save schema to post meta
function ssc_save_schema() {
    check_ajax_referer('ssc_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $post_id = intval($_POST['post_id']);
    $schema = sanitize_text_field($_POST['schema']);
    
    update_post_meta($post_id, '_ssc_schema', $schema);
    wp_send_json_success();
}
add_action('wp_ajax_ssc_save_schema', 'ssc_save_schema');

// Add schema to page head
function ssc_output_schema() {
    if (is_singular()) {
        $post_id = get_the_ID();
        $schema = get_post_meta($post_id, '_ssc_schema', true);
        
        if ($schema) {
            echo "\n<script type=\"application/ld+json\">\n";
            echo wp_kses_post($schema);
            echo "\n</script>\n";
        }
    }
}
add_action('wp_head', 'ssc_output_schema');