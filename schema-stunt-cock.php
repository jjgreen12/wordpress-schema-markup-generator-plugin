<?php
/**
 * Plugin Name: Schema Stunt Cock
 * Description: Advanced schema markup generator for WordPress
 * Version: 1.0.6
 * Author: BigHouse
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SSC_VERSION', '1.0.3');
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

/**
 * AJAX: Get schema by ID
 */
function ssc_get_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    // Get data
    $schema_id = isset($_GET['schema_id']) ? intval($_GET['schema_id']) : 0;
    
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
    
    // Get page assignments
    $page_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT page_id FROM $relationships_table WHERE schema_id = %d",
        $schema_id
    ));
    
    $schema['pages'] = $page_ids;
    $schema['last_updated'] = date('F j, Y, g:i a', strtotime($schema['updated_at']));
    
    wp_send_json_success(array('schema' => $schema));
    exit;
}
add_action('wp_ajax_ssc_get_schema', 'ssc_get_schema');

// Admin page content
function ssc_admin_page() {
    // Get all schemas from database
    global $wpdb;
    $schemas_table = $wpdb->prefix . 'ssc_schemas';
    $relationships_table = $wpdb->prefix . 'ssc_schema_pages';
    
    // Get all schemas with their page counts
    $schemas = $wpdb->get_results("
        SELECT s.*, COUNT(p.page_id) as page_count
        FROM $schemas_table s
        LEFT JOIN $relationships_table p ON s.id = p.schema_id
        GROUP BY s.id
        ORDER BY s.updated_at DESC
    ", ARRAY_A);
    
    // Get all pages for the dropdown
    $pages = ssc_get_pages_data();
    ?>
    <div class="wrap">
        <div class="ssc-container">
            <h1>Schema Stunt Cock</h1>
            
            <ul class="ssc-tabs">
                <li class="active"><a href="?page=schema-stunt-cock">Builder</a></li>
                <li><a href="?page=schema-stunt-cock&tab=validator">Validator</a></li>
                <li><a href="?page=schema-stunt-cock&tab=settings">Settings</a></li>
            </ul>
            
            <!-- Builder Tab -->
            <div class="ssc-tab-content">
                <h2>Your Schemas</h2>
                
                <div class="ssc-schema-selector">
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
                    <button id="ssc-create-schema" class="button-primary">Create New Schema</button>
                </div>
                
                <div class="ssc-schemas-list">
                    <?php if (empty($schemas)) : ?>
                        <div class="ssc-empty-state">No schemas created yet. Create your first schema using the form above.</div>
                    <?php else : ?>
                        <?php foreach ($schemas as $schema) : 
                            $schema_obj = json_decode($schema['json'], true);
                            $schema_type = isset($schema_obj['@type']) ? $schema_obj['@type'] : 'Unknown';
                            
                            // Get assigned pages
                            $page_ids = $wpdb->get_col($wpdb->prepare(
                                "SELECT page_id FROM $relationships_table WHERE schema_id = %d",
                                $schema['id']
                            ));
                            ?>
                            <div class="ssc-schema-item">
                                <div class="ssc-schema-item-info">
                                    <h3><?php echo esc_html($schema['name']); ?></h3>
                                    <div class="ssc-schema-type"><?php echo esc_html($schema_type); ?></div>
                                    <div class="ssc-schema-updated">Last updated: <?php echo date('F j, Y, g:i a', strtotime($schema['updated_at'])); ?></div>
                                    <?php if (!empty($page_ids)) : ?>
                                        <div class="ssc-schema-pages">Applied to <?php echo count($page_ids); ?> page(s)</div>
                                    <?php endif; ?>
                                </div>
                                <div class="ssc-schema-item-actions">
                                    <button class="button schema-edit-btn" data-schema-id="<?php echo esc_attr($schema['id']); ?>">Edit</button>
                                    <button class="button schema-delete-btn" data-schema-id="<?php echo esc_attr($schema['id']); ?>">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Schema Editor View (initially hidden) -->
                <div id="ssc-schema-editor-view" style="display: none;">
                    <div class="ssc-tab-header">
                        <h2>Edit Schema</h2>
                        <button id="ssc-back-to-list" class="button">← Back to List</button>
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
                                <button id="ssc-update-schema" class="button-primary">Update Schema</button>
                                <button id="ssc-apply-schema" class="button-primary">Apply to Pages</button>
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
                                <button id="ssc-add-page" class="button">Add Page</button>
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
            
            <!-- Other tabs would go here -->
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
    $json = isset($_POST['json']) ? stripslashes($_POST['json']) : '{}'; // Don't sanitize JSON, but strip slashes
    
    // Debug info
    error_log('Creating schema: ' . $name . ' of type ' . $type);
    error_log('JSON: ' . $json);
    
    // Validate JSON
    json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON error: ' . json_last_error_msg());
        wp_send_json_error(array('message' => 'Invalid JSON format: ' . json_last_error_msg()));
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
        error_log('Database error: ' . $wpdb->last_error);
        wp_send_json_error(array('message' => 'Failed to create schema: ' . $wpdb->last_error));
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
    $json = isset($_POST['json']) ? stripslashes($_POST['json']) : ''; // Don't sanitize JSON, but strip slashes
    
    if ($schema_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid schema ID'));
        exit;
    }
    
    // Validate JSON
    json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(array('message' => 'Invalid JSON format: ' . json_last_error_msg()));
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
        wp_send_json_error(array('message' => 'Failed to update schema: ' . $wpdb->last_error));
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
    
    // Remove schema from all pages before deleting
    ssc_remove_schema_from_all_pages($schema_id);
    
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
        // First, remove this schema from all pages where it was previously applied
        ssc_remove_schema_from_all_pages($schema_id);
        
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
 * FIXED: AJAX Apply Schema function
 */
function ssc_apply_schema() {
    check_ajax_referer('ssc_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
        exit;
    }
    
    $schema_id = isset($_POST['schema_id']) ? intval($_POST['schema_id']) : 0;
    
    if ($schema_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid schema ID'));
        exit;
    }
    
    global $wpdb;
    $schemas_table = $wpdb->prefix . 'ssc_schemas';
    $relationships_table = $wpdb->prefix . 'ssc_schema_pages';
    
    // Get schema from database
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
    
    // Clean the JSON from database
    $raw_json = $schema['json'];
    $clean_json = wp_unslash($raw_json);
    $clean_json = stripslashes($clean_json);
    
    // Validate JSON
    $parsed = json_decode($clean_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(array('message' => 'Schema contains invalid JSON: ' . json_last_error_msg()));
        exit;
    }
    
    // Re-encode cleanly
    $final_json = wp_json_encode($parsed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
    // Apply to pages
    $applied_count = 0;
    $failed_pages = array();
    
    foreach ($page_ids as $page_id) {
        if (ssc_add_schema_to_page($page_id, $schema_id, $final_json)) {
            $applied_count++;
        } else {
            $failed_pages[] = $page_id;
        }
    }
    
    if ($applied_count > 0) {
        $message = "Schema successfully applied to {$applied_count} page(s)";
        if (!empty($failed_pages)) {
            $message .= ". Failed on pages: " . implode(', ', $failed_pages);
        }
        
        wp_send_json_success(array(
            'message' => $message,
            'applied_count' => $applied_count,
            'failed_pages' => $failed_pages
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to apply schema to any pages'));
    }
    
    exit;
}

add_action('wp_ajax_ssc_apply_schema', 'ssc_apply_schema');

/**
 * FIXED: Add schema to a page (supports multiple schemas per page)
 */
/**
 * FIXED: Add schema to a page with proper JSON handling
 */
function ssc_add_schema_to_page($page_id, $schema_id, $schema_json) {
    // Validate inputs
    if (empty($page_id) || empty($schema_id) || empty($schema_json)) {
        error_log("SSC: Invalid parameters for ssc_add_schema_to_page");
        return false;
    }
    
    // CRITICAL FIX: Proper JSON cleaning and validation
    $schema_json = wp_unslash($schema_json); // Remove WordPress slashes
    $schema_json = stripslashes($schema_json); // Remove any remaining slashes
    $schema_json = trim($schema_json); // Remove whitespace
    
    // Parse JSON to validate and clean it
    $parsed_schema = json_decode($schema_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("SSC: JSON decode error for schema $schema_id: " . json_last_error_msg());
        error_log("SSC: Raw JSON (first 500 chars): " . substr($schema_json, 0, 500));
        return false;
    }
    
    // Re-encode as clean JSON without escaping
    $clean_json = wp_json_encode($parsed_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
    if ($clean_json === false) {
        error_log("SSC: Failed to encode clean JSON for schema $schema_id");
        return false;
    }
    
    // Get existing schemas
    $existing_schemas = get_post_meta($page_id, '_ssc_schemas', true);
    if (!is_array($existing_schemas)) {
        $existing_schemas = array();
    }
    
    // Store schema with clean JSON
    $existing_schemas[$schema_id] = array(
        'id' => intval($schema_id),
        'json' => $clean_json,
        'applied_at' => current_time('mysql')
    );
    
    // Update meta with proper serialization
    $update_result = update_post_meta($page_id, '_ssc_schemas', $existing_schemas);
    
    // Update schema IDs list
    $schema_ids = array_keys($existing_schemas);
    update_post_meta($page_id, '_ssc_schema_ids', $schema_ids);
    
    error_log("SSC: Successfully stored schema $schema_id for page $page_id");
    return $update_result !== false;
}

/**
 * Remove schema from a page - FIXED VERSION
 */
function ssc_remove_schema_from_page($page_id, $schema_id) {
    // Get existing schemas for this page
    $existing_schemas = get_post_meta($page_id, '_ssc_schemas', true);
    
    if (!is_array($existing_schemas)) {
        return true; // Nothing to remove
    }
    
    // Remove this schema
    unset($existing_schemas[$schema_id]);
    
    if (empty($existing_schemas)) {
        // No schemas left, remove the meta fields
        delete_post_meta($page_id, '_ssc_schemas');
        delete_post_meta($page_id, '_ssc_schema_ids');
    } else {
        // Update the remaining schemas
        update_post_meta($page_id, '_ssc_schemas', $existing_schemas);
        $schema_ids = array_keys($existing_schemas);
        update_post_meta($page_id, '_ssc_schema_ids', $schema_ids);
    }
    
    return true;
}

/**
 * Remove schema from all pages (used when deleting a schema) - FIXED VERSION
 */
function ssc_remove_schema_from_all_pages($schema_id) {
    global $wpdb;
    
    // Get all pages that have schemas
    $pages_with_schemas = $wpdb->get_results(
        "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_ssc_schemas'",
        ARRAY_A
    );
    
    foreach ($pages_with_schemas as $page_meta) {
        $page_id = $page_meta['post_id'];
        $schemas_data = maybe_unserialize($page_meta['meta_value']);
        
        if (is_array($schemas_data) && isset($schemas_data[$schema_id])) {
            ssc_remove_schema_from_page($page_id, $schema_id);
        }
    }
}

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
 * FIXED: Schema output with proper JSON handling
 */
function ssc_output_schema() {
    if (is_singular()) {
        $post_id = get_the_ID();
        
        // Get schemas from post meta
        $page_schemas = get_post_meta($post_id, '_ssc_schemas', true);
        
        if (is_array($page_schemas) && !empty($page_schemas)) {
            echo "\n<!-- Schema by Schema Stunt Cock v" . esc_attr(SSC_VERSION) . " -->\n";
            
            foreach ($page_schemas as $schema_id => $schema_data) {
                if (isset($schema_data['json']) && !empty($schema_data['json'])) {
                    // Get the stored JSON
                    $stored_json = $schema_data['json'];
                    
                    // Validate JSON before output
                    $parsed_schema = json_decode($stored_json, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed_schema)) {
                        // Format for pretty output
                        $formatted_json = wp_json_encode($parsed_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        
                        if ($formatted_json !== false) {
                            // Apply filters
                            $output_json = apply_filters('ssc_schema_output', $formatted_json, $post_id, $schema_id);
                            
                            echo "<script type=\"application/ld+json\">\n";
                            echo $output_json . "\n";
                            echo "</script>\n";
                        } else {
                            error_log("SSC: Failed to format JSON for output - Schema $schema_id");
                        }
                    } else {
                        error_log("SSC: Invalid stored JSON for schema $schema_id: " . json_last_error_msg());
                    }
                }
            }
        } else {
            // Legacy schema handling
            $legacy_schema = get_post_meta($post_id, '_ssc_schema', true);
            if ($legacy_schema) {
                $clean_legacy = wp_unslash($legacy_schema);
                $parsed_legacy = json_decode($clean_legacy, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    $legacy_schema_id = get_post_meta($post_id, '_ssc_schema_id', true);
                    if ($legacy_schema_id) {
                        // Migrate to new format
                        $clean_json = wp_json_encode($parsed_legacy, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        ssc_add_schema_to_page($post_id, $legacy_schema_id, $clean_json);
                        
                        // Clean up
                        delete_post_meta($post_id, '_ssc_schema');
                        delete_post_meta($post_id, '_ssc_schema_id');
                    }
                    
                    // Output migrated schema
                    echo "\n<!-- Migrated Schema by Schema Stunt Cock v" . esc_attr(SSC_VERSION) . " -->\n";
                    echo "<script type=\"application/ld+json\">\n";
                    echo wp_json_encode($parsed_legacy, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
                    echo "</script>\n";
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

/**
 * Debug function to check current page schemas
 */
function ssc_debug_page_schemas() {
    if (current_user_can('manage_options') && isset($_GET['ssc_debug'])) {
        if (is_singular()) {
            $post_id = get_the_ID();
            $schemas = get_post_meta($post_id, '_ssc_schemas', true);
            echo "\n<!-- SSC DEBUG: Page $post_id schemas: " . print_r($schemas, true) . " -->\n";
        }
    }
}
add_action('wp_head', 'ssc_debug_page_schemas', 1);

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

// Add this to your schema-stunt-cock.php temporarily
function ssc_debug_paths() {
    if (current_user_can('manage_options')) {
        echo "<!--\n";
        echo "Plugin URL: " . SSC_PLUGIN_URL . "\n";
        echo "Plugin Dir: " . SSC_PLUGIN_DIR . "\n";
        echo "File: " . __FILE__ . "\n";
        echo "-->\n";
    }
}
add_action('wp_head', 'ssc_debug_paths');

function ssc_debug_page() {
    add_management_page(
        'SSC Debug',
        'SSC Debug',
        'manage_options',
        'ssc-debug',
        'ssc_debug_output'
    );
}
add_action('admin_menu', 'ssc_debug_page');

function ssc_debug_output() {
    echo '<h1>SSC Debug Info</h1>';
    echo '<p>Plugin URL: ' . SSC_PLUGIN_URL . '</p>';
    echo '<p>Plugin DIR: ' . SSC_PLUGIN_DIR . '</p>';
    echo '<p>File exists CSS: ' . (file_exists(SSC_PLUGIN_DIR . 'css/schema-admin.css') ? 'Yes' : 'No') . '</p>';
    echo '<p>File exists JS: ' . (file_exists(SSC_PLUGIN_DIR . 'js/schema-admin.js') ? 'Yes' : 'No') . '</p>';
    echo '<p>Directory structure:<pre>';
    var_dump(scandir(SSC_PLUGIN_DIR));
    echo '</pre></p>';
}

/**
 * Debug function to show current schema status
 */
function ssc_debug_current_schemas() {
    if (current_user_can('manage_options') && isset($_GET['ssc_debug'])) {
        if (is_singular()) {
            $post_id = get_the_ID();
            $schemas = get_post_meta($post_id, '_ssc_schemas', true);
            
            echo "\n<!-- SSC DEBUG for Post $post_id -->\n";
            
            if (is_array($schemas)) {
                foreach ($schemas as $schema_id => $schema_data) {
                    echo "<!-- Schema $schema_id: -->\n";
                    echo "<!-- JSON Length: " . strlen($schema_data['json']) . " -->\n";
                    echo "<!-- JSON Valid: " . (json_decode($schema_data['json']) ? 'YES' : 'NO') . " -->\n";
                    echo "<!-- JSON Error: " . json_last_error_msg() . " -->\n";
                    echo "<!-- First 100 chars: " . substr($schema_data['json'], 0, 100) . "... -->\n";
                }
            } else {
                echo "<!-- No schemas found or invalid format -->\n";
            }
            
            echo "<!-- SSC DEBUG END -->\n";
        }
    }
}
add_action('wp_head', 'ssc_debug_current_schemas', 1);
/**
 * Enhanced debug function
 */
function ssc_debug_schema_json() {
    if (current_user_can('manage_options') && isset($_GET['ssc_debug'])) {
        if (is_singular()) {
            $post_id = get_the_ID();
            $schemas = get_post_meta($post_id, '_ssc_schemas', true);
            
            echo "\n<!-- SSC JSON DEBUG for Post $post_id -->\n";
            
            if (is_array($schemas)) {
                foreach ($schemas as $schema_id => $schema_data) {
                    echo "<!-- Schema ID: $schema_id -->\n";
                    
                    if (isset($schema_data['json'])) {
                        $json = $schema_data['json'];
                        $parsed = json_decode($json, true);
                        
                        echo "<!-- JSON Length: " . strlen($json) . " -->\n";
                        echo "<!-- JSON Valid: " . (json_last_error() === JSON_ERROR_NONE ? 'YES' : 'NO') . " -->\n";
                        echo "<!-- JSON Error: " . json_last_error_msg() . " -->\n";
                        echo "<!-- JSON Preview: " . substr($json, 0, 200) . "... -->\n";
                        
                        if (json_last_error() === JSON_ERROR_NONE) {
                            echo "<!-- Parsed Type: " . (isset($parsed['@type']) ? $parsed['@type'] : 'Unknown') . " -->\n";
                        }
                    }
                    
                    echo "<!-- --- -->\n";
                }
            } else {
                echo "<!-- No valid schemas found -->\n";
            }
            
            echo "<!-- SSC JSON DEBUG END -->\n";
        }
    }
}
add_action('wp_head', 'ssc_debug_schema_json', 1);

/**
 * Admin function to clean schemas via admin panel
 */
function ssc_add_clean_schemas_admin_notice() {
    if (current_user_can('manage_options') && isset($_GET['page']) && $_GET['page'] === 'schema-stunt-cock') {
        if (isset($_GET['clean_schemas']) && $_GET['clean_schemas'] === '1') {
            $cleaned = ssc_clean_existing_schemas();
            echo '<div class="notice notice-success"><p>Cleaned schemas on ' . $cleaned . ' posts.</p></div>';
        } else {
            $current_url = admin_url('admin.php?page=schema-stunt-cock&clean_schemas=1');
            echo '<div class="notice notice-info"><p>If you\'re having JSON issues, <a href="' . esc_url($current_url) . '">click here to clean existing schemas</a>.</p></div>';
        }
    }
}
add_action('admin_notices', 'ssc_add_clean_schemas_admin_notice');