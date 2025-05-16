import React from 'react';
import { FileJson, AlertTriangle, CheckCircle, BookOpen, HelpCircle } from 'lucide-react';
import SchemaTypeCard from './SchemaTypeCard';
import StatsCard from './StatsCard';

const Dashboard: React.FC = () => {
  const schemaTypes = [
    { 
      id: 'article', 
      title: 'Article',
      description: 'News, blog posts, and articles',
      active: true,
      count: 24
    },
    { 
      id: 'product', 
      title: 'Product',
      description: 'E-commerce and product pages',
      active: true,
      count: 8
    },
    { 
      id: 'local', 
      title: 'Local Business',
      description: 'Business location and details',
      active: true,
      count: 1
    },
    { 
      id: 'faq', 
      title: 'FAQ',
      description: 'Frequently asked questions',
      active: false,
      count: 0
    },
    { 
      id: 'event', 
      title: 'Event',
      description: 'Events, webinars, and meetups',
      active: false,
      count: 0
    },
    { 
      id: 'person', 
      title: 'Person',
      description: 'Author and team member details',
      active: true,
      count: 5
    },
  ];

  const stats = [
    { 
      title: 'Total Schema', 
      value: 38, 
      icon: <FileJson size={20} className="text-blue-500" />,
      change: '+5 this week',
      positive: true
    },
    { 
      title: 'Validation Issues', 
      value: 2, 
      icon: <AlertTriangle size={20} className="text-amber-500" />,
      change: '-3 from last week',
      positive: true
    },
    { 
      title: 'Schema Coverage', 
      value: '82%', 
      icon: <CheckCircle size={20} className="text-green-500" />,
      change: '+8% from last month',
      positive: true
    }
  ];

  return (
    <div className="space-y-6 fade-in">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <button className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Add New Schema
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {stats.map((stat, index) => (
          <StatsCard 
            key={index}
            title={stat.title}
            value={stat.value}
            icon={stat.icon}
            change={stat.change}
            positive={stat.positive}
          />
        ))}
      </div>

      <div>
        <h2 className="text-lg font-medium text-gray-900 mb-4">Schema Types</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {schemaTypes.map((schemaType) => (
            <SchemaTypeCard 
              key={schemaType.id}
              title={schemaType.title}
              description={schemaType.description}
              active={schemaType.active}
              count={schemaType.count}
            />
          ))}
        </div>
      </div>

      <div className="bg-white rounded-lg shadow p-6 mt-6">
        <div className="flex items-start space-x-4">
          <div className="flex-shrink-0 bg-blue-100 rounded-md p-2">
            <HelpCircle className="h-6 w-6 text-blue-600" />
          </div>
          <div>
            <h3 className="text-lg font-medium text-gray-900">Getting Started</h3>
            <p className="mt-1 text-sm text-gray-500">
              New to SchemaPro? Learn how to set up your first schema and optimize your SEO.
            </p>
            <div className="mt-4 flex space-x-4">
              <button className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <BookOpen className="mr-2 h-4 w-4" />
                View Tutorial
              </button>
              <button className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Set Up Wizard
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;