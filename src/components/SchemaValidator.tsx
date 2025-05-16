import React, { useState } from 'react';
import { Check, XCircle, AlertTriangle, RefreshCw } from 'lucide-react';

const SchemaValidator: React.FC = () => {
  const [schema, setSchema] = useState<string>('');
  const [validating, setValidating] = useState<boolean>(false);
  const [validationResult, setValidationResult] = useState<any>(null);

  const validateSchema = (e: React.FormEvent) => {
    e.preventDefault();
    setValidating(true);

    try {
      const parsedSchema = JSON.parse(schema);
      const result = {
        valid: true,
        errors: [],
        warnings: []
      };

      // Basic validation
      if (!parsedSchema['@context'] || !parsedSchema['@type']) {
        result.valid = false;
        result.errors.push('Missing required @context or @type properties');
      }

      setValidationResult(result);
    } catch (error) {
      setValidationResult({
        valid: false,
        errors: ['Invalid JSON format'],
        warnings: []
      });
    }

    setValidating(false);
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold text-gray-900">Schema Validator</h2>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4">
          <form onSubmit={validateSchema}>
            <div className="space-y-4">
              <div>
                <label htmlFor="schema" className="block text-sm font-medium text-gray-700">
                  JSON-LD Schema
                </label>
                <textarea
                  id="schema"
                  rows={10}
                  className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                  placeholder="Paste your JSON-LD schema here..."
                  value={schema}
                  onChange={(e) => setSchema(e.target.value)}
                />
              </div>
              <button
                type="submit"
                disabled={validating}
                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70"
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
            </div>
          </form>
        </div>
      </div>

      {validationResult && (
        <div className="bg-white shadow-sm rounded-lg overflow-hidden">
          <div className="p-4 border-b border-gray-200">
            <div className="flex items-center">
              {validationResult.valid ? (
                <Check className="h-5 w-5 text-green-500 mr-2" />
              ) : (
                <XCircle className="h-5 w-5 text-red-500 mr-2" />
              )}
              <h3 className="text-lg font-medium text-gray-900">
                Validation Results
              </h3>
            </div>
          </div>
          <div className="p-4">
            {validationResult.errors.length > 0 && (
              <div className="mb-4">
                <h4 className="text-sm font-medium text-red-800 mb-2">Errors:</h4>
                <ul className="list-disc list-inside text-sm text-red-600">
                  {validationResult.errors.map((error: string, index: number) => (
                    <li key={index}>{error}</li>
                  ))}
                </ul>
              </div>
            )}
            {validationResult.warnings.length > 0 && (
              <div>
                <h4 className="text-sm font-medium text-amber-800 mb-2">Warnings:</h4>
                <ul className="list-disc list-inside text-sm text-amber-600">
                  {validationResult.warnings.map((warning: string, index: number) => (
                    <li key={index}>{warning}</li>
                  ))}
                </ul>
              </div>
            )}
            {validationResult.valid && validationResult.errors.length === 0 && (
              <div className="text-sm text-green-600">
                Schema is valid and ready to use!
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default SchemaValidator;