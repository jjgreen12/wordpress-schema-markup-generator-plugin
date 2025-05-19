import React, { useState, useEffect } from 'react';
import { Code, FileJson, Settings } from 'lucide-react';
import Header from './components/Header';
import SchemaBuilder from './components/SchemaBuilder';
import SchemaValidator from './components/SchemaValidator';
import SchemaSettings from './components/SchemaSettings';
import './App.css';

export type NavigationItem = {
  id: string;
  label: string;
  icon: React.ReactNode;
};

function App() {
  // Use localStorage to persist the active tab
  const [activeTab, setActiveTab] = useState<string>(() => {
    const savedTab = localStorage.getItem('sscActiveTab');
    return savedTab || 'builder';
  });

  // Update localStorage when tab changes
  useEffect(() => {
    localStorage.setItem('sscActiveTab', activeTab);
  }, [activeTab]);

  const navigationItems: NavigationItem[] = [
    { id: 'builder', label: 'Schema Builder', icon: <FileJson size={20} /> },
    { id: 'validator', label: 'Validator', icon: <Code size={20} /> },
    { id: 'settings', label: 'Settings', icon: <Settings size={20} /> },
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'builder':
        return <SchemaBuilder />;
      case 'validator':
        return <SchemaValidator />;
      case 'settings':
        return <SchemaSettings />;
      default:
        return <SchemaBuilder />;
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header navigationItems={navigationItems} activeTab={activeTab} setActiveTab={setActiveTab} />
      <main className="container mx-auto px-4 py-6">
        {renderContent()}
      </main>
      <div className="bg-white py-4 border-t border-gray-200 mt-10">
        <div className="container mx-auto px-4 text-center text-sm text-gray-500">
          Schema Stunt Cock v1.0.1 - Advanced Schema Markup for WordPress
        </div>
      </div>
    </div>
  );
}

export default App;