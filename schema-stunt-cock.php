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
        // Enqueue admin styles and scripts
        wp_enqueue_style('ssc-admin-styles', SSC_PLUGIN_URL . 'css/schema-admin.css', array(), SSC_VERSION);
        wp_enqueue_script('ssc-admin-scripts', SSC_PLUGIN_URL . 'js/schema-admin.js', array('jquery'), SSC_VERSION, true);
        
        // Add WordPress data to window object
        wp_localize_script('ssc-admin-scripts', 'sscData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssc_nonce'),
            'pages' => ssc_get_pages_data(),
            'schemaTemplates' => ssc_get_schema_templates()
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

/**
 * Get schema templates for various schema types
 */
function ssc_get_schema_templates() {
    return array(
        'Article' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => 'Your article title',
            'description' => 'Article description',
            'image' => 'https://example.com/image.jpg',
            'datePublished' => '',
            'dateModified' => '',
            'author' => array(
                '@type' => 'Person',
                'name' => ''
            )
        ), JSON_PRETTY_PRINT),
        
        'BlogPosting' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => 'Your blog post title',
            'description' => 'Blog post description',
            'image' => 'https://example.com/image.jpg',
            'datePublished' => '',
            'dateModified' => '',
            'author' => array(
                '@type' => 'Person',
                'name' => ''
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => '',
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => ''
                )
            )
        ), JSON_PRETTY_PRINT),
        
        'Product' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => 'Product name',
            'description' => 'Product description',
            'image' => 'https://example.com/product.jpg',
            'brand' => array(
                '@type' => 'Brand',
                'name' => ''
            ),
            'offers' => array(
                '@type' => 'Offer',
                'price' => '',
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
                'url' => ''
            ),
            'aggregateRating' => array(
                '@type' => 'AggregateRating',
                'ratingValue' => '',
                'reviewCount' => ''
            )
        ), JSON_PRETTY_PRINT),
        
        'LocalBusiness' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'Business name',
            'description' => 'Business description',
            'image' => '',
            'telephone' => '',
            'email' => '',
            'url' => '',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '',
                'addressLocality' => '',
                      'addressRegion' => '',
                'postalCode' => '',
                'addressCountry' => ''
            ),
            'geo' => array(
                '@type' => 'GeoCoordinates',
                'latitude' => '',
                'longitude' => ''
            ),
            'openingHoursSpecification' => array(
                array(
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
                    'opens' => '9:00',
                    'closes' => '17:00'
                )
            )
        ), JSON_PRETTY_PRINT),
        
        'Organization' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Organization name',
            'url' => '',
            'logo' => '',
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'telephone' => '',
                'contactType' => 'customer service'
            ),
            'sameAs' => array(
                'https://www.facebook.com/your-profile',
                'https://www.twitter.com/your-profile'
            )
        ), JSON_PRETTY_PRINT),
        
        'Person' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Person name',
            'jobTitle' => '',
            'url' => '',
            'image' => '',
            'sameAs' => array(),
            'address' => array(
                '@type' => 'PostalAddress',
                'addressLocality' => '',
                'addressRegion' => ''
            )
        ), JSON_PRETTY_PRINT),
        
        'WebPage' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Page title',
            'description' => 'Page description',
            'url' => '',
            'image' => '',
            'datePublished' => '',
            'dateModified' => '',
            'lastReviewed' => '',
            'breadcrumb' => array(
                '@type' => 'BreadcrumbList',
                'itemListElement' => array(
                    array(
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => 'https://example.com'
                    ),
                    array(
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Category',
                        'item' => 'https://example.com/category'
                    )
                )
            )
        ), JSON_PRETTY_PRINT),
        
        'FAQPage' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array(
                array(
                    '@type' => 'Question',
                    'name' => 'Question 1',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Answer to question 1'
                    )
                ),
                array(
                    '@type' => 'Question',
                    'name' => 'Question 2',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Answer to question 2'
                    )
                )
            )
        ), JSON_PRETTY_PRINT),
        
        'Event' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => 'Event name',
            'description' => 'Event description',
            'startDate' => '',
            'endDate' => '',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => array(
                '@type' => 'Place',
                'name' => 'Location name',
                'address' => array(
                    '@type' => 'PostalAddress',
                    'streetAddress' => '',
                    'addressLocality' => '',
                    'addressRegion' => '',
                    'postalCode' => '',
                    'addressCountry' => ''
                )
            ),
            'performer' => array(
                '@type' => 'Person',
                'name' => ''
            ),
            'organizer' => array(
                '@type' => 'Organization',
                'name' => '',
                'url' => ''
            ),
            'offers' => array(
                '@type' => 'Offer',
                'price' => '',
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
                'validFrom' => ''
            )
        ), JSON_PRETTY_PRINT),
        
        'Review' => json_encode(array(
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'name' => 'Review title',
            'reviewBody' => 'Review content',
            'author' => array(
                '@type' => 'Person',
                'name' => ''
            ),
            'datePublished' => '',
            'reviewRating' => array(
                '@type' => 'Rating',
                'ratingValue' => '5',
                'bestRating' => '5'
            ),
            'itemReviewed' => array(
                '@type' => 'Product',
                'name' => ''
            )
        ), JSON_PRETTY_PRINT),
    );
}

// Admin page content
function ssc_admin_page() {
    // Get all pages for the dropdown
    $pages = ssc_get_pages_data();
    ?>
    <div class="wrap">
        <div class="ssc-container">
            <div class="ssc-header">
                <h1>Schema Stunt Cock</h1>
                <div class="ssc-nav">
                    <a href="#" class="ssc-nav-item active" data-tab="builder">
                        <span class="dashicons dashicons-edit"></span> Builder
                    </a>
                    <a href="#" class="ssc-nav-item" data-tab="validator">
                        <span class="dashicons dashicons-yes-alt"></span> Validator
                    </a>
                    <a href="#" class="ssc-nav-item" data-tab="settings">
                        <span class="dashicons dashicons-admin-settings"></span> Settings
                    </a>
                </div>
            </div>
            
            <!-- Builder Tab -->
            <div id="ssc-tab-builder" class="ssc-tab">
                <!-- Schema List View -->
                <div id="ssc-schema-list-view">
                    <div class="ssc-tab-header">
                        <h2>Your Schemas</h2>
                        <div>
                            <select id="ssc-new-schema-type">
                                <option value="">-- Select Schema Type --</option>
                                <option value="Article">Article</option>
                                <option value="BlogPosting">Blog Post</option>
                                <option value="Product">Product</option>
                                <option value="LocalBusiness">Local Business</option>
                                <option value="Organization">Organization</option>
                                <option value="Person">Person</option>
                                <option value="WebPage">Web Page</option>
                                <option value="FAQPage">FAQ Page</option>
                                <option value="Event">Event</option>
                                <option value="Review">Review</option>
                            </select>
                            <button id="ssc-create-schema" class="ssc-button">Create New Schema</button>
                        </div>
                    </div>
                    <div id="ssc-schema-list" class="ssc-schema-list">
                        <!-- Schema items will be rendered by JavaScript -->
                        <div class="ssc-empty-state">Loading schemas...</div>
                    </div>
                </div>
                
                <!-- Schema Editor View -->
                <div id="ssc-schema-editor-view" style="display: none;">
                    <div class="ssc-tab-header">
                        <h2>Edit Schema</h2>
                        <button id="ssc-back-to-list" class="ssc-button secondary">← Back to List</button>
                    </div>
                    
                    <div class="ssc-grid">
                        <div>
                            <div class="ssc-form-group">
                                <label for="ssc-schema-name">Schema Name</label>
                                <input type="text" id="ssc-schema-name" class="regular-text" placeholder="Enter a name for this schema">
                            </div>
                            
                            <div class="ssc-form-group">
                                <label for="ssc-schema-editor">JSON-LD Schema</label>
                                <textarea id="ssc-schema-editor" class="ssc-editor"></textarea>
                            </div>
                            
                            <div id="ssc-save-result"></div>
                            
                            <div class="ssc-button-row">
                                <button id="ssc-update-schema" class="ssc-button">Update Schema</button>
                                <button id="ssc-apply-schema" class="ssc-button">Apply to Pages</button>
                            </div>
                        </div>
                        
                        <div>
                            <div class="ssc-form-group">
                                <label for="ssc-page-selector">Add Page</label>
                                <select id="ssc-page-selector" class="regular-text">
                                    <option value="">-- Select a page --</option>
                                    <?php foreach($pages as $page): ?>
                                        <option value="<?php echo esc_attr($page['ID']); ?>">
                                            <?php echo esc_html($page['post_title']); ?> (<?php echo esc_html($page['post_type']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button id="ssc-add-page" class="ssc-button secondary">Add Page</button>
                            </div>
                            
                            <div class="ssc-form-group">
                                <label>Assigned Pages</label>
                                <div id="ssc-page-assignments" class="ssc-page-assignments">
                                    <!-- Page assignments will be rendered by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Validator Tab -->
            <div id="ssc-tab-validator" class="ssc-tab" style="display: none;">
                <h2>Schema Validator</h2>
                <p>Paste your JSON-LD schema below to validate:</p>
                <textarea id="ssc-validator-input" class="ssc-editor" style="min-height: 200px;"></textarea>
                <div style="margin-top: 15px; margin-bottom: 15px;">
                    <button id="ssc-validate-schema-button" class="ssc-button">Validate Schema</button>
                </div>
                <div id="ssc-validation-result"></div>
            </div>
            
            <!-- Settings Tab -->
            <div id="ssc-tab-settings" class="ssc-tab" style="display: none;">
                <h2>Schema Settings</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3>General Settings</h3>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <div>
                                <strong>Auto-generate Schema</strong>
                                <p style="margin-top: 5px; color: #666;">Automatically generate schema for eligible content types</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="auto_generate" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3>Content Types</h3>
                        <p>Enable schema generation for these content types:</p>
                        
                        <div style="margin-top: 15px;">
                            <label>
                                <input type="checkbox" name="content_types[]" value="post" checked>
                                Posts
                            </label>
                        </div>
                        
                        <div style="margin-top: 10px;">
                            <label>
                                <input type="checkbox" name="content_types[]" value="page" checked>
                                Pages
                            </label>
                        </div>
                        
                        <?php if (class_exists('WooCommerce')): ?>
                        <div style="margin-top: 10px;">
                            <label>
                                <input type="checkbox" name="content_types[]" value="product">
                                Products
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <button id="ssc-save-settings" class="ssc-button">Save Settings</button>
                    <div id="ssc-settings-result"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Database setup
 */
function ssc_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create schemas table
    $table_name = $wpdb->prefix . 'ssc_schemas';
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        type varchar(50) NOT NULL,
        json longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Create schema-page relationships table
    $table_relationships = $wpdb->prefix . 'ssc_schema_pages';
    $sql_relationships = "CREATE TABLE $table_relationships (
        id int(11) NOT NULL AUTO_INCREMENT,
        schema_id int(11) NOT NULL,
        page_id int(11) NOT NULL,
        PRIMARY KEY  (id),
        KEY schema_id (schema_id),
        KEY page_id (page_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($sql_relationships);
}
register_activation_hook(__FILE__, 'ssc_create_tables');

/**
 * AJAX: Get all schemas
 */
function ssc_get_all_schemas() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    global $wpdb;
    $schemas_table = $wpdb->prefix . 'ssc_schemas';
    $relationships_table = $wpdb->prefix . 'ssc_schema_pages';
    
    // Get all schemas
    $schemas = $wpdb->get_results("SELECT * FROM $schemas_table ORDER BY updated_at DESC", ARRAY_A);
    
    // Get page assignments for each schema
    foreach ($schemas as &$schema) {
        $page_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT page_id FROM $relationships_table WHERE schema_id = %d",
            $schema['id']
        ));
        
        $schema['pages'] = $page_ids;
        $schema['last_updated'] = date('F j, Y, g:i a', strtotime($schema['updated_at']));
    }
    
    wp_send_json_success(array('schemas' => $schemas));
    exit;
}
add_action('wp_ajax_ssc_get_all_schemas', 'ssc_get_all_schemas');

/**
 * AJAX: Create schema
 */
function ssc_create_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : 'Unnamed Schema';
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $json = isset($_POST['json']) ? $_POST['json'] : '{}'; // Don't sanitize JSON
    
    // Validate JSON
    json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(array('message' => 'Invalid JSON format'));
        exit;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'ssc_schemas';
    
    // Insert schema
    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'type' => $type,
            'json' => $json,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => 'Failed to create schema'));
        exit;
    }
    
    // Get the new schema
    $schema_id = $wpdb->insert_id;
    $schema = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $schema_id
    ), ARRAY_A);
    
    $schema['pages'] = array();
    $schema['last_updated'] = date('F j, Y, g:i a', strtotime($schema['updated_at']));
    
    wp_send_json_success(array('schema' => $schema));
    exit;
}
add_action('wp_ajax_ssc_create_schema', 'ssc_create_schema');

/**
 * AJAX: Update schema
 */
function ssc_update_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $schema_id = isset($_POST['schema_id']) ? intval($_POST['schema_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $json = isset($_POST['json']) ? $_POST['json'] : ''; // Don't sanitize JSON
    
    if ($schema_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid schema ID'));
        exit;
    }
    
    // Validate JSON
    json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(array('message' => 'Invalid JSON format'));
        exit;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'ssc_schemas';
    
    // Update schema
    $result = $wpdb->update(
        $table_name,
        array(
            'name' => $name,
            'json' => $json,
            'updated_at' => current_time('mysql')
        ),
        array('id' => $schema_id),
        array('%s', '%s', '%s'),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => 'Failed to update schema'));
        exit;
    }
    
    wp_send_json_success(array('last_updated' => date('F j, Y, g:i a')));
    exit;
}
add_action('wp_ajax_ssc_update_schema', 'ssc_update_schema');

/**
 * AJAX: Delete schema
 */
function ssc_delete_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $schema_id = isset($_POST['schema_id']) ? intval($_POST['schema_id']) : 0;
    
    if ($schema_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid schema ID'));
        exit;
    }
    
    global $wpdb;
    $schemas_table = $wpdb->prefix . 'ssc_schemas';
    $relationships_table = $wpdb->prefix . 'ssc_schema_pages';
    
    // Delete schema
    $result = $wpdb->delete(
        $schemas_table,
        array('id' => $schema_id),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => 'Failed to delete schema'));
        exit;
    }
    
    // Delete page relationships
    $wpdb->delete(
        $relationships_table,
        array('schema_id' => $schema_id),
        array('%d')
    );
    
    wp_send_json_success();
    exit;
}
add_action('wp_ajax_ssc_delete_schema', 'ssc_delete_schema');

/**
 * AJAX: Update page assignments
 */
function ssc_update_page_assignments() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $schema_id = isset($_POST['schema_id']) ? intval($_POST['schema_id']) : 0;
    $pages = isset($_POST['pages']) ? (array) $_POST['pages'] : array();
    
    if ($schema_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid schema ID'));
        exit;
    }
    
    global $wpdb;
    $relationships_table = $wpdb->prefix . 'ssc_schema_pages';
    
    // Begin transaction
    $wpdb->query('START TRANSACTION');
    
    try {
        // Delete existing relationships
        $wpdb->delete(
            $relationships_table,
            array('schema_id' => $schema_id),
            array('%d')
        );
        
        // Insert new relationships
        foreach ($pages as $page_id) {
            $wpdb->insert(
                $relationships_table,
                array(
                    'schema_id' => $schema_id,
                    'page_id' => intval($page_id)
                ),
                array('%d', '%d')
            );
        }
        
        $wpdb->query('COMMIT');
        wp_send_json_success();
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        wp_send_json_error(array('message' => 'Failed to update page assignments'));
    }
    
    exit;
}
add_action('wp_ajax_ssc_update_page_assignments', 'ssc_update_page_assignments');

/**
 * AJAX: Apply schema to pages
 */
function ssc_apply_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $schema_id = isset($_POST['schema_id']) ? intval($_POST['schema_id']) : 0;
    
    if ($schema_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid schema ID'));
        exit;
    }
    
    global $wpdb;
    $schemas_table = $wpdb->prefix . 'ssc_schemas';
    $relationships_table = $wpdb->prefix . 'ssc_schema_pages';
    
    // Get schema
    $schema = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $schemas_table WHERE id = %d",
        $schema_id
    ), ARRAY_A);
    
    if (!$schema) {
        wp_send_json_error(array('message' => 'Schema not found'));
        exit;
    }
    
    // Get assigned pages
    $page_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT page_id FROM $relationships_table WHERE schema_id = %d",
        $schema_id
    ));
    
    if (empty($page_ids)) {
        wp_send_json_error(array('message' => 'No pages assigned to this schema'));
        exit;
    }
    
    // Apply schema to each page
    foreach ($page_ids as $page_id) {
        update_post_meta($page_id, '_ssc_schema', wp_slash($schema['json']));
        update_post_meta($page_id, '_ssc_schema_id', $schema_id);
    }
    
    wp_send_json_success();
    exit;
}
add_action('wp_ajax_ssc_apply_schema', 'ssc_apply_schema');

/**
 * AJAX: Save settings
 */
function ssc_save_settings() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $auto_generate = isset($_POST['auto_generate']) ? (bool) $_POST['auto_generate'] : false;
    $content_types = isset($_POST['content_types']) ? (array) $_POST['content_types'] : array();
    
    // Sanitize content types
    $content_types = array_map('sanitize_text_field', $content_types);
    
    // Save settings
    $settings = array(
        'auto_generate' => $auto_generate,
        'content_types' => $content_types
    );
    
    update_option('ssc_settings', $settings);
    
    wp_send_json_success();
    exit;
}
add_action('wp_ajax_ssc_save_settings', 'ssc_save_settings');

/**
 * Add schema to page head
 */
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
        } else {
            // Check if auto-generate is enabled
            $settings = get_option('ssc_settings', array(
                'auto_generate' => true,
                'content_types' => array('post', 'page')
            ));
            
            if ($settings['auto_generate']) {
                $post_type = get_post_type();
                
                // Check if this post type is in the enabled content types
                if (in_array($post_type, $settings['content_types'])) {
                    // Auto-generate schema based on post type
                    $auto_schema = ssc_generate_auto_schema($post_id);
                    
                    if ($auto_schema) {
                        echo "\n<!-- Auto-generated Schema by Schema Stunt Cock v" . esc_attr(SSC_VERSION) . " -->\n";
                        echo "<script type=\"application/ld+json\">\n";
                        echo wp_kses_post($auto_schema) . "\n";
                        echo "</script>\n";
                    }
                }
            }
        }
    }
}
add_action('wp_head', 'ssc_output_schema', 10);

/**
 * Auto-generate schema based on post type
 */
function ssc_generate_auto_schema($post_id) {
    $post = get_post($post_id);
    
    if (!$post) {
        return false;
    }
    
    $post_type = get_post_type($post);
    $schema = array();
    
    switch ($post_type) {
        case 'post':
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => get_the_title($post),
                'description' => get_the_excerpt($post),
                'datePublished' => get_the_date('c', $post),
                'dateModified' => get_the_modified_date('c', $post),
                'author' => array(
                    '@type' => 'Person',
                    'name' => get_the_author_meta('display_name', $post->post_author)
                )
            );
            
            // Add featured image if available
            if (has_post_thumbnail($post)) {
                $image_id = get_post_thumbnail_id($post);
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                
                if ($image_url) {
                    $schema['image'] = $image_url;
                }
            }
            break;
            
        case 'page':
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => get_the_title($post),
                'description' => get_the_excerpt($post),
                'datePublished' => get_the_date('c', $post),
                   'dateModified' => get_the_modified_date('c', $post),
                'url' => get_permalink($post)
            );
            
            // Add featured image if available
            if (has_post_thumbnail($post)) {
                $image_id = get_post_thumbnail_id($post);
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                
                if ($image_url) {
                    $schema['image'] = $image_url;
                }
            }
            break;
            
        case 'product':
            // Only run if WooCommerce is active
            if (class_exists('WooCommerce')) {
                $product = wc_get_product($post);
                
                if ($product) {
                    $schema = array(
                        '@context' => 'https://schema.org',
                        '@type' => 'Product',
                        'name' => $product->get_name(),
                        'description' => $product->get_short_description(),
                        'sku' => $product->get_sku(),
                        'offers' => array(
                            '@type' => 'Offer',
                            'price' => $product->get_price(),
                            'priceCurrency' => get_woocommerce_currency(),
                            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                            'url' => get_permalink($post)
                        )
                    );
                    
                    // Add product image
                    if ($product->get_image_id()) {
                        $image_url = wp_get_attachment_image_url($product->get_image_id(), 'full');
                        
                        if ($image_url) {
                            $schema['image'] = $image_url;
                        }
                    }
                    
                    // Add rating if available
                    if ($product->get_rating_count() > 0) {
                        $schema['aggregateRating'] = array(
                            '@type' => 'AggregateRating',
                            'ratingValue' => $product->get_average_rating(),
                            'reviewCount' => $product->get_review_count()
                        );
                    }
                }
            }
            break;
    }
    
    if (empty($schema)) {
        return false;
    }
    
    return wp_json_encode($schema, JSON_PRETTY_PRINT);
}

// Register activation hook
function ssc_activate() {
    // Create tables
    ssc_create_tables();
    
    // Create default options
    $default_settings = array(
        'auto_generate' => true,
        'content_types' => array('post', 'page')
    );
    
    add_option('ssc_settings', $default_settings);
    
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