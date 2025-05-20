<?php
/**
 * Plugin Name: Schema Stunt Cock
 * Description: Advanced schema markup generator for WordPress
 * Version: 1.0.2
 * Author: inaurumsperamus
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SSC_VERSION', '1.0.1');
define('SSC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Add detailed debug function
function ssc_debug_log($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

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
        // Debug paths
        ssc_debug_log('Attempting to load Schema Stunt Cock assets');
        
        // Embedded fallback styles - guaranteed to work
        wp_enqueue_style('ssc-embedded-styles', false);
        wp_add_inline_style('ssc-embedded-styles', ssc_get_fallback_css());
        
        // Attempt to load scripts
        wp_enqueue_script('ssc-embedded-scripts', '', array('jquery'), SSC_VERSION, true);
        wp_add_inline_script('ssc-embedded-scripts', ssc_get_fallback_js(), 'before');
        
        // Add WordPress data to window object
        wp_localize_script('ssc-embedded-scripts', 'sscData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssc_nonce'),
            'pages' => ssc_get_pages_data()
        ));
    }
}
add_action('admin_enqueue_scripts', 'ssc_enqueue_assets');

// Fallback CSS
function ssc_get_fallback_css() {
    return <<<CSS
/* Fallback CSS for Schema Stunt Cock */
.ssc-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.ssc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

.ssc-nav {
    display: flex;
    gap: 10px;
}

.ssc-nav-item {
    padding: 8px 16px;
    background-color: #f5f5f5;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ssc-nav-item.active {
    background-color: #f0f0f1;
    color: #2271b1;
}

.ssc-tab {
    background: white;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.ssc-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.ssc-editor {
    width: 100%;
    min-height: 400px;
    font-family: monospace;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.ssc-button {
    background-color: #2271b1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.ssc-button:hover {
    background-color: #135e96;
}

.ssc-button.secondary {
    background-color: #f0f0f1;
    color: #2271b1;
}

.ssc-button.secondary:hover {
    background-color: #ddd;
}

.ssc-success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.ssc-error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}
CSS;
}

// Fallback JavaScript
function ssc_get_fallback_js() {
    return <<<JS
/* Fallback JavaScript for Schema Stunt Cock */
jQuery(document).ready(function($) {
    // Simple tab system
    $('.ssc-nav-item').on('click', function() {
        const tabId = $(this).data('tab');
        
        // Update active tab
        $('.ssc-nav-item').removeClass('active');
        $(this).addClass('active');
        
        // Show selected tab
        $('.ssc-tab').hide();
        $('#' + tabId).show();
    });
    
    // Initial state
    $('.ssc-nav-item:first').addClass('active');
    $('.ssc-tab:not(:first)').hide();
    
    // Schema validation
    $('#ssc-validate-schema').on('click', function() {
        const schema = $('#ssc-schema-editor').val();
        
        try {
            // Try to parse the JSON
            JSON.parse(schema);
            $('#ssc-validation-result').html('<div class="ssc-success-message">Schema is valid JSON!</div>');
        } catch (e) {
            $('#ssc-validation-result').html('<div class="ssc-error-message">Invalid JSON: ' + e.message + '</div>');
        }
    });
    
    // Save schema
    $('#ssc-save-schema').on('click', function() {
        const schema = $('#ssc-schema-editor').val();
        const selectedPage = $('#ssc-page-selector').val();
        
        if (!schema || !selectedPage) {
            alert('Please enter schema and select a page.');
            return;
        }
        
        try {
            // Validate JSON
            JSON.parse(schema);
            
            // Save via AJAX
            $.ajax({
                url: sscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ssc_save_schema',
                    nonce: sscData.nonce,
                    post_id: selectedPage,
                    schema: schema
                },
                success: function(response) {
                    if (response.success) {
                        $('#ssc-save-result').html('<div class="ssc-success-message">' + response.data.message + '</div>');
                    } else {
                        $('#ssc-save-result').html('<div class="ssc-error-message">' + response.data.message + '</div>');
                    }
                },
                error: function() {
                    $('#ssc-save-result').html('<div class="ssc-error-message">Error communicating with server.</div>');
                }
            });
        } catch (e) {
            $('#ssc-save-result').html('<div class="ssc-error-message">Invalid JSON: ' + e.message + '</div>');
        }
    });
});
JS;
}

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

// Admin page content with simplified fallback UI
function ssc_admin_page() {
    // Get all pages for the dropdown
    $pages = ssc_get_pages_data();
    ?>
    <div class="wrap">
        <div class="ssc-container">
            <div class="ssc-header">
                <h1>Schema Stunt Cock</h1>
                <div class="ssc-nav">
                    <div class="ssc-nav-item" data-tab="ssc-tab-builder">
                        <span class="dashicons dashicons-edit"></span> Builder
                    </div>
                    <div class="ssc-nav-item" data-tab="ssc-tab-validator">
                        <span class="dashicons dashicons-yes-alt"></span> Validator
                    </div>
                    <div class="ssc-nav-item" data-tab="ssc-tab-settings">
                        <span class="dashicons dashicons-admin-settings"></span> Settings
                    </div>
                </div>
            </div>
            
            <!-- Builder Tab -->
            <div id="ssc-tab-builder" class="ssc-tab">
                <div class="ssc-grid">
                    <div>
                        <h2>JSON-LD Schema</h2>
                        <textarea id="ssc-schema-editor" class="ssc-editor">{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Your article title",
  "description": "Article description",
  "image": "https://example.com/image.jpg",
  "datePublished": "",
  "dateModified": "",
  "author": {
    "@type": "Person",
    "name": ""
  }
}</textarea>
                        <div id="ssc-save-result"></div>
                    </div>
                    <div>
                        <h2>Apply Schema To</h2>
                        <select id="ssc-page-selector" style="width: 100%; margin-bottom: 15px;">
                            <option value="">-- Select a page --</option>
                            <?php foreach($pages as $page): ?>
                                <option value="<?php echo esc_attr($page['ID']); ?>">
                                    <?php echo esc_html($page['post_title']); ?> (<?php echo esc_html($page['post_type']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button id="ssc-save-schema" class="ssc-button">Save Schema</button>
                    </div>
                </div>
            </div>
            
            <!-- Validator Tab -->
            <div id="ssc-tab-validator" class="ssc-tab">
                <h2>Schema Validator</h2>
                <p>Paste your JSON-LD schema below to validate:</p>
                <textarea id="ssc-validator-input" class="ssc-editor" style="min-height: 200px;"></textarea>
                <div style="margin-top: 15px; margin-bottom: 15px;">
                    <button id="ssc-validate-schema" class="ssc-button">Validate Schema</button>
                </div>
                <div id="ssc-validation-result"></div>
            </div>
            
            <!-- Settings Tab -->
            <div id="ssc-tab-settings" class="ssc-tab">
                <h2>Schema Settings</h2>
                <form>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">
                            Auto-generate Schema
                        </label>
                        <label style="margin-right: 15px;">
                            <input type="radio" name="auto_generate" value="1" checked> Enabled
                        </label>
                        <label>
                            <input type="radio" name="auto_generate" value="0"> Disabled
                        </label>
                        <p class="description">Automatically generate schema for eligible content types</p>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">
                            Content Types
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                            <label>
                                <input type="checkbox" name="content_types[]" value="post" checked> Posts
                            </label>
                            <label>
                                <input type="checkbox" name="content_types[]" value="page" checked> Pages
                            </label>
                            <label>
                                <input type="checkbox" name="content_types[]" value="product"> Products
                            </label>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="ssc-button">Save Settings</button>
                    </div>
                </form>
            </div>
            
            <!-- Root div for React app that would normally be used -->
            <div id="root" style="display: none;"></div>
        </div>
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