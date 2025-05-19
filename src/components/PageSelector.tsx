import React, { useState } from 'react';
import { Search } from 'lucide-react';

interface WordPressPage {
  ID: number;
  post_title: string;
  post_type: string;
}

interface PageSelectorProps {
  pages: WordPressPage[];
  selectedPages: number[];
  onPageSelect: (pageId: number, isSelected: boolean) => void;
}

const PageSelector: React.FC<PageSelectorProps> = ({ 
  pages, 
  selectedPages, 
  onPageSelect 
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterType, setFilterType] = useState<string>('all');
  
  // Get unique post types
  const postTypes = ['all', ...Array.from(new Set(pages.map(page => page.post_type)))];
  
  // Filter pages based on search term and post type
  const filteredPages = pages.filter(page => {
    const matchesSearch = page.post_title.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesType = filterType === 'all' || page.post_type === filterType;
    return matchesSearch && matchesType;
  });

  return (
    <div className="space-y-4">
      <div className="flex space-x-2">
        <div className="relative flex-grow">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Search pages..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <select
          className="block w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          value={filterType}
          onChange={(e) => setFilterType(e.target.value)}
        >
          {postTypes.map((type) => (
            <option key={type} value={type}>
              {type === 'all' ? 'All types' : type}
            </option>
          ))}
        </select>
      </div>

      {filteredPages.length === 0 ? (
        <div className="text-center py-4 text-sm text-gray-500">
          No pages found matching your search.
        </div>
      ) : (
        <div className="space-y-2">
          {filteredPages.map((page) => (
            <div key={page.ID} className="flex items-center space-x-2 p-2 rounded hover:bg-gray-50">
              <input
                type="checkbox"
                id={`page-${page.ID}`}
                checked={selectedPages.includes(page.ID)}
                onChange={(e) => onPageSelect(page.ID, e.target.checked)}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor={`page-${page.ID}`} className="flex-grow text-sm text-gray-700 cursor-pointer">
                <span>{page.post_title}</span>
                <span className="text-xs text-gray-500 ml-2">({page.post_type})</span>
              </label>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default PageSelector;