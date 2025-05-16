import React, { useState } from 'react';
import { Save, Copy } from 'lucide-react';
import SchemaTypeSelector from './SchemaTypeSelector';

const SchemaBuilder: React.FC = () => {
  const [schemaType, setSchemaType] = useState<string>('Article');
  const [jsonLd, setJsonLd] = useState<string>(`{
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
}`);

  const handleSchemaTypeChange = (type: string) => {
    setSchemaType(type);
    // Update JSON-LD template based on type
    const templates: { [key: string]: string } = {
      Article: `{
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
}`,
      Product: `{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Product name",
  "description": "Product description",
  "image": "https://example.com/product.jpg",
  "offers": {
    "@type": "Offer",
    "price": "",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock"
  }
}`,
      LocalBusiness: `{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Business name",
  "description": "Business description",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "",
    "addressLocality": "",
    "addressRegion": "",
    "postalCode": "",
    "addressCountry": ""
  }
}`
    };
    
    setJsonLd(templates[type] || templates.Article);
  };

  const copyToClipboard = () => {
    navigator.clipboard.writeText(jsonLd);
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold text-gray-900">Schema Builder</h2>
        <div className="flex space-x-2">
          <button
            onClick={copyToClipboard}
            className="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            <Copy className="mr-2 h-4 w-4" />
            Copy
          </button>
          <button className="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
            <Save className="mr-2 h-4 w-4" />
            Save Schema
          </button>
        </div>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 bg-gray-50 border-b border-gray-200">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-medium text-gray-900">Schema Type</h3>
            <SchemaTypeSelector value={schemaType} onChange={handleSchemaTypeChange} />
          </div>
        </div>
        
        <div className="p-4">
          <textarea
            value={jsonLd}
            onChange={(e) => setJsonLd(e.target.value)}
            rows={20}
            className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
            placeholder="Enter your JSON-LD schema here..."
          />
        </div>
      </div>
    </div>
  );
};

export default SchemaBuilder;