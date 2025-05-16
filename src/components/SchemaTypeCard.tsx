import React from 'react';
import { FilePlus } from 'lucide-react';

interface SchemaTypeCardProps {
  title: string;
  description: string;
  active: boolean;
  count: number;
}

const SchemaTypeCard: React.FC<SchemaTypeCardProps> = ({ 
  title, 
  description, 
  active, 
  count 
}) => {
  return (
    <div className="bg-white overflow-hidden shadow rounded-lg schema-card">
      <div className="px-4 py-5 sm:p-6">
        <div className="flex justify-between items-start">
          <div>
            <h3 className="text-lg leading-6 font-medium text-gray-900">
              {title}
            </h3>
            <p className="mt-1 text-sm text-gray-500">
              {description}
            </p>
          </div>
          <label className="toggle-switch">
            <input type="checkbox" checked={active} onChange={() => {}} />
            <span className="toggle-slider"></span>
          </label>
        </div>
        <div className="mt-5 flex items-center justify-between">
          <div className="text-sm text-gray-500">
            {active ? (
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Active
              </span>
            ) : (
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Inactive
              </span>
            )}
            {count > 0 && (
              <span className="ml-2">
                {count} page{count !== 1 ? 's' : ''}
              </span>
            )}
          </div>
          <button className="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
            <FilePlus className="mr-1 h-4 w-4" />
            Add
          </button>
        </div>
      </div>
    </div>
  );
};

export default SchemaTypeCard;