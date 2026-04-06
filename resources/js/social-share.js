// Social Media Sharing Component
class SocialShare {
    constructor() {
        this.init();
    }

    init() {
        // Add social sharing buttons to all product pages
        this.addShareButtons();
    }

    addShareButtons() {
        // Add share buttons to product detail pages
        const productDetailElements = document.querySelectorAll('.product-detail-page');
        productDetailElements.forEach(element => {
            this.addShareButtonsToElement(element);
        });

        // Add share buttons to product cards
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            this.addShareButtonsToCard(card);
        });
    }

    addShareButtonsToElement(element) {
        const shareContainer = document.createElement('div');
        shareContainer.className = 'social-share-buttons mt-4 flex space-x-2';
        
        const shareButtons = [
            {
                name: 'Facebook',
                icon: 'fab fa-facebook-f',
                color: '#1877f2',
                url: this.getShareUrl('facebook', element)
            },
            {
                name: 'Twitter',
                icon: 'fab fa-twitter',
                color: '#1da1f2',
                url: this.getShareUrl('twitter', element)
            },
            {
                name: 'WhatsApp',
                icon: 'fab fa-whatsapp',
                color: '#25d366',
                url: this.getShareUrl('whatsapp', element)
            },
            {
                name: 'Pinterest',
                icon: 'fab fa-pinterest',
                color: '#bd081c',
                url: this.getShareUrl('pinterest', element)
            },
            {
                name: 'LinkedIn',
                icon: 'fab fa-linkedin-in',
                color: '#0077b5',
                url: this.getShareUrl('linkedin', element)
            },
            {
                name: 'Email',
                icon: 'fas fa-envelope',
                color: '#6c757d',
                url: this.getShareUrl('email', element)
            }
        ];

        shareButtons.forEach(button => {
            const buttonElement = document.createElement('button');
            buttonElement.className = 'social-share-btn bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition-colors duration-200';
            buttonElement.innerHTML = `
                <i class="${button.icon}"></i>
                <span class="ml-1">${button.name}</span>
            `;
            buttonElement.onclick = () => this.share(button.url, button.name);
            
            shareContainer.appendChild(buttonElement);
        });

        // Insert share buttons after product actions
        const productActions = element.querySelector('.product-actions');
        if (productActions) {
            productActions.insertAdjacentElement('afterend', shareContainer);
        }
    }

    addShareButtonsToCard(card) {
        const shareContainer = document.createElement('div');
        shareContainer.className = 'social-share-buttons absolute top-2 right-2 flex space-x-1';
        
        const shareButtons = [
            {
                name: 'Facebook',
                icon: 'fab fa-facebook-f',
                color: '#1877f2',
                url: this.getShareUrl('facebook', card)
            },
            {
                name: 'Twitter',
                icon: 'fab fa-twitter',
                color: '#1da1f2',
                url: this.getShareUrl('twitter', card)
            },
            {
                name: 'WhatsApp',
                icon: 'fab fa-whatsapp',
                color: '#25d366',
                url: this.getShareUrl('whatsapp', card)
            }
        ];

        shareButtons.forEach(button => {
            const buttonElement = document.createElement('button');
            buttonElement.className = 'social-share-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-700 p-1 rounded transition-all duration-200';
            buttonElement.innerHTML = `<i class="${button.icon}"></i>`;
            buttonElement.onclick = () => this.share(button.url, button.name);
            
            shareContainer.appendChild(buttonElement);
        });

        card.appendChild(shareContainer);
    }

    getShareUrl(platform, element) {
        const url = element.dataset.url || window.location.href;
        const title = element.dataset.title || document.title;
        
        switch (platform) {
            case 'facebook':
                return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&t=${encodeURIComponent(title)}`;
            case 'twitter':
                return `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            case 'whatsapp':
                return `https://wa.me/?text=${encodeURIComponent(title)} ${encodeURIComponent(url)}`;
            case 'pinterest':
                return `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&description=${encodeURIComponent(title)}`;
            case 'linkedin':
                return `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`;
            case 'email':
                return `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(url)}`;
            default:
                return url;
        }
    }

    share(url, platform) {
        if (platform === 'WhatsApp') {
            window.open(url, '_blank');
        } else {
            window.open(url, '_blank', 'width=600,height=400');
        }
    }

    // Add floating share widget
    addFloatingShareWidget() {
        const widget = document.createElement('div');
        widget.className = 'floating-share-widget fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 z-50';
        widget.innerHTML = `
            <div class="text-sm font-medium text-gray-700 mb-2">Share this product</div>
            <div class="flex space-x-2">
                <button onclick="socialShare.share('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href) + '&t=' + encodeURIComponent(document.title) + ')" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button onclick="socialShare.share('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(document.title) + ')" class="bg-blue-400 text-white p-2 rounded-lg hover:bg-blue-500 transition-colors">
                    <i class="fab fa-twitter"></i>
                </button>
                <button onclick="socialShare.share('https://wa.me/?text=' + encodeURIComponent(document.title) + ' ' + encodeURIComponent(window.location.href) + ')" class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fab fa-whatsapp"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(widget);
        
        // Show/hide widget on scroll
        let lastScrollTop = 0;
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 300) {
                widget.classList.add('translate-y-0');
            } else {
                widget.classList.remove('translate-y-0');
            }
            
            lastScrollTop = scrollTop;
        });
    }

    // Add copy link functionality
    addCopyLinkButton() {
        const copyButtons = document.querySelectorAll('.copy-link-btn');
        copyButtons.forEach(button => {
            button.addEventListener('click', () => {
                const url = button.dataset.url || window.location.href;
                this.copyToClipboard(url);
                this.showNotification('Link copied to clipboard!');
            });
        });
    }

    copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize social sharing
const socialShare = new SocialShare();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    socialShare.init();
});

// Make it globally available
window.socialShare = socialShare;
