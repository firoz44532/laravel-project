<!-- WhatsApp Chat Widget -->
<div id="whatsapp-chat" class="fixed bottom-6 right-6 z-[9999]">
    <!-- Chat Button -->
    <button id="whatsapp-toggle" class="bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition-all duration-300 transform hover:scale-110">
        <i class="fab fa-whatsapp text-2xl"></i>
    </button>
    
    <!-- Chat Box -->
    <div id="whatsapp-chat-box" class="hidden absolute bottom-20 right-0 w-80 bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-green-500 text-white p-4">
            <div class="flex items-center">
                <div class="bg-white rounded-full p-2 mr-3">
                    <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Customer Support</h3>
                    <p class="text-sm text-green-100">We typically reply within minutes</p>
                </div>
                <button id="whatsapp-close" class="ml-auto text-white hover:text-green-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Messages -->
        <div class="p-4 h-64 overflow-y-auto bg-gray-50">
            <div class="space-y-3">
                <!-- Welcome Message -->
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <p class="text-sm text-gray-700">
                        👋 Hello! Welcome to our store! How can I help you today?
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Support Team</p>
                </div>
                
                <!-- Quick Replies -->
                <div class="space-y-2">
                    <p class="text-xs text-gray-600 font-medium">Quick replies:</p>
                    <div class="space-y-1">
                        <button class="whatsapp-quick-reply w-full text-left bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm hover:bg-green-50 hover:border-green-300 transition duration-200" data-message="I need help with my order">
                            📦 I need help with my order
                        </button>
                        <button class="whatsapp-quick-reply w-full text-left bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm hover:bg-green-50 hover:border-green-300 transition duration-200" data-message="Do you have international shipping?">
                            🌍 Do you have international shipping?
                        </button>
                        <button class="whatsapp-quick-reply w-full text-left bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm hover:bg-green-50 hover:border-green-300 transition duration-200" data-message="What are your payment methods?">
                            💳 What are your payment methods?
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="p-4 border-t border-gray-200">
            <form id="whatsapp-form" class="space-y-3">
                <textarea 
                    id="whatsapp-message" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                    rows="3" 
                    placeholder="Type your message..."></textarea>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500">
                        <i class="fab fa-whatsapp mr-1"></i>
                        Chat via WhatsApp
                    </p>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        <i class="fab fa-whatsapp mr-2"></i>Send on WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Debug Info (remove in production) -->
<div class="fixed top-4 right-4 bg-red-500 text-white p-2 rounded text-xs z-[10000]">
    WhatsApp Widget Loaded
</div>

<style>
/* WhatsApp Chat Styles */
#whatsapp-chat-box {
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.whatsapp-quick-reply:hover {
    transform: translateX(2px);
}

/* Mobile Responsive */
@media (max-width: 640px) {
    #whatsapp-chat-box {
        width: calc(100vw - 48px);
        right: -12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('WhatsApp Widget Loading...');
    
    const toggle = document.getElementById('whatsapp-toggle');
    const close = document.getElementById('whatsapp-close');
    const chatBox = document.getElementById('whatsapp-chat-box');
    const form = document.getElementById('whatsapp-form');
    const messageInput = document.getElementById('whatsapp-message');
    const quickReplies = document.querySelectorAll('.whatsapp-quick-reply');
    
    console.log('WhatsApp Elements:', { toggle, close, chatBox, form });
    
    // Toggle chat box
    if (toggle) {
        toggle.addEventListener('click', function() {
            console.log('WhatsApp button clicked');
            chatBox.classList.toggle('hidden');
            if (!chatBox.classList.contains('hidden')) {
                messageInput.focus();
            }
        });
    }
    
    // Close chat box
    if (close) {
        close.addEventListener('click', function() {
            chatBox.classList.add('hidden');
        });
    }
    
    // Quick reply buttons
    quickReplies.forEach(button => {
        button.addEventListener('click', function() {
            const message = this.getAttribute('data-message');
            messageInput.value = message;
            messageInput.focus();
        });
    });
    
    // Send message via WhatsApp
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (message) {
                // WhatsApp number from service
                const phoneNumber = '{{ \App\Services\WhatsAppService::getPhoneNumber() }}';
                
                // Encode message for URL
                const encodedMessage = encodeURIComponent(message);
                
                // Create WhatsApp URL
                const whatsappUrl = `https://wa.me/${phoneNumber.replace(/[^\d]/g, '')}?text=${encodedMessage}`;
                
                // Open WhatsApp in new tab
                window.open(whatsappUrl, '_blank');
                
                // Clear input and close chat
                messageInput.value = '';
                chatBox.classList.add('hidden');
            }
        });
    }
    
    // Close chat when clicking outside
    document.addEventListener('click', function(e) {
        if (!chatBox.contains(e.target) && !toggle.contains(e.target)) {
            chatBox.classList.add('hidden');
        }
    });
    
    console.log('WhatsApp Widget Loaded Successfully');
});
</script>
