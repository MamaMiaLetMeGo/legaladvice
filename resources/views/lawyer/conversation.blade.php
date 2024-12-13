@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">
                            Conversation #{{ $conversation->id }}
                            <span class="text-sm text-gray-500">
                                with {{ $conversation->user ? $conversation->user->name : 'Anonymous User' }}
                            </span>
                        </h2>
                        <a href="{{ route('lawyer.dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            Back to Dashboard
                        </a>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 h-96 overflow-y-auto" id="messages">
                        @foreach($conversation->messages as $message)
                            <div class="mb-4 {{ $message->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                <div class="inline-block max-w-3/4 {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200' }} rounded-lg px-4 py-2">
                                    @if($message->system_message)
                                        <p class="text-sm italic">{{ $message->content }}</p>
                                    @else
                                        <p class="text-sm font-semibold mb-1">
                                            {{ $message->user ? $message->user->name : 'Anonymous User' }}
                                        </p>
                                        <p>{{ $message->content }}</p>
                                    @endif
                                    <p class="text-xs {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }} mt-1">
                                        {{ $message->created_at->format('M j, g:i a') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form class="mt-4" onsubmit="sendMessage(event)">
                        <div class="flex gap-4">
                            <input 
                                type="text" 
                                id="message-input"
                                name="message"
                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                placeholder="Type your message..."
                            >
                            <button 
                                type="submit"
                                id="send-message"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition"
                            >
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chat', () => ({
                newMessage: '',
                conversationId: {{ $conversation->id }},

                init() {
                    this.scrollToBottom();
                    this.listenForMessages();
                },

                scrollToBottom() {
                    const container = document.getElementById('messages');
                    container.scrollTop = container.scrollHeight;
                },

                async sendMessage() {
                    if (!this.newMessage.trim()) return;

                    try {
                        const response = await fetch('/api/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                content: this.newMessage,
                                conversation_id: this.conversationId,
                                new_conversation: false
                            })
                        });

                        if (!response.ok) throw new Error('Failed to send message');

                        this.newMessage = '';
                        this.scrollToBottom();
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please try again.');
                    }
                },

                listenForMessages() {
                    Echo.private(`chat.${this.conversationId}`)
                        .listen('NewChatMessage', (e) => {
                            // Append new message to the chat
                            const messagesContainer = document.getElementById('messages');
                            const messageDiv = document.createElement('div');
                            messageDiv.className = `mb-4 ${e.message.user_id === {{ auth()->id() }} ? 'text-right' : 'text-left'}`;
                            messageDiv.innerHTML = `
                                <div class="inline-block max-w-3/4 ${e.message.user_id === {{ auth()->id() }} ? 'bg-blue-500 text-white' : 'bg-gray-200'} rounded-lg px-4 py-2">
                                    <p class="text-sm font-semibold mb-1">${e.message.user ? e.message.user.name : 'Anonymous User'}</p>
                                    <p>${e.message.content}</p>
                                    <p class="text-xs ${e.message.user_id === {{ auth()->id() }} ? 'text-blue-100' : 'text-gray-500'} mt-1">
                                        ${new Date(e.message.created_at).toLocaleString()}
                                    </p>
                                </div>
                            `;
                            messagesContainer.appendChild(messageDiv);
                            this.scrollToBottom();
                        });
                }
            }));
        });

        function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            
            if (!message) return;

            fetch('/lawyer/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    conversation_id: {{ $conversation->id }},
                    content: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear input
                    messageInput.value = '';
                    
                    // Add message to UI immediately
                    const messagesContainer = document.getElementById('messages');
                    const messageHtml = `
                        <div class="mb-4 text-right">
                            <div class="inline-block max-w-3/4 bg-blue-500 text-white rounded-lg px-4 py-2">
                                <p class="text-sm font-semibold mb-1">{{ auth()->user()->name }}</p>
                                <p>${message}</p>
                                <p class="text-xs text-blue-100 mt-1">
                                    ${new Date().toLocaleString()}
                                </p>
                            </div>
                        </div>
                    `;
                    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                    
                    // Scroll to bottom
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send message. Please try again.');
            });
        }
    </script>
    @endpush
@endsection 