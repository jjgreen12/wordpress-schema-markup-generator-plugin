import React from 'react';
import type { NavigationItem } from '../App';

interface SidebarProps {
  navigationItems: NavigationItem[];
  activeTab: string;
  setActiveTab: (tab: string) => void;
}

const Sidebar: React.FC<SidebarProps> = ({ 
  navigationItems, 
  activeTab, 
  setActiveTab 
}) => {
  return (
    <div className="w-64 bg-white border-r border-gray-200 overflow-y-auto">
      <div className="p-6">
        <h2 className="text-xs font-semibold text-gray-500 uppercase tracking-wider">
          Schema Management
        </h2>
        <nav className="mt-5 space-y-1">
          {navigationItems.map((item) => (
            <button
              key={item.id}
              onClick={() => setActiveTab(item.id)}
              className={`group flex items-center px-3 py-2 text-sm font-medium rounded-md w-full ${
                activeTab === item.id
                  ? 'bg-indigo-50 text-indigo-600'
                  : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'
              }`}
            >
              <span className={`mr-3 ${
                activeTab === item.id 
                  ? 'text-indigo-600' 
                  : 'text-gray-500 group-hover:text-gray-600'
              }`}>
                {item.icon}
              </span>
              {item.label}
            </button>
          ))}
        </nav>
      </div>
      <div className="p-4 border-t border-gray-200">
        <div className="flex flex-col space-y-2">
          <div className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
            Quick Actions
          </div>
          <button className="text-sm text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
            Test Schema
          </button>
          <button className="text-sm text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
            Clear Cache
          </button>
          <button className="text-sm text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">
            Get Support
          </button>
        </div>
      </div>
    </div>
  );
};

export default Sidebar;