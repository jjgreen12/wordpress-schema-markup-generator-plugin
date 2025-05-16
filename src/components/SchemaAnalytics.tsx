import React from 'react';
import { BarChart2, TrendingUp, TrendingDown, FileJson } from 'lucide-react';

const SchemaAnalytics: React.FC = () => {
  // Mock data for charts
  const performanceData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
      {
        name: 'Clicks',
        data: [120, 135, 170, 168, 190, 210]
      },
      {
        name: 'Impressions',
        data: [1200, 1350, 1600, 1700, 1900, 2100]
      }
    ]
  };
  
  const schemaDistribution = [
    { type: 'Article', count: 24, percentage: 38 },
    { type: 'Product', count: 18, percentage: 28 },
    { type: 'FAQ', count: 8, percentage: 12 },
    { type: 'LocalBusiness', count: 7, percentage: 11 },
    { type: 'Person', count: 4, percentage: 6 },
    { type: 'Other', count: 3, percentage: 5 },
  ];

  const topPerformingSchemas = [
    { type: 'Product', clicks: 125, impressions: 980, ctr: 12.8 },
    { type: 'Article', clicks: 98, impressions: 845, ctr: 11.6 },
    { type: 'FAQ', clicks: 76, impressions: 690, ctr: 11.0 },
    { type: 'LocalBusiness', clicks: 65, impressions: 520, ctr: 12.5 },
    { type: 'Recipe', clicks: 58, impressions: 470, ctr: 12.3 },
  ];

  return (
    <div className="space-y-6 fade-in">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-semibold text-gray-900">Schema Analytics</h1>
        <div className="flex space-x-2">
          <select className="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option>Last 30 days</option>
            <option>Last 90 days</option>
            <option>Last 6 months</option>
            <option>Last year</option>
          </select>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white shadow-sm rounded-lg overflow-hidden p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 rounded-md p-2 bg-blue-100">
              <FileJson className="h-5 w-5 text-blue-600" />
            </div>
            <div className="ml-5 w-0 flex-1">
              <dt className="text-sm font-medium text-gray-500 truncate">
                Total Schemas
              </dt>
              <dd className="flex items-baseline">
                <div className="text-2xl font-semibold text-gray-900">
                  64
                </div>
                <div className="ml-2 flex items-center text-sm text-green-600">
                  <TrendingUp className="self-center flex-shrink-0 h-4 w-4 text-green-500" />
                  <span className="ml-1">+12% from last month</span>
                </div>
              </dd>
            </div>
          </div>
        </div>
        
        <div className="bg-white shadow-sm rounded-lg overflow-hidden p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 rounded-md p-2 bg-green-100">
              <TrendingUp className="h-5 w-5 text-green-600" />
            </div>
            <div className="ml-5 w-0 flex-1">
              <dt className="text-sm font-medium text-gray-500 truncate">
                Clicks (30 days)
              </dt>
              <dd className="flex items-baseline">
                <div className="text-2xl font-semibold text-gray-900">
                  432
                </div>
                <div className="ml-2 flex items-center text-sm text-green-600">
                  <TrendingUp className="self-center flex-shrink-0 h-4 w-4 text-green-500" />
                  <span className="ml-1">+18% from last period</span>
                </div>
              </dd>
            </div>
          </div>
        </div>
        
        <div className="bg-white shadow-sm rounded-lg overflow-hidden p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 rounded-md p-2 bg-indigo-100">
              <BarChart2 className="h-5 w-5 text-indigo-600" />
            </div>
            <div className="ml-5 w-0 flex-1">
              <dt className="text-sm font-medium text-gray-500 truncate">
                CTR
              </dt>
              <dd className="flex items-baseline">
                <div className="text-2xl font-semibold text-gray-900">
                  8.7%
                </div>
                <div className="ml-2 flex items-center text-sm text-red-600">
                  <TrendingDown className="self-center flex-shrink-0 h-4 w-4 text-red-500" />
                  <span className="ml-1">-0.5% from last period</span>
                </div>
              </dd>
            </div>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div className="lg:col-span-8 bg-white shadow-sm rounded-lg overflow-hidden">
          <div className="p-4 border-b border-gray-200">
            <h2 className="text-lg font-medium text-gray-900">Performance Over Time</h2>
          </div>
          <div className="p-4 h-64 flex items-center justify-center">
            {/* We'd use a real chart library here */}
            <div className="w-full h-full bg-gray-50 rounded flex items-center justify-center">
              <div className="text-center">
                <BarChart2 className="h-12 w-12 text-gray-400 mx-auto" />
                <p className="mt-2 text-sm text-gray-500">Performance chart would render here</p>
                <p className="text-xs text-gray-400">Using data from Search Console</p>
              </div>
            </div>
          </div>
        </div>
        
        <div className="lg:col-span-4 bg-white shadow-sm rounded-lg overflow-hidden">
          <div className="p-4 border-b border-gray-200">
            <h2 className="text-lg font-medium text-gray-900">Schema Distribution</h2>
          </div>
          <div className="p-4">
            <div className="space-y-4">
              {schemaDistribution.map((item, index) => (
                <div key={index}>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">{item.type}</span>
                    <span className="text-gray-900 font-medium">{item.count} ({item.percentage}%)</span>
                  </div>
                  <div className="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                    <div 
                      className="bg-indigo-600 h-2.5 rounded-full" 
                      style={{ width: `${item.percentage}%` }}
                    ></div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 border-b border-gray-200">
          <h2 className="text-lg font-medium text-gray-900">Top Performing Schemas</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Schema Type
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Clicks
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Impressions
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  CTR
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Trend
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {topPerformingSchemas.map((schema, index) => (
                <tr key={index}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {schema.type}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {schema.clicks}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {schema.impressions}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {schema.ctr}%
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <TrendingUp className="h-5 w-5 text-green-500" />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className="bg-white shadow-sm rounded-lg overflow-hidden">
        <div className="p-4 border-b border-gray-200">
          <h2 className="text-lg font-medium text-gray-900">Recommendations</h2>
        </div>
        <div className="p-6">
          <ul className="space-y-4">
            <li className="flex items-start">
              <div className="flex-shrink-0">
                <div className="h-6 w-6 rounded-full bg-amber-100 flex items-center justify-center">
                  <AlertTriangle className="h-4 w-4 text-amber-600" />
                </div>
              </div>
              <div className="ml-3">
                <p className="text-sm font-medium text-gray-900">Add FAQ schema to your support pages</p>
                <p className="mt-1 text-sm text-gray-500">
                  FAQ schema can improve CTR by up to 30% for support and documentation pages.
                </p>
              </div>
            </li>
            <li className="flex items-start">
              <div className="flex-shrink-0">
                <div className="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                  <TrendingUp className="h-4 w-4 text-green-600" />
                </div>
              </div>
              <div className="ml-3">
                <p className="text-sm font-medium text-gray-900">Add more product details to Product schema</p>
                <p className="mt-1 text-sm text-gray-500">
                  Products with complete schema attributes perform 15% better in search results.
                </p>
              </div>
            </li>
            <li className="flex items-start">
              <div className="flex-shrink-0">
                <div className="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                  <FileJson className="h-4 w-4 text-blue-600" />
                </div>
              </div>
              <div className="ml-3">
                <p className="text-sm font-medium text-gray-900">Fix 2 missing required fields in Article schema</p>
                <p className="mt-1 text-sm text-gray-500">
                  Some Article schemas are missing the "dateModified" field which is required.
                </p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default SchemaAnalytics;