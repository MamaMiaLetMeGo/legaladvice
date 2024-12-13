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
                        <h2 class="text-white text-lg font-semibold">Chat with a Legal Expert</h2>
                        <p x-show="conversation" class="text-blue-100 text-sm">
                            <span x-show="conversation && conversation.status === 'pending'">Waiting for a lawyer...</span>
                            <span x-show="conversation && conversation.status === 'active'">Connected with <span x-text="conversation?.lawyer?.name"></span></span>
                        </p>
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div 
                        class="bg-gray-50 h-80 p-4 overflow-y-auto"
                        x-ref="messagesContainer"
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
                                    <p class="text-gray-800">Hello! What legal matter can I help you with today?</p>
                                </div>
                            </div>

                            <!-- Dynamic Messages -->
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex items-start" :class="{'justify-end': message.user_id === userId}">
                                    <div class="flex items-start" :class="{'flex-row-reverse': message.user_id === userId}">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-gray-300"></div>
                                        </div>
                                        <div class="mx-3" :class="{'bg-blue-500 text-white': message.user_id === userId, 'bg-white text-gray-800': message.user_id !== userId}">
                                            <div class="rounded-lg py-2 px-4 shadow-sm">
                                                <p x-text="message.content"></p>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500" x-text="formatDate(message.created_at)"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Chat Input Area -->
                    <div class="p-4 bg-white border-t">
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
</script>
@endpush

@endsection