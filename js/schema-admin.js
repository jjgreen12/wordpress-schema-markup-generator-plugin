/**
 * Schema Stunt Cock - Admin JavaScript
 * Handles all admin-side interactions
 */
jQuery(document).ready(function($) {
    // Store schemas locally
    let savedSchemas = [];
    let currentSchemaId = null;
    
    // Load schemas from WordPress
    function loadSchemas() {
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'GET',
            data: {
                action: 'ssc_get_all_schemas',
                nonce: sscData.nonce
            },
            success: function(response) {
                if (response.success && response.data.schemas) {
                    savedSchemas = response.data.schemas;
                    renderSchemaList();
                }
            },
            error: function() {
                alert('Failed to load schemas. Please refresh the page and try again.');
            }
        });
    }
    
    // Render the schema list
    function renderSchemaList() {
        const schemaList = $('#ssc-schema-list');
        
        if (savedSchemas.length === 0) {
            schemaList.html('<div class="ssc-empty-state">No schemas created yet. Create your first schema using the form below.</div>');
            return;
        }
        
        let html = '';
        savedSchemas.forEach(schema => {
            const schemaObj = JSON.parse(schema.json);
            const schemaType = schemaObj['@type'] || 'Unknown';
            
            let assignedPages = '';
            if (schema.pages && schema.pages.length > 0) {
                assignedPages = '<div class="ssc-schema-item-pages">';
                assignedPages += 'Applied to ' + schema.pages.length + ' page' + (schema.pages.length !== 1 ? 's' : '');
                assignedPages += '</div>';
            }
            
            html += '<div class="ssc-schema-item" data-id="' + schema.id + '">';
            html += '<div class="ssc-schema-item-info">';
            html += '<div class="ssc-schema-item-title">' + schema.name + '</div>';
            html += '<div class="ssc-schema-item-meta">' + schemaType + ' • Last updated: ' + schema.last_updated + '</div>';
            html += assignedPages;
            html += '</div>';
            html += '<div class="ssc-schema-item-actions">';
            html += '<button class="ssc-button secondary ssc-edit-schema" data-id="' + schema.id + '">Edit</button>';
            html += '<button class="ssc-button secondary ssc-delete-schema" data-id="' + schema.id + '">Delete</button>';
            html += '</div>';
            html += '</div>';
        });
        
        schemaList.html(html);
        
        // Add event listeners
        $('.ssc-edit-schema').on('click', function() {
            const schemaId = $(this).data('id');
            editSchema(schemaId);
        });
        
        $('.ssc-delete-schema').on('click', function() {
            const schemaId = $(this).data('id');
            if (confirm('Are you sure you want to delete this schema?')) {
                deleteSchema(schemaId);
            }
        });
    }
    
    // Create a new schema
    function createNewSchema() {
        const schemaType = $('#ssc-new-schema-type').val();
        if (!schemaType) {
            alert('Please select a schema type');
            return;
        }
        
        const templateJson = getSchemaTemplate(schemaType);
        
        // Create schema in WordPress
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssc_create_schema',
                nonce: sscData.nonce,
                name: schemaType,
                type: schemaType,
                json: templateJson
            },
            success: function(response) {
                if (response.success) {
                    const newSchema = response.data.schema;
                    savedSchemas.push(newSchema);
                    editSchema(newSchema.id);
                } else {
                    alert('Failed to create schema: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to create schema. Please try again.');
            }
        });
    }
    
    // Edit a schema
    function editSchema(schemaId) {
        const schema = savedSchemas.find(s => s.id === schemaId);
        
        if (!schema) {
            alert('Schema not found');
            return;
        }
        
        currentSchemaId = schemaId;
        
        // Show editor view, hide list view
        $('#ssc-schema-list-view').hide();
        $('#ssc-schema-editor-view').show();
        
        // Populate form
        $('#ssc-schema-name').val(schema.name);
        $('#ssc-schema-editor').val(schema.json);
        
        // Load page assignments
        if (schema.pages) {
            renderPageAssignments(schema.pages);
        } else {
            renderPageAssignments([]);
        }
    }
    
    // Update a schema
    function updateSchema() {
        if (!currentSchemaId) {
            alert('No schema selected');
            return false;
        }
        
        const schemaName = $('#ssc-schema-name').val() || 'Unnamed Schema';
        const schemaJson = $('#ssc-schema-editor').val();
        
        // Validate JSON
        try {
            JSON.parse(schemaJson);
        } catch (e) {
            alert('Invalid JSON: ' + e.message);
            return false;
        }
        
        // Find schema
        const schema = savedSchemas.find(s => s.id === currentSchemaId);
        
        if (!schema) {
            alert('Schema not found');
            return false;
        }
        
        // Update schema in WordPress
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssc_update_schema',
                nonce: sscData.nonce,
                schema_id: currentSchemaId,
                name: schemaName,
                json: schemaJson
            },
            success: function(response) {
                if (response.success) {
                    // Update local copy
                    schema.name = schemaName;
                    schema.json = schemaJson;
                    schema.last_updated = response.data.last_updated;
                    
                    $('#ssc-save-result').html('<div class="ssc-success-message">Schema updated successfully!</div>');
                    setTimeout(() => {
                        $('#ssc-save-result').empty();
                    }, 3000);
                    
                    // Update the list (even though it's hidden)
                    renderSchemaList();
                } else {
                    alert('Failed to update schema: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to update schema. Please try again.');
            }
        });
        
        return true;
    }
    
    // Delete a schema
    function deleteSchema(schemaId) {
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssc_delete_schema',
                nonce: sscData.nonce,
                schema_id: schemaId
            },
            success: function(response) {
                if (response.success) {
                    // Remove from local array
                    savedSchemas = savedSchemas.filter(s => s.id !== schemaId);
                    
                    // If this was the current schema, clear it
                    if (currentSchemaId === schemaId) {
                        currentSchemaId = null;
                        $('#ssc-schema-editor-view').hide();
                        $('#ssc-schema-list-view').show();
                    }
                    
                    // Update the list
                    renderSchemaList();
                } else {
                    alert('Failed to delete schema: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to delete schema. Please try again.');
            }
        });
    }
    
    // Render page assignments
    function renderPageAssignments(pageIds) {
        const container = $('#ssc-page-assignments');
        container.empty();
        
        if (!pageIds || pageIds.length === 0) {
            container.html('<div class="ssc-empty-assignments">No pages assigned yet.</div>');
            return;
        }
        
        pageIds.forEach(pageId => {
            // Find page details
            const page = sscData.pages.find(p => p.ID === parseInt(pageId));
            
            if (page) {
                const html = `<div class="ssc-page-assignment" data-id="${page.ID}">
                    ${page.post_title} (${page.post_type})
                    <span class="remove">×</span>
                </div>`;
                container.append(html);
            }
        });
        
        // Add event listener for removing assignments
        $('.ssc-page-assignment .remove').on('click', function() {
            const pageId = $(this).parent().data('id');
            removePageAssignment(pageId);
        });
    }
    
    // Add page assignment
    function addPageAssignment() {
        if (!currentSchemaId) {
            alert('No schema selected');
            return;
        }
        
        const pageId = $('#ssc-page-selector').val();
        if (!pageId) {
            alert('Please select a page');
            return;
        }
        
        // Find schema
        const schema = savedSchemas.find(s => s.id === currentSchemaId);
        
        if (!schema) {
            alert('Schema not found');
            return;
        }
        
        // Initialize pages array if needed
        if (!schema.pages) {
            schema.pages = [];
        }
        
        // Check if already assigned
        if (schema.pages.includes(pageId)) {
            alert('This page is already assigned to this schema');
            return;
        }
        
        // Add to pages
        schema.pages.push(pageId);
        
        // Update page assignments in WordPress
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssc_update_page_assignments',
                nonce: sscData.nonce,
                schema_id: currentSchemaId,
                pages: schema.pages
            },
            success: function(response) {
                if (response.success) {
                    renderPageAssignments(schema.pages);
                } else {
                    alert('Failed to update page assignments: ' + response.data.message);
                    // Revert the change
                    schema.pages.pop();
                }
            },
            error: function() {
                alert('Failed to update page assignments. Please try again.');
                // Revert the change
                schema.pages.pop();
            }
        });
    }
    
    // Remove page assignment
    function removePageAssignment(pageId) {
        if (!currentSchemaId) {
            alert('No schema selected');
            return;
        }
        
        // Find schema
        const schema = savedSchemas.find(s => s.id === currentSchemaId);
        
        if (!schema || !schema.pages) {
            return;
        }
        
        // Remove page
        schema.pages = schema.pages.filter(id => id !== pageId.toString());
        
        // Update page assignments in WordPress
        $.ajax({
            url: sscData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ssc_update_page_assignments',
                nonce: sscData.nonce,
                schema_id: currentSchemaId,
                pages: schema.pages
            },
            success: function(response) {
                if (response.success) {
                    renderPageAssignments(schema.pages);
                } else {
                    alert('Failed to update page assignments: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to update page assignments. Please try again.');
            }
        });
    }
    
    // Apply schema to pages
    function applySchemaToPages() {
        if (updateSchema()) {
            const schema = savedSchemas.find(s => s.id === currentSchemaId);
            
            if (!schema || !schema.pages || schema.pages.length === 0) {
                alert('Please select at least one page to apply this schema to');
                return;
            }
            
            // Show loading message
            $('#ssc-save-result').html('<div class="ssc-info-message">Applying schema to pages...</div>');
            
            $.ajax({
                url: sscData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ssc_apply_schema',
                    nonce: sscData.nonce,
                    schema_id: currentSchemaId
                },
                success: function(response) {
                    if (response.success) {
                        $('#ssc-save-result').html('<div class="ssc-success-message">Schema successfully applied to selected pages!</div>');
                        setTimeout(() => {
                            $('#ssc-save-result').empty();
                        }, 3000);
                    } else {
                        $('#ssc-save-result').html('<div class="ssc-error-message">Error: ' + response.data.message + '</div>');
                    }
                },
                error: function() {
                    $('#ssc-save-result').html('<div class="ssc-error-message">Server error while applying schema</div>');
                }
            });
        }
    }
    
    // Validate schema
    function validateSchema() {
        const schemaJson = $('#ssc-validator-input').val();
        
        if (!schemaJson.trim()) {
            $('#ssc-validation-result').html('<div class="ssc-error-message">Please enter schema JSON to validate</div>');
            return;
        }
        
        // Basic JSON validation
        try {
            const parsedSchema = JSON.parse(schemaJson);
            
            // Basic schema validation
            const errors = [];
            const warnings = [];
            
            // Required properties
            if (!parsedSchema['@context']) {
                errors.push('Missing required @context property');
            } else if (parsedSchema['@context'] !== 'https://schema.org' && 
                       !parsedSchema['@context'].includes('schema.org')) {
                warnings.push('@context should include "schema.org"');
            }
            
            if (!parsedSchema['@type']) {
                errors.push('Missing required @type property');
            }
            
            // Type-specific validation
            if (parsedSchema['@type']) {
                const schemaType = parsedSchema['@type'];
                
                switch (schemaType) {
                    case 'Article':
                        if (!parsedSchema.headline) {
                            errors.push('Missing required "headline" property for Article');
                        }
                        break;
                        
                    case 'Product':
                        if (!parsedSchema.name) {
                            errors.push('Missing required "name" property for Product');
                        }
                        break;
                        
                    case 'LocalBusiness':
                        if (!parsedSchema.name) {
                            errors.push('Missing required "name" property for LocalBusiness');
                        }
                        break;
                        
                    case 'FAQPage':
                        if (!parsedSchema.mainEntity || !Array.isArray(parsedSchema.mainEntity)) {
                            errors.push('FAQPage requires "mainEntity" array property');
                        }
                        break;
                        
                    case 'Event':
                        if (!parsedSchema.name) {
                            errors.push('Missing required "name" property for Event');
                        }
                        if (!parsedSchema.startDate) {
                            errors.push('Missing required "startDate" property for Event');
                        }
                        break;
                }
            }
            
            // Display validation results
            let resultHtml = '';
            
            if (errors.length === 0 && warnings.length === 0) {
                resultHtml = '<div class="ssc-success-message">Schema is valid!</div>';
            } else {
                resultHtml = '<div class="ssc-validation-details">';
                
                if (errors.length > 0) {
                    resultHtml += '<div class="ssc-error-message">';
                    resultHtml += '<strong>Schema validation failed:</strong>';
                    resultHtml += '<ul>';
                    
                    errors.forEach(error => {
                        resultHtml += '<li>' + error + '</li>';
                    });
                    
                    resultHtml += '</ul></div>';
                }
                
                if (warnings.length > 0) {
                    resultHtml += '<div class="ssc-warning-message">';
                    resultHtml += '<strong>Schema validation warnings:</strong>';
                    resultHtml += '<ul>';
                    
                    warnings.forEach(warning => {
                        resultHtml += '<li>' + warning + '</li>';
                    });
                    
                    resultHtml += '</ul></div>';
                }
                
                resultHtml += '</div>';
            }
            
            $('#ssc-validation-result').html(resultHtml);
            
        } catch (e) {
            $('#ssc-validation-result').html('<div class="ssc-error-message"><strong>Invalid JSON format:</strong> ' + e.message + '</div>');
        }
    }
    
    // Get schema template based on type
    function getSchemaTemplate(type) {
        return sscData.schemaTemplates[type] || '{"@context":"https://schema.org","@type":"' + type + '"}';
    }
    
    // Initialize the page
    function init() {
        // Load saved schemas
        loadSchemas();
        
        // Set up event handlers
        $('#ssc-create-schema').on('click', createNewSchema);
        $('#ssc-back-to-list').on('click', function() {
            currentSchemaId = null;
            $('#ssc-schema-editor-view').hide();
            $('#ssc-schema-list-view').show();
        });
        $('#ssc-update-schema').on('click', updateSchema);
        $('#ssc-apply-schema').on('click', applySchemaToPages);
        $('#ssc-add-page').on('click', addPageAssignment);
        $('#ssc-validate-schema-button').on('click', validateSchema);
        
        // Tab navigation
        $('.ssc-nav-item').on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            
            // Update active tab
            $('.ssc-nav-item').removeClass('active');
            $(this).addClass('active');
            
            // Show selected tab
            $('.ssc-tab').hide();
            $('#ssc-tab-' + tabId).show();
        });
    }
    
    // Initialize when document is ready
    init();
});