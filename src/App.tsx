import React, { useState } from 'react';
import { Code, FileJson } from 'lucide-react';
import Header from './components/Header';
import SchemaBuilder from './components/SchemaBuilder';
import SchemaValidator from './components/SchemaValidator';
import './App.css';

export type NavigationItem = {
  id: string;
  label: string;
  icon: React.ReactNode;
};

function App() {
  const [activeTab, setActiveTab] = useState<string>('builder');

  const navigationItems: NavigationItem[] = [
    { id: 'builder', label: 'Schema Builder', icon: <FileJson size={20} /> },
    { id: 'validator', label: 'Validator', icon: <Code size={20} /> },
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'builder':
        return <SchemaBuilder />;
      case 'validator':
        return <SchemaValidator />;
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
    </div>
  );
}

export default App;