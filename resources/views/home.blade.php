@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <!-- Hero Section with Video Background -->
    <div class="relative overflow-hidden bg-white min-h-[506px]">
        <!-- Video Background (unchanged) -->
        <div class="absolute inset-0">
            <video autoplay loop muted playsinline class="w-full h-full object-cover" style="min-height: 506px">
                <source src="{{ asset('videos/hero-bg.mp4') }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <!-- Chat Box -->
                <div 
                    x-data="chat()"
                    class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-md"
                    x-init="init()"
                >
                    <!-- Chat Header -->
                    <div class="bg-blue-600 p-4">
                        <h2 class="text-white text-lg font-semibold">Chat with a Legal Expert in seconds</h2>
                        <p x-show="conversation" class="text-blue-100 text-sm">
                            <span x-show="conversation && conversation.status === 'pending'">Waiting for a lawyer...</span>
                            <span x-show="conversation && conversation.status === 'active'">Connected with <span x-text="conversation?.lawyer?.name"></span></span>
                        </p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <button data-message="I need help with Family Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Family Law</button>
                            <button data-message="I need help with Criminal Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Criminal Law</button>
                            <button data-message="I need help with Business Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Business Law</button>
                            <button data-message="I need help with Real Estate Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Real Estate</button>
                            <button data-message="I need help with Immigration Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Immigration</button>
                            <button data-message="I need help with Employment Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Employment Law</button>
                            <button data-message="I need help with Personal Injury Law" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">Personal Injury</button>
                            <button data-message="I need help with another legal matter" class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full">More</button>
                        </div>
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div 
                        class="bg-gray-50 h-80 p-4 overflow-y-auto"
                        x-ref="messagesContainer"
                        id="messages"
                    >
                        <div class="space-y-4">
                            <!-- Welcome message -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 bg-white rounded-lg py-2 px-4 shadow-sm">
                                    <div x-data="{ showMessage: false }" x-init="setTimeout(() => showMessage = true, 1000)">
                                        <!-- Loading dots -->
                                        <div x-show="!showMessage" class="flex space-x-1">
                                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                        </div>
                                        <!-- Message -->
                                        <p x-show="showMessage" 
                                           x-transition:enter="transition ease-out duration-300"
                                           x-transition:enter-start="opacity-0" 
                                           x-transition:enter-end="opacity-100"
                                           class="text-gray-800">
                                            Hello! What legal matter can I help you with right now?
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Messages -->
                            <template x-for="message in messages" :key="message.id">
                                <div class="message-custom-style flex flex-col items-end mb-4">
                                    <div class="message-bubble bg-blue-500 text-white rounded-[30px] px-6 py-3 max-w-[75%]">
                                        <div class="text-right text-sm mb-1">Guest</div>
                                        <div class="text-right text-sm" x-text="message.content"></div>
                                        <div class="text-right text-xs text-blue-100 mt-1" x-text="formatDate(message.created_at)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Chat Input Area -->
                    <div class="p-4 bg-white border-t">
                        <div class="text-sm text-green-600 mb-2 flex items-center justify-center">
                            <span class="inline-block w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                            Legal experts are online now
                        </div>
                        <form @submit.prevent="sendMessage" class="flex space-x-3">
                            <input 
                                type="text" 
                                x-model="newMessage"
                                placeholder="Type your message..." 
                                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                            >
                            <button 
                                type="submit"
                                class="bg-blue-600 text-white rounded-lg px-4 py-2 font-semibold hover:bg-blue-700 transition duration-300 flex items-center justify-center space-x-2"
                                :disabled="!newMessage.trim()"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right side content (unchanged) -->
                <div class="text-center md:text-left text-white">
                    <h1 class="text-4xl font-bold mb-4">Get Legal Help Now</h1>
                    <p class="text-xl opacity-90">Connect instantly with qualified legal experts ready to assist you with your questions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.userId = {{ auth()->check() ? auth()->id() : 'null' }};
    window.pusherKey = '0cab072eaeccb141e0a3';
    window.pusherCluster = 'eu';

function chat() {
    return {
        messages: [],
        newMessage: '',
        conversation: null,
        userId: {{ auth()->id() ?? 'null' }},
        canSendMessage: true,

        init() {
            this.loadExistingConversation();
            this.setupEchoListeners();
        },

        async loadExistingConversation() {
            if (!this.userId) return;

            try {
                const response = await fetch('/api/chat/conversation');
                const data = await response.json();
                if (data.conversation) {
                    this.conversation = data.conversation;
                    this.messages = data.messages;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading conversation:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.canSendMessage) return;

            try {
                const response = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        content: this.newMessage,
                        conversation_id: this.conversation?.id,
                        new_conversation: !this.conversation
                    })
                });

                const data = await response.json();
                
                if (!this.conversation) {
                    this.conversation = data.conversation;
                }
                
                this.messages.push(data.message);
                this.newMessage = '';
                this.scrollToBottom();
            } catch (error) {
                console.error('Error sending message:', error);
            }
        },

        setupEchoListeners() {
            if (!this.userId) return;

            Echo.private(`chat.${this.userId}`)
                .listen('NewMessage', (e) => {
                    this.messages.push(e.message);
                    this.scrollToBottom();
                })
                .listen('ConversationUpdated', (e) => {
                    this.conversation = e.conversation;
                });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        },

        formatDate(date) {
            return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
    
    function log(...args) {
        if (DEBUG) {
            console.log(...args);
        }
    }

    log('Script initialized');
    let conversationId = localStorage.getItem('conversationId');

    const messageButtons = document.querySelectorAll('[data-message]');
    log('Found message buttons:', messageButtons.length);

    messageButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            log('Button clicked');
            const message = this.dataset.message;
            log('Message to send:', message);
            sendMessage(message);
        });
    });

    async function sendMessage(content) {
        log('Attempting to send message:', content);
        try {
            const response = await fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    content: content,
                    conversation_id: conversationId,
                    new_conversation: !conversationId
                })
            });

            log('Response received');
            const data = await response.json();
            log('Response data:', data);
            
            if (data.success) {
                if (!conversationId) {
                    conversationId = data.conversation_id;
                    localStorage.setItem('conversationId', conversationId);
                    setupEchoListener(conversationId);
                }
                addMessageToUI(data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    function setupEchoListener(conversationId) {
        if (typeof window.Echo !== 'undefined' && conversationId) {
            console.log('Setting up Echo listener for conversation:', conversationId);
            
            window.Echo.channel(`chat.${conversationId}`)
                .listen('NewChatMessage', (e) => {
                    console.log('Received new message:', e);
                    addMessageToUI(e.message);
                });
        }
    }

    function addMessageToUI(message) {
        const messagesContainer = document.getElementById('messages');
        if (!messagesContainer) {
            console.error('Messages container not found');
            return;
        }

        const messageHtml = `
            <div class="message-custom-style flex flex-col items-end mb-4">
                <div class="message-bubble bg-blue-500 text-white rounded-[30px] px-6 py-3 max-w-[75%]">
                    <div class="text-right text-sm mb-1">Guest</div>
                    <div class="text-right text-sm">${message.content}</div>
                    <div class="text-right text-xs text-blue-100 mt-1">
                        ${new Date(message.created_at).toLocaleString('en-US', {
                            month: 'numeric',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        })}
                    </div>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Set up initial Echo listener if we have a conversation ID
    if (conversationId) {
        setupEchoListener(conversationId);
        console.log('Initial conversation ID:', conversationId);
    }
});
</script>
@endpush

@endsection