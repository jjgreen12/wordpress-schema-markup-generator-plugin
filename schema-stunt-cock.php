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

// Add detailed debug function
function ssc_debug_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
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
            'pages' => ssc_get_pages_data(),
            'schemaTemplates' => ssc_get_schema_templates()
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
    text-decoration: none;
    color: #333;
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
    margin-bottom: 20px;
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
    border: 1px solid #ddd;
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

.ssc-schema-list {
    margin-top: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.ssc-schema-item {
    padding: 15px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ssc-schema-item:last-child {
    border-bottom: none;
}

.ssc-schema-item-info {
    flex-grow: 1;
}

.ssc-schema-item-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.ssc-schema-item-meta {
    font-size: 12px;
    color: #666;
}

.ssc-schema-item-actions {
    display: flex;
    gap: 10px;
}

.ssc-template-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.ssc-template-card {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    width: calc(25% - 10px);
    cursor: pointer;
    transition: all 0.2s;
}

.ssc-template-card:hover {
    border-color: #2271b1;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.ssc-template-card h3 {
    margin-top: 0;
    margin-bottom: 10px;
}

.ssc-template-card p {
    margin: 0;
    font-size: 13px;
    color: #666;
}

.ssc-page-assignments {
    margin-top: 15px;
}

.ssc-page-assignment {
    padding: 5px 10px;
    background: #f5f5f5;
    border-radius: 4px;
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 12px;
}

.ssc-page-assignment .remove {
    margin-left: 5px;
    cursor: pointer;
    color: #999;
}

.ssc-page-assignment .remove:hover {
    color: #d63638;
}

.ssc-schema-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.ssc-schema-tab {
    padding: 5px 10px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
}

.ssc-schema-tab.active {
    border-bottom-color: #2271b1;
    font-weight: bold;
}

/* Checkbox toggle styles */
.ssc-toggle {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.ssc-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.ssc-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.ssc-toggle-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.ssc-toggle input:checked + .ssc-toggle-slider {
    background-color: #2271b1;
}

.ssc-toggle input:checked + .ssc-toggle-slider:before {
    transform: translateX(20px);
}

.ssc-settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.ssc-settings-card {
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.ssc-settings-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.ssc-setting-row {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ssc-setting-row:last-child {
    margin-bottom: 0;
}

.ssc-setting-label {
    flex-grow: 1;
}

.ssc-setting-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.ssc-setting-description {
    font-size: 12px;
    color: #666;
}

.ssc-type-selector {
    margin-bottom: 15px;
}

.ssc-tab-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.ssc-form-group {
    margin-bottom: 15px;
}

.ssc-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.ssc-button-row {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.ssc-empty-state {
    padding: 20px;
    text-align: center;
    color: #666;
}

.ssc-empty-assignments {
    color: #666;
    font-style: italic;
}

.ssc-info-message {
    background-color: #e6f7ff;
    color: #0070f3;
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
    // Load saved schemas
    let savedSchemas = JSON.parse(localStorage.getItem('sscSavedSchemas') || '[]');
    let currentSchemaId = null;
    let activeTab = localStorage.getItem('sscActiveTab') || 'builder';
    
    // Initialize UI
    initUI();
    
    // Functions to manage schemas
    function initUI() {
        // Set active tab
        $('.ssc-nav-item').removeClass('active');
        $('[data-tab="' + activeTab + '"]').addClass('active');
        $('.ssc-tab').hide();
        $('#ssc-tab-' + activeTab).show();
        
        // If we're on builder tab and have saved schemas, show them
        if (activeTab === 'builder') {
            renderSavedSchemas();
            
            // If editing a schema, show editor
            if (currentSchemaId) {
                showSchemaEditor(currentSchemaId);
            }
        }
        
        // Initialize type selector if it exists and is empty
        if ($('#ssc-schema-type-selector').length && $('#ssc-schema-type-selector').is(':empty')) {
            const schemaTypes = Object.keys(sscData.schemaTemplates);
            let typeOptions = '<option value="">-- Select Schema Type --</option>';
            
            schemaTypes.forEach(type => {
                typeOptions += '<option value="' + type + '">' + type + '</option>';
            });
            
            $('#ssc-schema-type-selector').html(typeOptions);
        }
    }
    
    function renderSavedSchemas() {
        const schemaList = $('#ssc-saved-schemas');
        
        if (savedSchemas.length === 0) {
            schemaList.html('<div class="ssc-empty-state">No schemas created yet. Create your first schema using the form below.</div>');
            return;
        }
        
        let schemaItems = '';
        
        savedSchemas.forEach(schema => {
            const schemaObj = JSON.parse(schema.json);
            const schemaType = schemaObj['@type'] || 'Unknown';
            
            let assignedPages = '';
            if (schema.pages && schema.pages.length > 0) {
                assignedPages = '<div class="ssc-schema-item-pages">';
                assignedPages += 'Applied to ' + schema.pages.length + ' page' + (schema.pages.length !== 1 ? 's' : '');
                assignedPages += '</div>';
            }
            
            schemaItems += '<div class="ssc-schema-item" data-id="' + schema.id + '">';
            schemaItems += '<div class="ssc-schema-item-info">';
            schemaItems += '<div class="ssc-schema-item-title">' + schema.name + '</div>';
            schemaItems += '<div class="ssc-schema-item-meta">' + schemaType + ' • Last updated: ' + schema.lastUpdated + '</div>';
            schemaItems += assignedPages;
            schemaItems += '</div>';
            schemaItems += '<div class="ssc-schema-item-actions">';
            schemaItems += '<button class="ssc-button secondary ssc-edit-schema" data-id="' + schema.id + '">Edit</button>';
            schemaItems += '<button class="ssc-button secondary ssc-delete-schema" data-id="' + schema.id + '">Delete</button>';
            schemaItems += '</div>';
            schemaItems += '</div>';
        });
        
        schemaList.html(schemaItems);
        
        // Add event listeners for edit and delete buttons
        $('.ssc-edit-schema').on('click', function() {
            const schemaId = $(this).data('id');
            showSchemaEditor(schemaId);
        });
        
        $('.ssc-delete-schema').on('click', function() {
            const schemaId = $(this).data('id');
            if (confirm('Are you sure you want to delete this schema?')) {
                deleteSchema(schemaId);
            }
        });
    }
    
    function showSchemaEditor(schemaId) {
        const schema = savedSchemas.find(s => s.id === schemaId);
        
        if (!schema) {
            alert('Schema not found');
            return;
        }
        
        currentSchemaId = schemaId;
        
        // Show the schema editor
        $('#ssc-schema-list-view').hide();
        $('#ssc-schema-editor-view').show();
        
        // Set the editor values
        $('#ssc-schema-name').val(schema.name);
        $('#ssc-schema-editor').val(schema.json);
        
        // Load page assignments
        renderPageAssignments(schema.pages || []);
        
        // Set type selector
        const schemaObj = JSON.parse(schema.json);
        const schemaType = schemaObj['@type'] || '';
        
        if ($('#ssc-schema-type-selector').length) {
            $('#ssc-schema-type-selector').val(schemaType);
        }
    }
    
    function renderPageAssignments(pageIds) {
        const assignmentContainer = $('#ssc-page-assignments');
        assignmentContainer.empty();
        
        if (!pageIds || pageIds.length === 0) {
            assignmentContainer.html('<div class="ssc-empty-assignments">No pages assigned yet.</div>');
            return;
        }
        
        // Find page details from the available pages data
        pageIds.forEach(pageId => {
            const page = sscData.pages.find(p => p.ID === parseInt(pageId));
            
            if (page) {
                const assignment = $('<div class="ssc-page-assignment" data-id="' + page.ID + '">' + 
                    page.post_title + ' (' + page.post_type + ')' +
                    '<span class="remove">×</span>' +
                    '</div>');
                
                assignmentContainer.append(assignment);
            }
        });
        
        // Add event listener for removing assignments
        $('.ssc-page-assignment .remove').on('click', function() {
            const pageId = $(this).parent().data('id');
            removePageAssignment(pageId);
        });
    }
    
    function removePageAssignment(pageId) {
        const schema = savedSchemas.find(s => s.id === currentSchemaId);
        
        if (schema && schema.pages) {
            schema.pages = schema.pages.filter(id => id !== pageId);
            
            // Update localStorage
            localStorage.setItem('sscSavedSchemas', JSON.stringify(savedSchemas));
            
            // Re-render assignments
            renderPageAssignments(schema.pages);
            
            // Save to WordPress if the schema is already saved there
            if (schema.wordpressId) {
                saveSchemaToWordPress(schema);
            }
        }
    }
    
    function createNewSchema(type) {
        // Generate a unique ID
        const id = 'schema_' + Date.now();
        const dateStr = new Date().toLocaleDateString();
        
        // Get the template for this type
        let json = '{}';
        if (type && sscData.schemaTemplates[type]) {
            json = sscData.schemaTemplates[type];
        } else {
            // Default to Article if type not found
            json = sscData.schemaTemplates.Article || '{"@context":"https://schema.org","@type":"Article","headline":"","description":""}';
        }
        
        // Create new schema object
        const newSchema = {
            id: id,
            name: type || 'New Schema',
            json: json,
            pages: [],
            lastUpdated: dateStr
        };
        
        // Add to saved schemas
        savedSchemas.push(newSchema);
        localStorage.setItem('sscSavedSchemas', JSON.stringify(savedSchemas));
        
        // Show the editor
        showSchemaEditor(id);
        
        // Update the list view (hidden but updated)
        renderSavedSchemas();
    }
    
    function deleteSchema(schemaId) {
        const schema = savedSchemas.find(s => s.id === schemaId);
        
        // Remove from saved schemas
        savedSchemas = savedSchemas.filter(s => s.id !== schemaId);
        localStorage.setItem('sscSavedSchemas', JSON.stringify(savedSchemas));
        
        // If this was the current schema, clear it
        if (currentSchemaId === schemaId) {
            currentSchemaId = null;
            $('#ssc-schema-editor-view').hide();
            $('#ssc-schema-list-view').show();
        }
        
        // Update the list view
        renderSavedSchemas();
        
        // If the schema was saved to WordPress, delete it there too
        if (schema && schema.wordpressId) {
            // Call AJAX to delete from WordPress
            $.ajax({
                url: sscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ssc_delete_schema',
                    nonce: sscData.nonce,
                    schema_id: schema.wordpressId
                },
                success: function(response) {
                    if (!response.success) {
                        alert('Warning: Schema deleted from local storage but could not be removed from WordPress. ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Warning: Schema deleted from local storage but could not be removed from WordPress due to a server error.');
                }
            });
        }
    }
    
    function updateSchema() {
        if (!currentSchemaId) {
            alert('No schema selected for update');
            return false;
        }
        
        const schemaName = $('#ssc-schema-name').val();
        const schemaJson = $('#ssc-schema-editor').val();
        
        // Validate JSON
        try {
            JSON.parse(schemaJson);
        } catch (e) {
            alert('Invalid JSON: ' + e.message);
            return false;
        }
        
        // Find and update the schema
        const schema = savedSchemas.find(s => s.id === currentSchemaId);
        
        if (!schema) {
            alert('Schema not found');
            return false;
        }
        
        schema.name = schemaName || 'Unnamed Schema';
        schema.json = schemaJson;
        schema.lastUpdated = new Date().toLocaleDateString();
        
        // Update localStorage
        localStorage.setItem('sscSavedSchemas', JSON.stringify(savedSchemas));
        
        // Success!
        $('#ssc-save-result').html('<div class="ssc-success-message">Schema updated successfully!</div>');
        setTimeout(() => {
            $('#ssc-save-result').empty();
        }, 3000);
        
        // Update the list (even though it's hidden)
        renderSavedSchemas();
        
        return true;
    }
    
    function validateSchema(json) {
        try {
            // Try to parse the JSON
            const schema = JSON.parse(json);
            
            // Check for required properties based on type
            let errors = [];
            
            // All schemas need @context and @type
            if (!schema['@context']) {
                errors.push('Missing required @context property');
            }
            
            if (!schema['@type']) {
                errors.push('Missing required @type property');
            }
            
            // Type-specific validation
            if (schema['@type']) {
                switch(schema['@type']) {
                    case 'Article':
                    case 'BlogPosting':
                        if (!schema.headline) {
                            errors.push('Missing required "headline" property for ' + schema['@type']);
                        }
                        break;
                        
                    case 'Product':
                        if (!schema.name) {
                            errors.push('Missing required "name" property for Product');
                        }
                        break;
                        
                    case 'LocalBusiness':
                        if (!schema.name) {
                            errors.push('Missing required "name" property for LocalBusiness');
                        }
                        break;
                        
                    case 'Event':
                        if (!schema.name) {
                            errors.push('Missing required "name" property for Event');
                        }
                        if (!schema.startDate) {
                            errors.push('Missing required "startDate" property for Event');
                        }
                        break;
                        
                    case 'FAQPage':
                        if (!schema.mainEntity || !Array.isArray(schema.mainEntity)) {
                            errors.push('FAQPage requires "mainEntity" array property');
                        }
                        break;
                }
            }
            
            return {
                valid: errors.length === 0,
                errors: errors
            };
        } catch (e) {
            return {
                valid: false,
                errors: ['Invalid JSON: ' + e.message]
            };
        }
    }
    
    function saveSchemaToWordPress(schema) {
        // Save the schema to all assigned pages
        if (!schema.pages || schema.pages.length === 0) {
            alert('Please select at least one page to apply this schema to.');
            return;
        }
        
        // Show loading state
        $('#ssc-save-result').html('<div class="ssc-info-message">Saving schema to WordPress...</div>');
        
        // Create promises for each page
        const savePromises = schema.pages.map(pageId => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: sscData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'ssc_save_schema',
                        nonce: sscData.nonce,
                        post_id: pageId,
                        schema: schema.json,
                        schema_name: schema.name,
                        schema_id: schema.id
                    },
                    success: function(response) {
                        if (response.success) {
                            resolve(response);
                            
                            // Store the WordPress ID if provided
                            if (response.data && response.data.schema_id) {
                                schema.wordpressId = response.data.schema_id;
                            }
                        } else {
                            reject(response.data.message);
                        }
                    },
                    error: function() {
                        reject('Server error while saving schema');
                    }
                });
            });
        });
        
        // Wait for all saves to complete
        Promise.all(savePromises)
            .then(() => {
                // Update the schema in localStorage with the WordPress ID if it was set
                localStorage.setItem('sscSavedSchemas', JSON.stringify(savedSchemas));
                
                // Show success message
                $('#ssc-save-result').html('<div class="ssc-success-message">Schema successfully applied to selected pages!</div>');
                setTimeout(() => {
                    $('#ssc-save-result').empty();
                }, 3000);
            })
            .catch(error => {
                $('#ssc-save-result').html('<div class="ssc-error-message">Error: ' + error + '</div>');
            });
    }
    
    // Event Handlers
    
    // Tab Navigation
    $('.ssc-nav-item').on('click', function(e) {
        e.preventDefault();
        const tabId = $(this).data('tab');
        
        // Update active tab
        $('.ssc-nav-item').removeClass('active');
        $(this).addClass('active');
        
        // Show selected tab
        $('.ssc-tab').hide();
        $('#ssc-tab-' + tabId).show();
        
        // Store the active tab
        activeTab = tabId;
        localStorage.setItem('sscActiveTab', tabId);
    });
    
    // Schema Type selection
    $('#ssc-schema-type-selector').on('change', function() {
        const selectedType = $(this).val();
        
        if (selectedType && sscData.schemaTemplates[selectedType]) {
            // If we're creating a new schema, just update the editor
            if (!currentSchemaId) {
                $('#ssc-schema-editor').val(sscData.schemaTemplates[selectedType]);
            } else {
                // If we're editing an existing schema, confirm before changing
                if (confirm('Changing the schema type will replace your current schema JSON. Continue?')) {
                    $('#ssc-schema-editor').val(sscData.schemaTemplates[selectedType]);
                } else {
                    // Reset the selector to the current type
                    const schema = savedSchemas.find(s => s.id === currentSchemaId);
                    if (schema) {
                        const schemaObj = JSON.parse(schema.json);
                        $(this).val(schemaObj['@type'] || '');
                    }
                }
            }
        }
    });
    
    // Schema tabs (within editor)
    $('.ssc-schema-tab').on('click', function() {
        const tabId = $(this).data('tab');
        
        // Update active tab
        $('.ssc-schema-tab').removeClass('active');
        $(this).addClass('active');
        
        // Show selected tab content
        $('.ssc-schema-tab-content').hide();
        $('#ssc-tab-' + tabId).show();
    });
    
    // Create New Schema button
    $('#ssc-create-schema').on('click', function() {
        const selectedType = $('#ssc-new-schema-type').val();
        createNewSchema(selectedType);
    });
    
    // Back to List button
    $('#ssc-back-to-list').on('click', function() {
        currentSchemaId = null;
        $('#ssc-schema-editor-view').hide();
        $('#ssc-schema-list-view').show();
    });
    
    // Update Schema button
    $('#ssc-update-schema').on('click', function() {
        if (updateSchema()) {
            // Optionally add more actions after successful update
        }
    });
    
    // Apply to Pages button
    $('#ssc-apply-schema').on('click', function() {
        // First make sure the schema is updated
        if (updateSchema()) {
            // Now save to WordPress
            const schema = savedSchemas.find(s => s.id === currentSchemaId);
            if (schema) {
                saveSchemaToWordPress(schema);
            }
        }
    });
    
    // Add Page button
    $('#ssc-add-page').on('click', function() {
        const selectedPage = $('#ssc-page-selector').val();
        
        if (!selectedPage) {
            alert('Please select a page to add');
            return;
        }
        
        const schema = savedSchemas.find(s => s.id === currentSchemaId);
        
        if (!schema) {
            alert('No schema selected');
            return;
        }
        
        // Initialize pages array if it doesn't exist
        if (!schema.pages) {
            schema.pages = [];
        }
        
        // Check if page is already assigned
        if (schema.pages.includes(selectedPage)) {
            alert('This page is already assigned to this schema');
            return;
        }
        
        // Add the page
        schema.pages.push(selectedPage);
        
        // Update localStorage
        localStorage.setItem('sscSavedSchemas', JSON.stringify(savedSchemas));
        
        // Re-render page assignments
        renderPageAss
        ignments(schema.pages);
    });
    
    // Schema validation
    $('#ssc-validate-schema-button').on('click', function() {
        const schemaToValidate = $('#ssc-validator-input').val();
        
        if (!schemaToValidate) {
            $('#ssc-validation-result').html('<div class="ssc-error-message">Please enter schema JSON to validate</div>');
            return;
        }
        
        const result = validateSchema(schemaToValidate);
        
        if (result.valid) {
            $('#ssc-validation-result').html('<div class="ssc-success-message">Schema is valid!</div>');
        } else {
            let errorHtml = '<div class="ssc-error-message">';
            errorHtml += '<strong>Schema validation failed:</strong>';
            errorHtml += '<ul>';
            
            result.errors.forEach(error => {
                errorHtml += '<li>' + error + '</li>';
            });
            
            errorHtml += '</ul></div>';
            
            $('#ssc-validation-result').html(errorHtml);
        }
    });
    
    // Save Settings button
    $('#ssc-save-settings').on('click', function() {
        // Get settings values
        const autoGenerate = $('input[name="auto_generate"]:checked').val();
        
        const contentTypes = [];
        $('input[name="content_types[]"]:checked').each(function() {
            contentTypes.push($(this).val());
        });
        
        // Save settings to WordPress
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssc_save_settings',
                nonce: sscData.nonce,
                auto_generate: autoGenerate,
                content_types: contentTypes
            },
            success: function(response) {
                if (response.success) {
                    $('#ssc-settings-result').html('<div class="ssc-success-message">Settings saved successfully!</div>');
                    setTimeout(() => {
                        $('#ssc-settings-result').empty();
                    }, 3000);
                } else {
                    $('#ssc-settings-result').html('<div class="ssc-error-message">Error: ' + response.data.message + '</div>');
                }
            },
            error: function() {
                $('#ssc-settings-result').html('<div class="ssc-error-message">Server error while saving settings</div>');
            }
        });
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

// Admin page content with functional UI
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
                    <div id="ssc-saved-schemas" class="ssc-schema-list">
                        <!-- Schema items will be rendered by JavaScript -->
                    </div>
                </div>
                
                <!-- Schema Editor View -->
                <div id="ssc-schema-editor-view" style="display: none;">
                    <div class="ssc-tab-header">
                        <h2>Edit Schema</h2>
                        <button id="ssc-back-to-list" class="ssc-button secondary">← Back to List</button>
                    </div>
                    
                    <div class="ssc-schema-tabs">
                        <div class="ssc-schema-tab active" data-tab="json">JSON-LD</div>
                        <div class="ssc-schema-tab" data-tab="pages">Page Assignments</div>
                    </div>
                    
                    <div class="ssc-schema-tab-content" id="ssc-tab-json">
                        <div class="ssc-grid">
                            <div>
                                <div class="ssc-form-group">
                                    <label for="ssc-schema-name">Schema Name</label>
                                    <input type="text" id="ssc-schema-name" class="regular-text" placeholder="Enter a name for this schema">
                                </div>
                                
                                <div class="ssc-form-group">
                                    <label for="ssc-schema-type-selector">Schema Type</label>
                                    <select id="ssc-schema-type-selector" class="regular-text">
                                        <!-- Options will be filled by JavaScript -->
                                    </select>
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
                    
                    <div class="ssc-schema-tab-content" id="ssc-tab-pages" style="display: none;">
                        <!-- Page assignment UI will be added here -->
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
                
                <div class="ssc-settings-grid">
                    <div class="ssc-settings-card">
                        <h3>General Settings</h3>
                        
                        <div class="ssc-setting-row">
                            <div class="ssc-setting-label">
                                <div class="ssc-setting-title">Auto-generate Schema</div>
                                <div class="ssc-setting-description">Automatically generate schema for eligible content types</div>
                            </div>
                            <div>
                                <label class="ssc-toggle">
                                    <input type="radio" name="auto_generate" value="1" checked>
                                    <span class="ssc-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ssc-settings-card">
                        <h3>Content Types</h3>
                        <p>Enable schema generation for these content types:</p>
                        
                        <div style="margin-top: 15px;">
                            <label class="ssc-checkbox">
                                <input type="checkbox" name="content_types[]" value="post" checked>
                                Posts
                            </label>
                        </div>
                        
                        <div style="margin-top: 10px;">
                            <label class="ssc-checkbox">
                                <input type="checkbox" name="content_types[]" value="page" checked>
                                Pages
                            </label>
                        </div>
                        
                        <?php if (class_exists('WooCommerce')): ?>
                        <div style="margin-top: 10px;">
                            <label class="ssc-checkbox">
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
    $schema_name = isset($_POST['schema_name']) ? sanitize_text_field($_POST['schema_name']) : '';
    $schema_id = isset($_POST['schema_id']) ? sanitize_text_field($_POST['schema_id']) : '';
    
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
    
    // Save the schema to post meta
    update_post_meta($post_id, '_ssc_schema', wp_slash($schema));
    
    // Save schema name and ID if provided
    if (!empty($schema_name)) {
        update_post_meta($post_id, '_ssc_schema_name', $schema_name);
    }
    
    if (!empty($schema_id)) {
        update_post_meta($post_id, '_ssc_schema_id', $schema_id);
    }
    
    // Success response
    wp_send_json_success(array(
        'message' => 'Schema saved successfully.',
        'schema_id' => $schema_id
    ));
    exit;
}
add_action('wp_ajax_ssc_save_schema', 'ssc_save_schema');

// Delete schema from post meta
function ssc_delete_schema() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access.'));
        exit;
    }
    
    // Get data
    $schema_id = isset($_POST['schema_id']) ? sanitize_text_field($_POST['schema_id']) : '';
    
    if (empty($schema_id)) {
        wp_send_json_error(array('message' => 'Invalid schema ID.'));
        exit;
    }
    
    // Find all posts that have this schema ID
    $args = array(
        'post_type' => 'any',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_ssc_schema_id',
                'value' => $schema_id,
                'compare' => '='
            )
        )
    );
    
    $posts = get_posts($args);
    
    // Delete schema from all found posts
    foreach ($posts as $post) {
        delete_post_meta($post->ID, '_ssc_schema');
        delete_post_meta($post->ID, '_ssc_schema_name');
        delete_post_meta($post->ID, '_ssc_schema_id');
    }
    
    // Success response
    wp_send_json_success(array(
        'message' => 'Schema deleted successfully.',
        'affected_posts' => count($posts)
    ));
    exit;
}
add_action('wp_ajax_ssc_delete_schema', 'ssc_delete_schema');

// Save plugin settings
function ssc_save_settings() {
    // Verify nonce
    check_ajax_referer('ssc_nonce', 'nonce');
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access.'));
        exit;
    }
    
    // Get data
    $auto_generate = isset($_POST['auto_generate']) ? sanitize_text_field($_POST['auto_generate']) : '0';
    $content_types = isset($_POST['content_types']) ? (array) $_POST['content_types'] : array();
    
    // Sanitize content types
    $content_types = array_map('sanitize_text_field', $content_types);
    
    // Save settings
    $settings = array(
        'auto_generate' => $auto_generate === '1',
        'content_types' => $content_types
    );
    
    update_option('ssc_settings', $settings);
    
    // Success response
    wp_send_json_success(array('message' => 'Settings saved successfully.'));
    exit;
}
add_action('wp_ajax_ssc_save_settings', 'ssc_save_settings');

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

// Auto-generate schema based on post type
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