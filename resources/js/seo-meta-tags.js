// SEO Meta Tags Manager
class SEOMetaTags {
    constructor() {
        this.init();
    }

    init() {
        this.addMetaTags();
        this.addStructuredData();
        this.addOpenGraphTags();
        this.addTwitterCards();
        this.addJsonLd();
    }

    addMetaTags() {
        // Basic meta tags
        this.addMetaTag('description', 'Shop for the latest products at best prices in Bangladesh. Wide range of electronics, fashion, and more with fast delivery.');
        this.addMetaTag('keywords', 'online shopping, Bangladesh, e-commerce, electronics, fashion, best prices, fast delivery');
        this.addMetaTag('author', 'E-commerce Shop');
        this.addMetaTag('viewport', 'width=device-width, initial-scale=1.0');
        this.addMetaTag('robots', 'index, follow, max-image-preview:large');
        this.addMetaTag('language', 'en');
        this.addMetaTag('rating', 'general');
        this.addMetaTag('geo.region', 'BD');
        this.addMetaTag('currency', 'BDT');
        this.addMetaTag('distribution', 'global');
        this.addMetaTag('revisit-after', '7 days');
        this.addMetaTag('theme-color', '#ff6b35');
    }

    addStructuredData() {
        // Organization structured data
        const organizationData = {
            '@context': 'https://schema.org',
            '@type': 'Organization',
            'name': 'E-commerce Shop',
            'url': window.location.origin,
            'logo': window.location.origin + '/images/logo.png',
            'description': 'Shop for the latest products at best prices in Bangladesh. Wide range of electronics, fashion, and more with fast delivery.',
            'address': {
                'addressCountry': 'Bangladesh',
                'addressRegion': 'Dhaka',
                'postalCode': '1000',
                'streetAddress': '123 Main Street'
            },
            'contactPoint': {
                '@type': 'ContactPoint',
                'telephone': '+880 1234 567890',
                'contactType': 'customer service',
                'areaServed': 'Bangladesh',
                'availableLanguage': ['en', 'bn'],
                'availableLanguage': ['en', 'bn']
            },
            'sameAs': [
                {
                    '@type': 'WebPage',
                    'url': window.location.origin
                }
            ]
        };

        this.addJsonLdTag(JSON.stringify(organizationData));
    }

    addOpenGraphTags() {
        // Open Graph meta tags
        this.addMetaProperty('og:title', document.title);
        this.addMetaProperty('og:description', 'Shop for the latest products at best prices in Bangladesh. Wide range of electronics, fashion, and more with fast delivery.');
        this.addMetaProperty('og:type', 'website');
        this.addMetaProperty('og:url', window.location.href);
        this.addMetaProperty('og:image', window.location.origin + '/images/og-image.jpg');
        this.addMetaProperty('og:image:width', '1200');
        this.addMetaProperty('og:image:height', '630');
        this.addMetaProperty('og:site_name', 'E-commerce Shop');
        this.addMetaProperty('og:locale', 'en_BD');
        this.addMetaProperty('og:currency', 'BDT');
        this.addMetaProperty('og:price:amount', '0.00');
        this.addMetaProperty('og:price:currency', 'BDT');
    }

    addTwitterCards() {
        // Twitter Card meta tags
        this.addMetaName('twitter:card', 'summary_large_image');
        this.addMetaName('twitter:title', document.title);
        this.addMetaName('twitter:description', 'Shop for the latest products at best prices in Bangladesh. Wide range of electronics, fashion, and more with fast delivery.');
        this.addMetaName('twitter:image', window.location.origin + '/images/twitter-image.jpg');
        this.addMetaName('twitter:site', '@ecommerce_shop');
        this.addMetaName('twitter:creator', '@ecommerce_shop');
        this.addMetaName('twitter:domain', window.location.hostname);
        this.addMetaName('twitter:card', 'summary_large_image');
    }

    addJsonLd(jsonLd) {
        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.text = jsonLd;
        document.head.appendChild(script);
    }

    addMetaTag(name, content) {
        let tag = document.querySelector(`meta[name="${name}"]`);
        if (!tag) {
            tag = document.createElement('meta');
            tag.name = name;
            document.head.appendChild(tag);
        }
        tag.content = content;
    }

    addMetaProperty(property, content) {
        let tag = document.querySelector(`meta[property="${property}"]`);
        if (!tag) {
            tag = document.createElement('meta');
            tag.property = property;
            document.head.appendChild(tag);
        }
        tag.content = content;
    }

    addMetaName(name, content) {
        let tag = document.querySelector(`meta[name="${name}"]`);
        if (!tag) {
            tag = document.createElement('meta');
            tag.name = name;
            document.head.appendChild(tag);
        }
        tag.content = content;
    }

    updateMetaTags(pageData) {
        // Update page-specific meta tags
        if (pageData.title) {
            document.title = pageData.title;
            this.updateMetaTag('og:title', pageData.title);
            this.updateMetaTag('twitter:title', pageData.title);
        }
        
        if (pageData.description) {
            this.updateMetaTag('description', pageData.description);
            this.updateMetaTag('og:description', pageData.description);
            this.updateMetaTag('twitter:description', pageData.description);
        }
        
        if (pageData.keywords) {
            this.updateMetaTag('keywords', pageData.keywords);
        }
        
        if (pageData.image) {
            this.updateMetaTag('og:image', pageData.image);
            this.updateMetaTag('twitter:image', pageData.image);
        }
        
        if (pageData.url) {
            this.updateMetaTag('og:url', pageData.url);
            this.updateMetaTag('canonical', pageData.url);
        }
    }

    updateMetaTag(name, content) {
        const tag = document.querySelector(`meta[name="${name}"]`);
        if (tag) {
            tag.content = content;
        }
    }

    updateMetaProperty(property, content) {
        const tag = document.querySelector(`meta[property="${property}"]`);
        if (tag) {
            tag.content = content;
        }
    }

    updateMetaName(name, content) {
        const tag = document.querySelector(`meta[name="${name}"]`);
        if (tag) {
            tag.content = content;
        }
    }

    addCanonicalUrl(url) {
        let link = document.querySelector('link[rel="canonical"]');
        if (!link) {
            link = document.createElement('link');
            link.rel = 'canonical';
            document.head.appendChild(link);
        }
        link.href = url;
    }

    addHrefLangTags() {
        // Add hreflang tags for multiple languages
        const languages = ['en', 'bn'];
        languages.forEach(lang => {
            const link = document.createElement('link');
            link.rel = 'alternate';
            link.hreflang = lang;
            link.href = window.location.origin + '/' + lang;
            document.head.appendChild(link);
        });
    }

    addBreadcrumbJsonLd(breadcrumbs) {
        const breadcrumbData = {
            '@context': 'https://schema.org',
            '@type': 'BreadcrumbList',
            'itemListElement': breadcrumbs
        };
        
        this.addJsonLdTag(JSON.stringify(breadcrumbData));
    }

    addProductJsonLd(product) {
        const productData = {
            '@context': 'https://schema.org',
            '@type': 'Product',
            'name': product.name,
            'image': product.image,
            'description': product.description,
            'sku': product.sku,
            'brand': product.brand,
            'category': product.category,
            'offers': {
                '@type': 'Offer',
                'price': product.price,
                'priceCurrency': 'BDT',
                'availability': product.availability,
                'url': product.url,
                'seller': {
                    '@type': 'Organization',
                    'name': 'E-commerce Shop'
                }
            },
            'aggregateRating': product.rating,
            'reviewCount': product.reviewCount
        };
        
        this.addJsonLdTag(JSON.stringify(productData));
    }

    addArticleJsonLd(article) {
        const articleData = {
            '@context': 'https://schema.org',
            '@type': 'Article',
            'headline': article.title,
            'description': article.description,
            'image': article.image,
            'author': article.author,
            'publisher': {
                '@type': 'Organization',
                'name': 'E-commerce Shop'
            },
            'datePublished': article.datePublished,
            'dateModified': article.dateModified,
            'mainEntityOfPage': {
                '@type': 'WebPage',
                '@id': article.url
            }
        };
        
        this.addJsonLdTag(JSON.stringify(articleData));
    }

    addReviewJsonLd(review) {
        const reviewData = {
            '@context': 'https://schema.org',
            '@type': 'Review',
            'itemReviewed': review.itemReviewed,
            'reviewRating': review.reviewRating,
            'author': review.author,
            'datePublished': review.datePublished,
            'reviewBody': review.reviewBody,
            'publisher': {
                '@type': 'Organization',
                'name': 'E-commerce Shop'
            }
        };
        
        this.addJsonLdTag(JSON.stringify(reviewData));
    }

    addEventJsonLd(event) {
        const eventData = {
            '@context': 'https://schema.org',
            '@type': 'Event',
            'name': event.name,
            'description': event.description,
            'startDate': event.startDate,
            'endDate': event.endDate,
            'location': event.location,
            'organizer': {
                '@type': 'Organization',
                'name': 'E-commerce Shop'
            }
        };
        
        this.addJsonLdTag(JSON.stringify(eventData));
    }

    addLocalBusinessJsonLd() {
        const businessData = {
            '@context': 'https://schema.org',
            '@type': 'LocalBusiness',
            'name': 'E-commerce Shop',
            'description': 'Shop for the latest products at best prices in Bangladesh. Wide range of electronics, fashion, and more with fast delivery.',
            'image': window.location.origin + '/images/storefront.jpg',
            'url': window.location.origin,
            'telephone': '+880 1234 567890',
            'address': {
                '@type': 'PostalAddress',
                'addressCountry': 'Bangladesh',
                'addressLocality': 'Dhaka',
                'postalCode': '1000',
                'streetAddress': '123 Main Street'
            },
            'geo': {
                '@type': 'GeoCoordinates',
                'latitude': 23.7104,
                'longitude': 90.4074'
            },
            'openingHours': 'Mo-Sa 10:00-20:00',
            'priceRange': '৳50-৳50000',
            'servesCuisine': ['Electronics', 'Fashion', 'Home & Garden'],
            'paymentAccepted': ['Cash', 'Card', 'bKash', 'Nagad', 'Rocket', 'Upay'],
            'currenciesAccepted': 'BDT'
        };
        
        this.addJsonLdTag(JSON.stringify(businessData));
    }

    addFAQJsonLd(faqs) {
        const faqData = {
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            'mainEntity': {
                '@type': 'Question',
                'name': faqs[0].question,
                'acceptedAnswer': faqs[0].answer
            },
            'description': faqs.map(faq => ({
                '@type': 'Question',
                'name': faq.question,
                'acceptedAnswer': faq.answer
            }))
        };
        
        this.addJsonLdTag(JSON.stringify(faqData));
    }

    generateProductSchema(product) {
        const schema = {
            '@context': 'https://schema.org',
            '@type': 'Product',
            'name': product.name,
            'image': product.image,
            'description': product.description,
            'sku': product.sku,
            'brand': product.brand,
            'category': product.category,
            'offers': {
                '@type': 'Offer',
                'price': product.price,
                'priceCurrency': 'BDT',
                'availability': product.availability,
                'url': product.url,
                'seller': {
                    '@type': 'Organization',
                    'name': 'E-commerce Shop'
                }
            },
            'aggregateRating': product.rating,
            'reviewCount': product.reviewCount,
            'aggregateOffer': {
                '@type': 'AggregateOffer',
                'priceCurrency': 'BDT',
                'lowPrice': product.lowPrice,
                'highPrice': product.highPrice,
                'offerCount': product.offerCount
            }
        };
        
        return schema;
    }

    generateArticleSchema(article) {
        const schema = {
            '@context': 'https://schema.org',
            '@type': 'Article',
            'headline': article.title,
            'description': article.description,
            'image': article.image,
            'author': article.author,
            'publisher': {
                '@type': 'Organization',
                'name': 'E-commerce Shop'
            },
            'datePublished': article.datePublished,
            'dateModified': article.dateModified,
            'mainEntityOfPage': {
                '@type': 'WebPage',
                '@id': article.url
            }
        };
        
        return schema;
    }

    generateReviewSchema(review) {
        const schema = {
            '@context': 'https://schema.org',
            '@type': 'Review',
            'itemReviewed': review.itemReviewed,
            'reviewRating': review.reviewRating,
            'author': review.author,
            'datePublished': review.datePublished,
            'reviewBody': review.reviewBody,
            'publisher': {
                '@type': 'Organization',
                'name': 'E-commerce Shop'
            }
        };
        
        return schema;
    }

    generateEventSchema(event) {
        const schema = {
            '@context': 'https://schema.org',
            '@type': 'Event',
            'name': event.name,
            'description': event.description,
            'startDate': event.startDate,
            'endDate': event.endDate,
            'location': event.location,
            'organizer': {
                '@type': 'Organization',
                'name': 'E-commerce Shop'
            }
        };
        
        return schema;
    }

    generateLocalBusinessSchema() {
        const schema = {
            '@context': 'https://schema.org',
            '@type': 'LocalBusiness',
            'name': 'E-commerce Shop',
            'description': 'Shop for the latest products at best prices in Bangladesh. Wide range of electronics, fashion, and more with fast delivery.',
            'image': window.location.origin + '/images/storefront.jpg',
            'url': window.location.origin,
            'telephone': '+880 1234 567890',
            'address': {
                '@type': 'PostalAddress',
                'addressCountry': 'Bangladesh',
                'addressLocality': 'Dhaka',
                'postalCode': '1000',
                'streetAddress': '123 Main Street'
            },
            'geo': {
                '@type': 'GeoCoordinates',
                'latitude': 23.7104,
                'longitude': 90.4074
            },
            'openingHours': 'Mo-Sa 10:00-20:00',
            'priceRange': '৳50-৳50000',
            'servesCuisine': ['Electronics', 'Fashion', 'Home & Garden'],
            'paymentAccepted': ['Cash', 'Card', 'bKash', 'Nagad', 'Rocket', 'Upay'],
            'currenciesAccepted': 'BDT'
        };
        
        return schema;
    }

    generateFAQSchema(faqs) {
        const schema = {
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            'mainEntity': {
                '@type': 'Question',
                'name': faqs[0].question,
                'acceptedAnswer': faqs[0].answer
            },
            'description': faqs.map(faq => ({
                '@type': 'Question',
                'name': faq.question,
                'acceptedAnswer': faq.answer
            }))
        };
        
        return schema;
    }

    // Helper method to add JSON-LD script tag
    addJsonLdTag(jsonLd) {
        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.text = jsonLd;
        document.head.appendChild(script);
    }

    // Helper method to update canonical URL
    updateCanonicalUrl(url) {
        let link = document.querySelector('link[rel="canonical"]');
        if (!link) {
            link = document.createElement('link');
            link.rel = 'canonical';
            document.head.appendChild(link);
        }
        link.href = url;
    }
}

// Initialize SEO meta tags
const seoMetaTags = new SEOMetaTags();

// Make it globally available
window.seoMetaTags = seoMetaTags;
