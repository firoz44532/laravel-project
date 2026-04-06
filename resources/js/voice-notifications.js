/**
 * Auto Voice Notification System
 * Web Speech API based voice notifications for e-commerce platform
 */

class VoiceNotificationSystem {
    constructor() {
        this.synth = window.speechSynthesis;
        this.voices = [];
        this.isEnabled = true;
        this.volume = 0.8;
        this.rate = 1.0;
        this.pitch = 1.0;
        this.voiceSettings = {
            orderConfirmation: { voice: 'female', rate: 1.0, pitch: 1.0 },
            paymentSuccess: { voice: 'female', rate: 1.1, pitch: 1.1 },
            newOrder: { voice: 'male', rate: 1.0, pitch: 0.9 },
            lowStock: { voice: 'female', rate: 1.2, pitch: 1.2 },
            error: { voice: 'female', rate: 0.9, pitch: 0.8 }
        };
        
        this.init();
    }

    init() {
        // Load available voices
        this.loadVoices();
        
        // Listen for voice changes
        if (this.synth.onvoiceschanged !== undefined) {
            this.synth.onvoiceschanged = () => this.loadVoices();
        }

        // Load user preferences
        this.loadUserPreferences();
        
        // Add keyboard shortcut for voice toggle (Ctrl + V)
        document.addEventListener('keydown', (e) => {
            // Check for Ctrl+V combination
            if (e.ctrlKey && (e.key === 'v' || e.key === 'V')) {
                e.preventDefault();
                this.toggleVoice();
                this.showNotification(this.isEnabled ? 'Voice Enabled' : 'Voice Disabled');
                console.log('Voice toggled:', this.isEnabled ? 'ON' : 'OFF');
            }
            
            // Also add Alt+V as alternative
            if (e.altKey && (e.key === 'v' || e.key === 'V')) {
                e.preventDefault();
                this.showControlPanel();
                console.log('Voice control panel opened');
            }
        });
    }

    loadVoices() {
        this.voices = this.synth.getVoices();
    }

    loadUserPreferences() {
        const saved = localStorage.getItem('voiceSettings');
        if (saved) {
            const settings = JSON.parse(saved);
            this.isEnabled = settings.enabled !== false;
            this.volume = settings.volume || 0.8;
            this.rate = settings.rate || 1.0;
            this.pitch = settings.pitch || 1.0;
        }
    }

    saveUserPreferences() {
        const settings = {
            enabled: this.isEnabled,
            volume: this.volume,
            rate: this.rate,
            pitch: this.pitch
        };
        localStorage.setItem('voiceSettings', JSON.stringify(settings));
    }

    speak(text, options = {}) {
        if (!this.isEnabled || !this.synth) {
            return;
        }

        // Cancel any ongoing speech
        this.synth.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        
        // Apply settings
        utterance.volume = options.volume || this.volume;
        utterance.rate = options.rate || this.rate;
        utterance.pitch = options.pitch || this.pitch;
        
        // Set voice based on options
        if (options.voiceType) {
            const voice = this.getVoiceByType(options.voiceType);
            if (voice) utterance.voice = voice;
        }

        // Add event listeners
        utterance.onstart = () => {
            this.showSpeakingIndicator(true);
        };

        utterance.onend = () => {
            this.showSpeakingIndicator(false);
        };

        utterance.onerror = (event) => {
            console.error('Speech error:', event);
            this.showSpeakingIndicator(false);
        };

        this.synth.speak(utterance);
    }

    getVoiceByType(type) {
        if (!this.voices.length) return null;
        
        const preferredLang = 'en-US';
        const gender = type === 'male' ? 'male' : 'female';
        
        // Try to find voice matching gender and language
        let voice = this.voices.find(v => 
            v.lang.includes(preferredLang) && 
            v.name.toLowerCase().includes(gender)
        );
        
        // Fallback to any voice with preferred language
        if (!voice) {
            voice = this.voices.find(v => v.lang.includes(preferredLang));
        }
        
        // Final fallback
        if (!voice) {
            voice = this.voices[0];
        }
        
        return voice;
    }

    // Predefined notification methods
    orderConfirmation(orderNumber) {
        const messages = [
            `Order ${orderNumber} has been confirmed successfully!`,
            `Thank you for your order! Your order number is ${orderNumber}`,
            `Order confirmed! ${orderNumber} is being processed`
        ];
        const message = messages[Math.floor(Math.random() * messages.length)];
        
        this.speak(message, this.voiceSettings.orderConfirmation);
    }

    paymentSuccess(amount) {
        const messages = [
            `Payment successful! Amount ${amount} BDT has been received`,
            `Payment confirmed! ${amount} BDT processed successfully`,
            `Thank you! Your payment of ${amount} BDT is complete`
        ];
        const message = messages[Math.floor(Math.random() * messages.length)];
        
        this.speak(message, this.voiceSettings.paymentSuccess);
    }

    newOrder(orderNumber) {
        const messages = [
            `New order received! Order number ${orderNumber}`,
            `Attention! New order ${orderNumber} needs processing`,
            `New order alert! ${orderNumber} is ready for review`
        ];
        const message = messages[Math.floor(Math.random() * messages.length)];
        
        this.speak(message, this.voiceSettings.newOrder);
    }

    lowStock(productName, stock) {
        const messages = [
            `Warning! Product ${productName} is running low on stock. Only ${stock} items left`,
            `Low stock alert! ${productName} has only ${stock} items remaining`,
            `Stock warning! ${productName} needs restocking. Current stock: ${stock}`
        ];
        const message = messages[Math.floor(Math.random() * messages.length)];
        
        this.speak(message, this.voiceSettings.lowStock);
    }

    orderStatusUpdate(status, orderNumber) {
        const messages = {
            'shipped': `Good news! Order ${orderNumber} has been shipped`,
            'delivered': `Great! Order ${orderNumber} has been delivered`,
            'processing': `Order ${orderNumber} is now being processed`,
            'cancelled': `Order ${orderNumber} has been cancelled`
        };
        
        const message = messages[status] || `Order ${orderNumber} status updated to ${status}`;
        this.speak(message, this.voiceSettings.orderConfirmation);
    }

    error(message) {
        this.speak(`Error: ${message}`, this.voiceSettings.error);
    }

    welcome() {
        const messages = [
            "Welcome to our e-commerce store!",
            "Hello! Welcome to ShopBD",
            "Welcome! Happy shopping with us"
        ];
        const message = messages[Math.floor(Math.random() * messages.length)];
        this.speak(message, { voiceType: 'female', rate: 0.9, pitch: 1.0 });
    }

    // Utility methods
    toggleVoice() {
        this.isEnabled = !this.isEnabled;
        this.saveUserPreferences();
        
        if (this.isEnabled) {
            this.speak("Voice notifications enabled");
        }
    }

    setVolume(volume) {
        this.volume = Math.max(0, Math.min(1, volume));
        this.saveUserPreferences();
    }

    setRate(rate) {
        this.rate = Math.max(0.5, Math.min(2, rate));
        this.saveUserPreferences();
    }

    setPitch(pitch) {
        this.pitch = Math.max(0.5, Math.min(2, pitch));
        this.saveUserPreferences();
    }

    showSpeakingIndicator(isSpeaking) {
        let indicator = document.getElementById('voice-speaking-indicator');
        
        if (isSpeaking) {
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'voice-speaking-indicator';
                indicator.innerHTML = `
                    <div class="fixed top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center z-50">
                        <div class="animate-pulse mr-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm">Speaking...</span>
                    </div>
                `;
                document.body.appendChild(indicator);
            }
        } else {
            if (indicator) {
                indicator.remove();
            }
        }
    }

    showNotification(message) {
        // Create a temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-20 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    // Create voice control panel
    createControlPanel() {
        const panel = document.createElement('div');
        panel.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-xl p-4 z-50 border border-gray-200';
        panel.innerHTML = `
            <div class="flex flex-col space-y-3">
                <h3 class="font-semibold text-gray-800">Voice Controls</h3>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Enable Voice:</label>
                    <input type="checkbox" id="voice-toggle" ${this.isEnabled ? 'checked' : ''} class="rounded">
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Volume:</label>
                    <input type="range" id="voice-volume" min="0" max="1" step="0.1" value="${this.volume}" class="w-24">
                    <span class="text-sm text-gray-600">${Math.round(this.volume * 100)}%</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Speed:</label>
                    <input type="range" id="voice-rate" min="0.5" max="2" step="0.1" value="${this.rate}" class="w-24">
                    <span class="text-sm text-gray-600">${this.rate}x</span>
                </div>
                
                <button id="test-voice" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                    Test Voice
                </button>
                
                <button id="close-voice-panel" class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                    Close
                </button>
            </div>
        `;
        
        document.body.appendChild(panel);
        
        // Add event listeners
        document.getElementById('voice-toggle').addEventListener('change', (e) => {
            this.isEnabled = e.target.checked;
            this.saveUserPreferences();
        });
        
        document.getElementById('voice-volume').addEventListener('input', (e) => {
            this.setVolume(parseFloat(e.target.value));
            e.target.nextElementSibling.textContent = Math.round(this.volume * 100) + '%';
        });
        
        document.getElementById('voice-rate').addEventListener('input', (e) => {
            this.setRate(parseFloat(e.target.value));
            e.target.nextElementSibling.textContent = this.rate + 'x';
        });
        
        document.getElementById('test-voice').addEventListener('click', () => {
            this.speak("Voice notification system is working perfectly!");
        });
        
        document.getElementById('close-voice-panel').addEventListener('click', () => {
            panel.remove();
        });
    }
}

// Initialize the voice system
window.voiceSystem = new VoiceNotificationSystem();

// Make it globally available for easy access
window.VoiceNotifications = {
    orderConfirmation: (orderNumber) => window.voiceSystem.orderConfirmation(orderNumber),
    paymentSuccess: (amount) => window.voiceSystem.paymentSuccess(amount),
    newOrder: (orderNumber) => window.voiceSystem.newOrder(orderNumber),
    lowStock: (product, stock) => window.voiceSystem.lowStock(product, stock),
    orderStatusUpdate: (status, orderNumber) => window.voiceSystem.orderStatusUpdate(status, orderNumber),
    error: (message) => window.voiceSystem.error(message),
    welcome: () => window.voiceSystem.welcome(),
    toggle: () => window.voiceSystem.toggleVoice(),
    showControlPanel: () => window.voiceSystem.createControlPanel()
};

// Auto-welcome on page load (with delay)
setTimeout(() => {
    if (window.voiceSystem.isEnabled) {
        window.voiceSystem.welcome();
    }
}, 2000);
