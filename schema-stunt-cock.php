<?php
/**
 * Plugin Name: Schema Stunt Cock
 * Description: Advanced schema markup generator for WordPress
 * Version: 1.0.1
 * Author: Your Name
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SSC_VERSION', '1.0.1');
define('SSC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Add menu item
function ssc_add_admin_menu() {
    add_menu_page(
        'Schema Stunt Cock',
        'Schema Stunt Cock',
        'manage_options',
        'schema-stunt-cock',
        'ssc_admin_page',
        'dashicons-chart-area',
        30
    );
}
add_action('admin_menu', 'ssc_add_admin_menu');

// Register scripts and styles
function ssc_enqueue_assets() {
    if (isset($_GET['page']) && $_GET['page'] === 'schema-stunt-cock') {
        $plugin_url = plugin_dir_url(__FILE__);
        
        // Ensure trailing slash
        $plugin_url = rtrim($plugin_url, '/') . '/';
        
        // Enqueue built assets with versioning to prevent caching issues
        wp_enqueue_style('ssc-styles', $plugin_url . 'dist/assets/index.css', array(), SSC_VERSION);
        wp_enqueue_script('ssc-scripts', $plugin_url . 'dist/assets/index.js', array(), SSC_VERSION, true);
        
        // Add WordPress data to window object
        wp_localize_script('ssc-scripts', 'sscData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssc_nonce'),
            'pages' => ssc_get_pages_data()
        ));
    }
}
add_action('admin_enqueue_scripts', 'ssc_enqueue_assets');

/**
 * Get all pages and posts for the selector
 */
function ssc_get_pages_data() {
    $pages = get_pages();
    $posts = get_posts(array(
        'posts_per_page' => -1,
        'post_type' => 'post'
    ));
    
    // Check if WooCommerce is active
    $has_woocommerce = class_exists('WooCommerce');
    $products = array();
    
    if ($has_woocommerce) {
        $products = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => 'product'
        ));
    }
    
    // Combine all content types
    $all_content = array_merge($pages, $posts, $products);
    
    // Format data
    $formatted_content = array();
    foreach ($all_content as $content) {
        $formatted_content[] = array(
            'ID' => $content->ID,
            'post_title' => $content->post_title,
            'post_type' => $content->post_type
        );
    }
    
    return $formatted_content;
}

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
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access.'));
        exit;
    }
    
    // Get data
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $schema = isset($_POST['schema']) ? $_POST['schema'] : '';
    
    // Validate data
    if ($post_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid post ID.'));
        exit;
    }
    
    if (empty($schema)) {
        wp_send_json_error(array('message' => 'Empty schema data.'));
        exit;
    }
    
    // Make sure the schema is valid JSON
    json_decode($schema);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(array('message' => 'Invalid JSON format.'));
        exit;
    }
    
    // Save the schema
    update_post_meta($post_id, '_ssc_schema', wp_slash($schema));
    
    // Success response
    wp_send_json_success(array('message' => 'Schema saved successfully.'));
    exit;
}
add_action('wp_ajax_ssc_save_schema', 'ssc_save_schema');

// Add schema to page head
function ssc_output_schema() {
    if (is_singular()) {
        $post_id = get_the_ID();
        $schema = get_post_meta($post_id, '_ssc_schema', true);
        
        if ($schema) {
            // Apply filters to allow customization
            $schema = apply_filters('ssc_schema_output', $schema, $post_id);
            
            echo "\n<!-- Schema by Schema Stunt Cock v" . esc_attr(SSC_VERSION) . " -->\n";
            echo "<script type=\"application/ld+json\">\n";
            echo wp_kses_post($schema) . "\n";
            echo "</script>\n";
        }
    }
}
add_action('wp_head', 'ssc_output_schema', 10);

// Get schema data for a specific post
function ssc_get_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access.'));
        exit;
    }
    
    // Get post ID
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    
    if ($post_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid post ID.'));
        exit;
    }
    
    // Get schema
    $schema = get_post_meta($post_id, '_ssc_schema', true);
    
    if (empty($schema)) {
        wp_send_json_error(array('message' => 'No schema found for this post.'));
        exit;
    }
    
    wp_send_json_success(array('schema' => $schema));
    exit;
}
add_action('wp_ajax_ssc_get_schema', 'ssc_get_schema');

// Register activation hook
function ssc_activate() {
    // Create default options
    $default_options = array(
        'auto_generate' => true,
        'include_optional_fields' => true,
        'show_in_preview' => false,
        'content_types' => array('post', 'page'),
        'schema_position' => 'head',
        'schema_format' => 'json-ld'
    );
    
    add_option('ssc_options', $default_options);
    
    // Clear permalinks
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'ssc_activate');

// Register deactivation hook
function ssc_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'ssc_deactivate');

// Add settings link to plugins page
function ssc_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=schema-stunt-cock">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin_basename = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin_basename", 'ssc_add_settings_link');