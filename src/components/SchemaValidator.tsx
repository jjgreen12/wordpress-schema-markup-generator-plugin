import React, { useState } from 'react';
import { Check, XCircle, AlertTriangle, RefreshCw, FileJson } from 'lucide-react';

interface ValidationResult {
  valid: boolean;
  errors: string[];
  warnings: string[];
}

const SchemaValidator: React.FC = () => {
  const [schema, setSchema] = useState<string>('');
  const [validating, setValidating] = useState<boolean>(false);
  const [validationResult, setValidationResult] = useState<ValidationResult | null>(null);

  const validateSchema = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!schema.trim()) {
      alert('Please enter a schema to validate');
      return;
    }

    setValidating(true);

    try {
      // Basic JSON validation
      let parsedSchema;
      try {
        parsedSchema = JSON.parse(schema);
      } catch (error) {
        setValidationResult({
          valid: false,
          errors: ['Invalid JSON format. Please check for syntax errors.'],
          warnings: []
        });
        setValidating(false);
        return;
      }

      // Perform schema validation
      const result: ValidationResult = {
        valid: true,
        errors: [],
        warnings: []
      };

      // Check for required fields based on schema type
      if (!parsedSchema['@context']) {
        result.errors.push('Missing required @context property');
        result.valid = false;
      } else if (parsedSchema['@context'] !== 'https://schema.org' && 
                !parsedSchema['@context'].includes('schema.org')) {
        result.warnings.push('@context should include "schema.org"');
      }

      if (!parsedSchema['@type']) {
        result.errors.push('Missing required @type property');
        result.valid = false;
      }

      // Type-specific validation
      if (parsedSchema['@type']) {
        const schemaType = parsedSchema['@type'];
        
        switch (schemaType) {
          case 'Article':
            if (!parsedSchema.headline) {
              result.errors.push('Missing required "headline" property for Article');
              result.valid = false;
            }
            if (!parsedSchema.author) {
              result.warnings.push('Article schema should include "author" property');
            }
            break;
            
          case 'Product':
            if (!parsedSchema.name) {
              result.errors.push('Missing required "name" property for Product');
              result.valid = false;
            }
            if (!parsedSchema.offers) {
              result.warnings.push('Product schema should include "offers" property');
            }
            break;
            
          case 'LocalBusiness':
            if (!parsedSchema.name) {
              result.errors.push('Missing required "name" property for LocalBusiness');
              result.valid = false;
            }
            if (!parsedSchema.address) {
              result.warnings.push('LocalBusiness schema should include "address" property');
            }
            break;
            
          case 'FAQPage':
            if (!parsedSchema.mainEntity || !Array.isArray(parsedSchema.mainEntity)) {
              result.errors.push('FAQPage requires "mainEntity" array property');
              result.valid = false;
            }
            break;
            
          case 'Event':
            if (!parsedSchema.name) {
              result.errors.push('Missing required "name" property for Event');
              result.valid = false;
            }
            if (!parsedSchema.startDate) {
              result.errors.push('Missing required "startDate" property for Event');
              result.valid = false;
            }
            break;
            
          case 'Person':
            if (!parsedSchema.name) {
              result.errors.push('Missing required "name" property for Person');
              result.valid = false;
            }
            break;
        }
      }

      // Simulate sending to Google Structured Data Testing Tool
      await new Promise(resolve => setTimeout(resolve, 800));

      setValidationResult(result);
    } catch (error) {
      console.error('Validation error:', error);
      setValidationResult({
        valid: false,
        errors: ['An unexpected error occurred during validation'],
        warnings: []
      });
    } finally {
      setValidating(false);
    }
  };

  const handleTestInGoogleTool = () => {
    try {
      // Create a form to submit to Google's Rich Results Test
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'https://search.google.com/test/rich-results';
      form.target = '_blank';
      
      // Add the code input
      const codeInput = document.createElement('input');
      codeInput.type = 'hidden';
      codeInput.name = 'code_snippet';
      codeInput.value = schema;
      form.appendChild(codeInput);
      
      // Submit the form
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    } catch (error) {
      console.error('Error opening Google Rich Results Test:', error);
      window.open('https://search.google.com/test/rich-results', '_blank');
    }
  };

  return (
    <div className="space-y-6 fade-in">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold text-gray-900">Schema Validator</h2>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 bg-gray-50 border-b border-gray-200">
          <div className="flex items-center">
            <FileJson className="h-5 w-5 text-blue-500 mr-2" />
            <h3 className="text-sm font-medium text-gray-900">JSON-LD Schema</h3>
          </div>
        </div>
        <div className="p-4">
          <form onSubmit={validateSchema}>
            <div className="space-y-4">
              <textarea
                id="schema"
                rows={12}
                className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                placeholder="Paste your JSON-LD schema here..."
                value={schema}
                onChange={(e) => setSchema(e.target.value)}
              />
              <div className="flex space-x-2">
                <button
                  type="submit"
                  disabled={validating}
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70 transition-colors"
                >
                  {validating ? (
                    <>
                      <RefreshCw className="animate-spin h-4 w-4 mr-2" />
                      Validating...
                    </>
                  ) : (
                    'Validate Schema'
                  )}
                </button>
                <button
                  type="button"
                  onClick={handleTestInGoogleTool}
                  disabled={!schema.trim()}
                  className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors"
                >
                  Test in Google Rich Results
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {validationResult && (
        <div className="bg-white shadow-sm rounded-lg overflow-hidden">
          <div className={`p-4 border-b ${
            validationResult.valid 
              ? 'border-green-200 bg-green-50' 
              : 'border-red-200 bg-red-50'
          }`}>
            <div className="flex items-center">
              {validationResult.valid ? (
                <Check className="h-5 w-5 text-green-500 mr-2" />
              ) : (
                <XCircle className="h-5 w-5 text-red-500 mr-2" />
              )}
              <h3 className={`text-lg font-medium ${
                validationResult.valid ? 'text-green-800' : 'text-red-800'
              }`}>
                {validationResult.valid 
                  ? 'Validation Successful' 
                  : 'Validation Failed'}
              </h3>
            </div>
          </div>
          <div className="p-4">
            {validationResult.errors.length > 0 && (
              <div className="mb-4">
                <h4 className="text-sm font-medium text-red-800 mb-2">Errors:</h4>
                <ul className="list-disc list-inside text-sm text-red-600 space-y-1">
                  {validationResult.errors.map((error: string, index: number) => (
                    <li key={index}>{error}</li>
                  ))}
                </ul>
              </div>
            )}
            {validationResult.warnings.length > 0 && (
              <div className="mb-4">
                <h4 className="text-sm font-medium text-amber-800 mb-2">Warnings:</h4>
                <ul className="list-disc list-inside text-sm text-amber-600 space-y-1">
                  {validationResult.warnings.map((warning: string, index: number) => (
                    <li key={index}>{warning}</li>
                  ))}
                </ul>
              </div>
            )}
            {validationResult.valid && validationResult.errors.length === 0 && validationResult.warnings.length === 0 && (
              <div className="text-sm text-green-600 flex items-center">
                <Check className="h-5 w-5 mr-2" />
                Schema is valid and ready to use!
              </div>
            )}
            {validationResult.valid && validationResult.warnings.length > 0 && (
              <div className="mt-4 bg-blue-50 text-blue-800 p-3 rounded-md text-sm flex items-start">
                <AlertTriangle className="h-5 w-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                <div>
                  Your schema is technically valid, but could be improved by addressing the warnings above. 
                  Following schema.org best practices can improve your rich results in search engines.
                </div>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default SchemaValidator;