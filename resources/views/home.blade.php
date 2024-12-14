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
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Family Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Family
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Criminal Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Criminal
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Business Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Business
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Real Estate Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Real Estate
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Immigration Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Immigration
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Employment Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Employment
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with Personal Injury Law" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                Personal Injury
                            </button>
                            <button 
                                @click="handleQuickMessage($el.dataset.message)" 
                                data-message="I need help with another legal matter" 
                                class="px-3 py-1 text-xs bg-blue-500 hover:bg-blue-400 text-white rounded-full"
                            >
                                More
                            </button>
                        </div>
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div id="messages" class="flex-1 overflow-y-auto p-4">
                        <!-- Welcome message -->
                        <div class="flex items-start mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 bg-white rounded-lg py-3 px-6 shadow-sm max-w-[75%]">
                                <p class="text-gray-800 text-lg">Hello! What legal matter can I help you with right now?</p>
                            </div>
                        </div>

                        <!-- Dynamic Messages Container -->
                        <div id="dynamic-messages">
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex flex-col items-end mb-6">
                                    <div class="bg-blue-500 text-white rounded-[30px] px-8 py-4 max-w-[75%]">
                                        <div class="text-right text-base font-medium mb-2">Guest</div>
                                        <div class="text-right text-lg" x-text="message.content"></div>
                                        <div class="text-right text-sm text-blue-100 mt-2" 
                                             x-text="new Date(message.created_at).toLocaleString('en-US', {
                                                 year: 'numeric',
                                                 month: 'numeric',
                                                 day: 'numeric',
                                                 hour: 'numeric',
                                                 minute: '2-digit',
                                                 hour12: true
                                             })">
                                        </div>
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

document.addEventListener('alpine:init', () => {
    Alpine.data('chat', () => ({
        messages: [],
        newMessage: '',
        conversation: null,
        conversationId: localStorage.getItem('conversationId'),

        init() {
            if (this.conversationId) {
                this.setupEchoListener(this.conversationId);
            }
            
            // Remove the separate button click handlers since we'll handle it through x-on:click
        },

        handleQuickMessage(message) {
            this.newMessage = message;
            this.sendMessage();
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            try {
                const response = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.newMessage,
                        conversation_id: this.conversationId,
                        new_conversation: !this.conversationId
                    })
                });

                const data = await response.json();
                
                if (!this.conversationId) {
                    this.conversationId = data.conversation_id;
                    localStorage.setItem('conversationId', this.conversationId);
                    this.setupEchoListener(this.conversationId);
                }

                this.messages = [...this.messages, data.message];
                this.newMessage = '';
                this.$nextTick(() => this.scrollToBottom());
                
            } catch (error) {
                console.error('Error sending message:', error);
            }
        },

        setupEchoListener(conversationId) {
            if (typeof window.Echo !== 'undefined' && conversationId) {
                window.Echo.private(`chat.conversation.${conversationId}`)
                    .listen('NewChatMessage', (e) => {
                        // Add new message to the end of the array
                        this.messages = [...this.messages, e.message];
                        this.$nextTick(() => this.scrollToBottom());
                    });
            }
        },

        scrollToBottom() {
            const messagesContainer = document.getElementById('messages');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
    }));
});
</script>
@endpush

@endsection