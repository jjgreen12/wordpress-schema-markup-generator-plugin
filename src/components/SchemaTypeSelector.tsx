import React from 'react';
import { ChevronDown } from 'lucide-react';

interface SchemaTypeSelectorProps {
  value: string;
  onChange: (value: string) => void;
}

const SchemaTypeSelector: React.FC<SchemaTypeSelectorProps> = ({ value, onChange }) => {
  const schemaTypes = [
    'Article',
    'Product',
    'LocalBusiness',
    'FAQ',
    'Event',
    'Person',
    'Organization'
  ];

  return (
    <div className="relative inline-block">
      <select
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="appearance-none block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
      >
        {schemaTypes.map((type) => (
          <option key={type} value={type}>
            {type}
          </option>
        ))}
      </select>
      <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
        <ChevronDown className="h-4 w-4" />
      </div>
    </div>
  );
};

export default SchemaTypeSelector;