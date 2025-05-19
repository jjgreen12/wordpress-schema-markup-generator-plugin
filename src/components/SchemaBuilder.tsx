import React, { useState, useEffect } from 'react';
import { Save, Copy, FileCheck, RefreshCw } from 'lucide-react';
import SchemaTypeSelector from './SchemaTypeSelector';
import PageSelector from './PageSelector';

// Define types for WordPress pages
interface WordPressPage {
  ID: number;
  post_title: string;
  post_type: string;
}

interface WordPressData {
  ajaxUrl: string;
  nonce: string;
  pages: WordPressPage[];
}

// Add global declaration for WordPress data
declare global {
  interface Window {
    sscData: WordPressData;
  }
}

const SchemaBuilder: React.FC = () => {
  const [schemaType, setSchemaType] = useState<string>('Article');
  const [jsonLd, setJsonLd] = useState<string>(`{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Your article title",
  "description": "Article description",
  "image": "https://example.com/image.jpg",
  "datePublished": "",
  "dateModified": "",
  "author": {
    "@type": "Person",
    "name": ""
  }
}`);
  const [selectedPages, setSelectedPages] = useState<number[]>([]);
  const [isSaving, setIsSaving] = useState<boolean>(false);
  const [saveSuccess, setSaveSuccess] = useState<boolean | null>(null);
  const [copySuccess, setCopySuccess] = useState<boolean | null>(null);
  const [pages, setPages] = useState<WordPressPage[]>([]);

  // Load WordPress data
  useEffect(() => {
    if (window.sscData && window.sscData.pages) {
      setPages(window.sscData.pages);
    }
  }, []);

  const handleSchemaTypeChange = (type: string) => {
    setSchemaType(type);
    // Update JSON-LD template based on type
    const templates: { [key: string]: string } = {
      // Common Types
      Article: `{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Your article title",
  "description": "Article description",
  "image": "https://example.com/image.jpg",
  "datePublished": "",
  "dateModified": "",
  "author": {
    "@type": "Person",
    "name": ""
  }
}`,
      BlogPosting: `{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Your blog post title",
  "description": "Blog post description",
  "image": "https://example.com/image.jpg",
  "datePublished": "",
  "dateModified": "",
  "author": {
    "@type": "Person",
    "name": ""
  },
  "publisher": {
    "@type": "Organization",
    "name": "",
    "logo": {
      "@type": "ImageObject",
      "url": ""
    }
  }
}`,
      Product: `{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Product name",
  "description": "Product description",
  "image": "https://example.com/product.jpg",
  "brand": {
    "@type": "Brand",
    "name": ""
  },
  "offers": {
    "@type": "Offer",
    "price": "",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock",
    "url": ""
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "",
    "reviewCount": ""
  }
}`,
      LocalBusiness: `{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Business name",
  "description": "Business description",
  "image": "",
  "telephone": "",
  "email": "",
  "url": "",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "",
    "addressLocality": "",
    "addressRegion": "",
    "postalCode": "",
    "addressCountry": ""
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "",
    "longitude": ""
  },
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
      "opens": "9:00",
      "closes": "17:00"
    }
  ]
}`,
      Organization: `{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Organization name",
  "url": "",
  "logo": "",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "",
    "contactType": "customer service"
  },
  "sameAs": [
    "https://www.facebook.com/your-profile",
    "https://www.twitter.com/your-profile"
  ]
}`,
      Person: `{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "Person name",
  "jobTitle": "",
  "url": "",
  "image": "",
  "sameAs": [],
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "",
    "addressRegion": ""
  }
}`,
      WebPage: `{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Page title",
  "description": "Page description",
  "url": "",
  "image": "",
  "datePublished": "",
  "dateModified": "",
  "lastReviewed": "",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "https://example.com"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Category",
        "item": "https://example.com/category"
      }
    ]
  }
}`,
      Event: `{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Event name",
  "description": "Event description",
  "startDate": "",
  "endDate": "",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "location": {
    "@type": "Place",
    "name": "Location name",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "",
      "addressLocality": "",
      "addressRegion": "",
      "postalCode": "",
      "addressCountry": ""
    }
  },
  "performer": {
    "@type": "Person",
    "name": ""
  },
  "organizer": {
    "@type": "Organization",
    "name": "",
    "url": ""
  },
  "offers": {
    "@type": "Offer",
    "price": "",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock",
    "validFrom": ""
  }
}`,
      Recipe: `{
  "@context": "https://schema.org",
  "@type": "Recipe",
  "name": "Recipe name",
  "image": "",
  "description": "Recipe description",
  "author": {
    "@type": "Person",
    "name": ""
  },
  "datePublished": "",
  "prepTime": "PT20M",
  "cookTime": "PT30M",
  "totalTime": "PT50M",
  "recipeYield": "4 servings",
  "recipeIngredient": [
    "Ingredient 1",
    "Ingredient 2"
  ],
  "recipeInstructions": [
    {
      "@type": "HowToStep",
      "text": "Step 1"
    },
    {
      "@type": "HowToStep",
      "text": "Step 2"
    }
  ],
  "nutrition": {
    "@type": "NutritionInformation",
    "calories": "270 calories"
  }
}`,
      FAQPage: `{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Question 1",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Answer to question 1"
      }
    },
    {
      "@type": "Question",
      "name": "Question 2",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Answer to question 2"
      }
    }
  ]
}`,
      Review: `{
  "@context": "https://schema.org",
  "@type": "Review",
  "name": "Review title",
  "reviewBody": "Review content",
  "author": {
    "@type": "Person",
    "name": ""
  },
  "datePublished": "",
  "reviewRating": {
    "@type": "Rating",
    "ratingValue": "5",
    "bestRating": "5"
  },
  "itemReviewed": {
    "@type": "Product",
    "name": ""
  }
}`,
      HowTo: `{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "How to do something",
  "description": "Description of how-to",
  "image": "",
  "totalTime": "PT30M",
  "estimatedCost": {
    "@type": "MonetaryAmount",
    "currency": "USD",
    "value": "100"
  },
  "supply": [
    {
      "@type": "HowToSupply",
      "name": "Supply 1"
    },
    {
      "@type": "HowToSupply",
      "name": "Supply 2"
    }
  ],
  "tool": [
    {
      "@type": "HowToTool",
      "name": "Tool 1"
    },
    {
      "@type": "HowToTool",
      "name": "Tool 2"
    }
  ],
  "step": [
    {
      "@type": "HowToStep",
      "name": "Step 1",
      "text": "Description of step 1",
      "image": "",
      "url": ""
    },
    {
      "@type": "HowToStep",
      "name": "Step 2",
      "text": "Description of step 2",
      "image": "",
      "url": ""
    }
  ]
}`,

      // Creative Works
      Book: `{
  "@context": "https://schema.org",
  "@type": "Book",
  "name": "Book title",
  "author": {
    "@type": "Person",
    "name": "Author name"
  },
  "isbn": "",
  "numberOfPages": "",
  "publisher": {
    "@type": "Organization",
    "name": "Publisher name"
  },
  "datePublished": "",
  "inLanguage": "English"
}`,
      Movie: `{
  "@context": "https://schema.org",
  "@type": "Movie",
  "name": "Movie title",
  "director": {
    "@type": "Person",
    "name": "Director name"
  },
  "actor": [
    {
      "@type": "Person",
      "name": "Actor 1"
    },
    {
      "@type": "Person",
      "name": "Actor 2"
    }
  ],
  "datePublished": "",
  "duration": "PT2H",
  "contentRating": "PG-13"
}`,
      MusicRecording: `{
  "@context": "https://schema.org",
  "@type": "MusicRecording",
  "name": "Song title",
  "byArtist": {
    "@type": "MusicGroup",
    "name": "Artist name"
  },
  "duration": "PT3M",
  "inAlbum": {
    "@type": "MusicAlbum",
    "name": "Album name"
  }
}`,
      SoftwareApplication: `{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "Application name",
  "applicationCategory": "BusinessApplication",
  "operatingSystem": "Windows, macOS, iOS, Android",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  }
}`,
      WebSite: `{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Website name",
  "url": "https://example.com",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://example.com/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}`,

      // Medical
      Hospital: `{
  "@context": "https://schema.org",
  "@type": "Hospital",
  "name": "Hospital name",
  "description": "Hospital description",
  "telephone": "",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "",
    "addressLocality": "",
    "addressRegion": "",
    "postalCode": "",
    "addressCountry": ""
  },
  "medicalSpecialty": ["Oncology", "Cardiology"]
}`,
      MedicalCondition: `{
  "@context": "https://schema.org",
  "@type": "MedicalCondition",
  "name": "Condition name",
  "alternateName": "",
  "description": "Condition description",
  "possibleTreatment": "",
  "possibleComplication": "",
  "typicalTest": ""
}`,

      // Places
      TouristAttraction: `{
  "@context": "https://schema.org",
  "@type": "TouristAttraction",
  "name": "Attraction name",
  "description": "Attraction description",
  "image": "",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "",
    "addressLocality": "",
    "addressRegion": "",
    "postalCode": "",
    "addressCountry": ""
  },
  "openingHours": "",
  "priceRange": ""
}`,
    };
    
    // If template doesn't exist for the specific type, create a generic one
    if (!templates[type]) {
      setJsonLd(`{
  "@context": "https://schema.org",
  "@type": "${type}",
  "name": "",
  "description": ""
}`);
    } else {
      setJsonLd(templates[type]);
    }
  };

  const copyToClipboard = () => {
    try {
      navigator.clipboard.writeText(jsonLd);
      setCopySuccess(true);
      
      // Clear success message after 2 seconds
      setTimeout(() => {
        setCopySuccess(null);
      }, 2000);
    } catch (err) {
      console.error('Failed to copy text: ', err);
      setCopySuccess(false);
    }
  };

  const saveSchema = async () => {
    if (!selectedPages.length) {
      alert('Please select at least one page to apply this schema to.');
      return;
    }

    try {
      setIsSaving(true);
      setSaveSuccess(null);
      
      // Validate JSON before saving
      try {
        JSON.parse(jsonLd);
      } catch (err) {
        alert('Invalid JSON format. Please check your schema.');
        setIsSaving(false);
        return;
      }

      // Create promises for each page
      const savePromises = selectedPages.map(async (pageId) => {
        const data = new FormData();
        data.append('action', 'ssc_save_schema');
        data.append('nonce', window.sscData.nonce);
        data.append('post_id', pageId.toString());
        data.append('schema', jsonLd);

        const response = await fetch(window.sscData.ajaxUrl, {
          method: 'POST',
          body: data,
          credentials: 'same-origin'
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
      });

      // Wait for all saves to complete
      await Promise.all(savePromises);
      
      setSaveSuccess(true);

      // Clear success message after 3 seconds
      setTimeout(() => {
        setSaveSuccess(null);
      }, 3000);
    } catch (error) {
      console.error('Error saving schema:', error);
      setSaveSuccess(false);
    } finally {
      setIsSaving(false);
    }
  };

  const handlePageSelection = (pageId: number, isSelected: boolean) => {
    if (isSelected) {
      setSelectedPages([...selectedPages, pageId]);
    } else {
      setSelectedPages(selectedPages.filter(id => id !== pageId));
    }
  };

  return (
    <div className="space-y-6 fade-in">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold text-gray-900">Schema Builder</h2>
        <div className="flex space-x-2">
          <button
            onClick={copyToClipboard}
            className="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            {copySuccess === true ? (
              <>
                <FileCheck className="mr-2 h-4 w-4 text-green-500" />
                Copied!
              </>
            ) : (
              <>
                <Copy className="mr-2 h-4 w-4" />
                Copy
              </>
            )}
          </button>
          <button 
            onClick={saveSchema}
            disabled={isSaving || selectedPages.length === 0}
            className={`inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors ${
              selectedPages.length === 0 
                ? 'bg-blue-400 cursor-not-allowed'
                : 'bg-blue-600 hover:bg-blue-700'
            }`}
          >
            {isSaving ? (
              <>
                <RefreshCw className="mr-2 h-4 w-4 animate-spin" />
                Saving...
              </>
            ) : (
              <>
                <Save className="mr-2 h-4 w-4" />
                Save Schema
              </>
            )}
          </button>
        </div>
      </div>

      {saveSuccess === true && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
          Schema successfully saved to selected pages!
        </div>
      )}

      {saveSuccess === false && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
          Failed to save schema. Please try again.
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2">
          <div className="bg-white shadow-sm rounded-lg overflow-hidden">
            <div className="p-4 bg-gray-50 border-b border-gray-200">
              <div className="flex items-center justify-between">
                <h3 className="text-sm font-medium text-gray-900">Schema Type</h3>
                <SchemaTypeSelector value={schemaType} onChange={handleSchemaTypeChange} />
              </div>
            </div>
            
            <div className="p-4">
              <textarea
                value={jsonLd}
                onChange={(e) => setJsonLd(e.target.value)}
                rows={20}
                className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                placeholder="Enter your JSON-LD schema here..."
              />
            </div>
          </div>
        </div>

        <div className="lg:col-span-1">
          <div className="bg-white shadow-sm rounded-lg overflow-hidden">
            <div className="p-4 bg-gray-50 border-b border-gray-200">
              <h3 className="text-sm font-medium text-gray-900">Apply Schema To</h3>
            </div>
            
            <div className="p-4">
              <div className="mb-4">
                <span className="text-sm text-gray-500">
                  Select pages to apply this schema to:
                </span>
              </div>
              
              <div className="max-h-[400px] overflow-y-auto pr-2">
                <PageSelector 
                  pages={pages} 
                  selectedPages={selectedPages}
                  onPageSelect={handlePageSelection}
                />
              </div>
              
              <div className="mt-4">
                <span className="text-xs text-gray-500">
                  {selectedPages.length} page(s) selected
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SchemaBuilder;