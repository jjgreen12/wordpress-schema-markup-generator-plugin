import React, { useState } from 'react';
import { Save, Cog, AlertTriangle } from 'lucide-react';

const SchemaSettings: React.FC = () => {
  const [autoGenerateSchema, setAutoGenerateSchema] = useState<boolean>(true);
  const [includeOptionalFields, setIncludeOptionalFields] = useState<boolean>(true);
  const [showInPreview, setShowInPreview] = useState<boolean>(false);
  const [contentTypes, setContentTypes] = useState<string[]>(['post', 'page', 'product']);

  const handleContentTypeToggle = (type: string) => {
    if (contentTypes.includes(type)) {
      setContentTypes(contentTypes.filter(t => t !== type));
    } else {
      setContentTypes([...contentTypes, type]);
    }
  };

  return (
    <div className="space-y-6 fade-in">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-semibold text-gray-900">Schema Settings</h1>
        <button className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <Save className="mr-2 h-4 w-4" />
          Save Settings
        </button>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 bg-indigo-50 border-b border-indigo-100">
          <div className="flex items-center">
            <Cog className="h-5 w-5 text-indigo-600 mr-2" />
            <h2 className="text-lg font-medium text-gray-900">General Settings</h2>
          </div>
        </div>
        
        <div className="p-6 space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h3 className="text-sm font-medium text-gray-900">Auto-generate Schema</h3>
              <p className="text-sm text-gray-500">
                Automatically generate schema for eligible content types
              </p>
            </div>
            <label className="toggle-switch">
              <input 
                type="checkbox" 
                checked={autoGenerateSchema} 
                onChange={() => setAutoGenerateSchema(!autoGenerateSchema)} 
              />
              <span className="toggle-slider"></span>
            </label>
          </div>
          
          <div className="flex items-center justify-between">
            <div>
              <h3 className="text-sm font-medium text-gray-900">Include Optional Fields</h3>
              <p className="text-sm text-gray-500">
                Include recommended and optional schema properties when available
              </p>
            </div>
            <label className="toggle-switch">
              <input 
                type="checkbox" 
                checked={includeOptionalFields} 
                onChange={() => setIncludeOptionalFields(!includeOptionalFields)} 
              />
              <span className="toggle-slider"></span>
            </label>
          </div>
          
          <div className="flex items-center justify-between">
            <div>
              <h3 className="text-sm font-medium text-gray-900">Show in Preview</h3>
              <p className="text-sm text-gray-500">
                Show schema markup in page preview (for debugging purposes)
              </p>
            </div>
            <label className="toggle-switch">
              <input 
                type="checkbox" 
                checked={showInPreview} 
                onChange={() => setShowInPreview(!showInPreview)} 
              />
              <span className="toggle-slider"></span>
            </label>
          </div>
        </div>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 bg-indigo-50 border-b border-indigo-100">
          <h2 className="text-lg font-medium text-gray-900">Content Types</h2>
          <p className="text-sm text-gray-500">
            Enable schema generation for these content types
          </p>
        </div>
        
        <div className="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          {['post', 'page', 'product', 'category', 'tag', 'custom_type_1', 'custom_type_2'].map((type) => (
            <div key={type} className="flex items-center">
              <input
                id={`content-type-${type}`}
                type="checkbox"
                checked={contentTypes.includes(type)}
                onChange={() => handleContentTypeToggle(type)}
                className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label htmlFor={`content-type-${type}`} className="ml-2 block text-sm text-gray-900 capitalize">
                {type.replace('_', ' ')}
              </label>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 bg-indigo-50 border-b border-indigo-100">
          <h2 className="text-lg font-medium text-gray-900">Advanced Settings</h2>
        </div>
        
        <div className="p-6 space-y-6">
          <div>
            <label htmlFor="schema-position" className="block text-sm font-medium text-gray-700">
              Schema Position
            </label>
            <select
              id="schema-position"
              className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
              defaultValue="head"
            >
              <option value="head">Head (Recommended)</option>
              <option value="body">Body</option>
              <option value="footer">Footer</option>
            </select>
          </div>
          
          <div>
            <label htmlFor="schema-format" className="block text-sm font-medium text-gray-700">
              Schema Format
            </label>
            <select
              id="schema-format"
              className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
              defaultValue="json-ld"
            >
              <option value="json-ld">JSON-LD (Recommended)</option>
              <option value="microdata">Microdata</option>
              <option value="rdfa">RDFa</option>
            </select>
          </div>
          
          <div>
            <div className="flex items-start">
              <div className="flex-shrink-0">
                <AlertTriangle className="h-5 w-5 text-amber-500" />
              </div>
              <div className="ml-3">
                <h3 className="text-sm font-medium text-gray-900">Warning: Advanced Settings</h3>
                <div className="mt-2 text-sm text-gray-500">
                  <p>
                    Changing these settings may affect how search engines interpret your schema. 
                    JSON-LD in the head section is recommended by Google and other search engines.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SchemaSettings;