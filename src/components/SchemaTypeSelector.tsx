import React, { useState } from 'react';
import { ChevronDown, Search } from 'lucide-react';

interface SchemaTypeSelectorProps {
  value: string;
  onChange: (value: string) => void;
}

const SchemaTypeSelector: React.FC<SchemaTypeSelectorProps> = ({ value, onChange }) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [showDropdown, setShowDropdown] = useState(false);

  // Comprehensive schema types organized by category
  const schemaCategories = [
    {
      name: 'Common Types',
      types: [
        'Article',
        'BlogPosting',
        'Product',
        'LocalBusiness',
        'Organization',
        'Person',
        'WebPage',
        'Event',
        'Recipe',
        'FAQPage',
        'Review',
        'HowTo'
      ]
    },
    {
      name: 'Creative Works',
      types: [
        'Book',
        'Movie',
        'MusicRecording',
        'Painting',
        'Photograph',
        'SoftwareApplication',
        'TVSeries',
        'VideoGame',
        'WebSite'
      ]
    },
    {
      name: 'Organizations',
      types: [
        'Corporation',
        'EducationalOrganization',
        'GovernmentOrganization',
        'MedicalOrganization',
        'NGO',
        'SportsOrganization'
      ]
    },
    {
      name: 'Local Businesses',
      types: [
        'AnimalShelter',
        'AutomotiveBusiness',
        'ChildCare',
        'DryCleaningOrLaundry',
        'EmergencyService',
        'EmploymentAgency',
        'EntertainmentBusiness',
        'FinancialService',
        'FoodEstablishment',
        'GovernmentOffice',
        'HealthAndBeautyBusiness',
        'HomeAndConstructionBusiness',
        'InternetCafe',
        'LegalService',
        'Library',
        'LodgingBusiness',
        'MedicalBusiness',
        'ProfessionalService',
        'RadioStation',
        'RealEstateAgent',
        'RecyclingCenter',
        'SelfStorage',
        'ShoppingCenter',
        'SportsActivityLocation',
        'Store',
        'TelevisionStation',
        'TouristInformationCenter',
        'TravelAgency'
      ]
    },
    {
      name: 'Events',
      types: [
        'BusinessEvent',
        'ChildrensEvent',
        'ComedyEvent',
        'CourseInstance',
        'DanceEvent',
        'DeliveryEvent',
        'EducationEvent',
        'ExhibitionEvent',
        'Festival',
        'FoodEvent',
        'LiteraryEvent',
        'MusicEvent',
        'PublicationEvent',
        'SaleEvent',
        'ScreeningEvent',
        'SocialEvent',
        'SportsEvent',
        'TheaterEvent',
        'VisualArtsEvent'
      ]
    },
    {
      name: 'Products',
      types: [
        'IndividualProduct',
        'ProductCollection',
        'ProductGroup',
        'ProductModel',
        'SomeProducts',
        'Vehicle'
      ]
    },
    {
      name: 'Medical',
      types: [
        'AnatomicalStructure',
        'AnatomicalSystem',
        'Diet',
        'Drug',
        'ExercisePlan',
        'Hospital',
        'MedicalCondition',
        'MedicalProcedure',
        'MedicalTest',
        'Pharmacy'
      ]
    },
    {
      name: 'Places',
      types: [
        'Accommodation',
        'AdministrativeArea',
        'CivicStructure',
        'Landform',
        'LandmarksOrHistoricalBuildings',
        'Place',
        'Residence',
        'TouristAttraction',
        'TouristDestination'
      ]
    }
  ];

  // Flatten all types for search
  const allTypes = schemaCategories.flatMap(category => category.types);

  // Filter types based on search term
  const filteredTypes = searchTerm
    ? allTypes.filter(type => 
        type.toLowerCase().includes(searchTerm.toLowerCase())
      )
    : [];

  return (
    <div className="relative inline-block w-64">
      <div 
        className="flex items-center border border-gray-300 rounded-md shadow-sm focus-within:ring-blue-500 focus-within:border-blue-500 bg-white"
        onClick={() => setShowDropdown(!showDropdown)}
      >
        <div className="flex-grow pl-3 py-2 text-sm cursor-pointer">
          {value}
        </div>
        <div className="px-2 py-2 text-gray-500">
          <ChevronDown className="h-4 w-4" />
        </div>
      </div>
      
      {showDropdown && (
        <div className="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200">
          <div className="p-2 border-b border-gray-200">
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Search className="h-4 w-4 text-gray-400" />
              </div>
              <input
                type="text"
                className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Search schema types..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                onClick={(e) => e.stopPropagation()}
              />
            </div>
          </div>
          
          <div className="max-h-60 overflow-y-auto p-1">
            {searchTerm ? (
              // Display search results
              filteredTypes.length > 0 ? (
                filteredTypes.map((type) => (
                  <div
                    key={type}
                    className="px-4 py-2 text-sm hover:bg-blue-50 cursor-pointer rounded"
                    onClick={() => {
                      onChange(type);
                      setShowDropdown(false);
                      setSearchTerm('');
                    }}
                  >
                    {type}
                  </div>
                ))
              ) : (
                <div className="px-4 py-2 text-sm text-gray-500">No matching types found</div>
              )
            ) : (
              // Display categorized list
              schemaCategories.map((category) => (
                <div key={category.name} className="mb-2">
                  <div className="px-3 py-1 text-xs font-semibold text-gray-500 bg-gray-50">
                    {category.name}
                  </div>
                  {category.types.map((type) => (
                    <div
                      key={type}
                      className={`px-4 py-2 text-sm hover:bg-blue-50 cursor-pointer rounded ${
                        type === value ? 'bg-blue-50 text-blue-700' : ''
                      }`}
                      onClick={() => {
                        onChange(type);
                        setShowDropdown(false);
                      }}
                    >
                      {type}
                    </div>
                  ))}
                </div>
              ))
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default SchemaTypeSelector;