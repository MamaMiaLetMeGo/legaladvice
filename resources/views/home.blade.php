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
                <div class="max-w-4xl mx-auto px-4 py-8">
                    <x-legal-chat />
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
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                console.log('Using CSRF Token:', csrfToken); // For debugging

                const response = await fetch('/api/chat/send', {
                    method: 'POST',
                    credentials: 'same-origin', // Important for cookies
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.newMessage,
                        conversation_id: this.conversationId,
                        new_conversation: !this.conversationId
                    })
                });

                // Improved error handling
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

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
                console.error('Error details:', {
                    message: error.message,
                    response: error.response,
                    stack: error.stack
                });
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