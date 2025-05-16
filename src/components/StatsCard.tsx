import React from 'react';
import { ArrowUpRight, ArrowDownRight } from 'lucide-react';

interface StatsCardProps {
  title: string;
  value: string | number;
  icon: React.ReactNode;
  change: string;
  positive: boolean;
}

const StatsCard: React.FC<StatsCardProps> = ({ title, value, icon, change, positive }) => {
  return (
    <div className="bg-white overflow-hidden shadow rounded-lg">
      <div className="px-4 py-5 sm:p-6">
        <div className="flex items-center">
          <div className="flex-shrink-0 rounded-md p-2 bg-gray-50">
            {icon}
          </div>
          <div className="ml-5 w-0 flex-1">
            <dt className="text-sm font-medium text-gray-500 truncate">
              {title}
            </dt>
            <dd className="flex items-baseline">
              <div className="text-2xl font-semibold text-gray-900">
                {value}
              </div>
              <div className={`ml-2 flex items-center text-sm ${
                positive ? 'text-green-600' : 'text-red-600'
              }`}>
                {positive ? (
                  <ArrowUpRight className="self-center flex-shrink-0 h-4 w-4 text-green-500" />
                ) : (
                  <ArrowDownRight className="self-center flex-shrink-0 h-4 w-4 text-red-500" />
                )}
                <span className="ml-1">{change}</span>
              </div>
            </dd>
          </div>
        </div>
      </div>
    </div>
  );
};

export default StatsCard;