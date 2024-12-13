@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Pending Conversations List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Pending Requests</h2>
            <div 
                x-data="pendingConversations()"
                class="space-y-4"
            >
                <template x-for="conversation in conversations" :key="conversation.id">
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium" x-text="conversation.user.name"></p>
                                <p class="text-sm text-gray-500" x-text="formatDate(conversation.created_at)"></p>
                            </div>
                            <button 
                                @click="claimConversation(conversation.id)"
                                class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700"
                            >
                                Claim
                            </button>
                        </div>
                        <p class="text-gray-600 text-sm" x-text="getLastMessage(conversation)"></p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Active Conversations -->
        <div class="md:col-span-2">
            <div 
                x-data="activeChat()"
                class="bg-white rounded-lg shadow-md h-[600px] flex flex-col"
            >
                <!-- Chat Header -->
                <div class="p-4 border-b">
                    <template x-if="currentConversation">
                        <div>
                            <h3 class="font-semibold" x-text="currentConversation.user.name"></h3>
                            <p class="text-sm text-gray-500">Started <span x-text="formatDate(currentConversation.created_at)"></span></p>
                        </div>
                    </template>
                    <template x-if="!currentConversation">
                        <p class="text-gray-500">Select a conversation to start chatting</p>
                    </template>
                </div>

                <!-- Messages Area -->
                <div 
                    class="flex-1 overflow-y-auto p-4 space-y-4"
                    x-ref="messagesContainer"
                >
                    <template x-for="message in messages" :key="message.id">
                        <div class="flex items-start" :class="{'justify-end': message.user_id === userId}">
                            <div class="flex items-start" :class="{'flex-row-reverse': message.user_id === userId}">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-300"></div>
                                </div>
                                <div class="mx-3">
                                    <div 
                                        class="rounded-lg py-2 px-4 shadow-sm"
                                        :class="{'bg-blue-500 text-white': message.user_id === userId, 'bg-white text-gray-800': message.user_id !== userId}"
                                    >
                                        <p x-text="message.content"></p>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500" x-text="formatDate(message.created_at)"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t">
                    <form @submit.prevent="sendMessage" class="flex space-x-3">
                        <input 
                            type="text" 
                            x-model="newMessage"
                            placeholder="Type your message..." 
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                            :disabled="!currentConversation"
                        >
                        <button 
                            type="submit"
                            class="bg-blue-600 text-white rounded-lg px-4 py-2 font-semibold hover:bg-blue-700 transition duration-300"
                            :disabled="!currentConversation"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function pendingConversations() {
    return {
        conversations: [],
        
        init() {
            this.loadPendingConversations();
            this.setupEchoListeners();
        },

        async loadPendingConversations() {
            try {
                const response = await fetch('/api/lawyer/pending-conversations');
                this.conversations = await response.json();
            } catch (error) {
                console.error('Error loading pending conversations:', error);
            }
        },

        async claimConversation(conversationId) {
            try {
                const response = await fetch(`/api/lawyer/claim-conversation/${conversationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.conversations = this.conversations.filter(c => c.id !== conversationId);
                    // Emit event to update active chat
                    window.dispatchEvent(new CustomEvent('conversation-claimed', {
                        detail: await response.json()
                    }));
                }
            } catch (error) {
                console.error('Error claiming conversation:', error);
            }
        },

        setupEchoListeners() {
            Echo.private('pending-conversations')
                .listen('NewConversation', (e) => {
                    this.conversations.unshift(e.conversation);
                })
                .listen('ConversationClaimed', (e) => {
                    this.conversations = this.conversations.filter(c => c.id !== e.conversation.id);
                });
        },

        getLastMessage(conversation) {
            return conversation.messages[0]?.content ?? 'No messages yet';
        },

        formatDate(date) {
            return new Date(date).toLocaleString();
        }
    }
}

function activeChat() {
    return {
        currentConversation: null,
        messages: [],
        newMessage: '',
        userId: {{ auth()->id() }},

        init() {
            this.setupEventListeners();
            this.loadActiveConversations();
        },

        setupEventListeners() {
            window.addEventListener('conversation-claimed', (event) => {
                this.setCurrentConversation(event.detail.conversation);
            });
        },

        async loadActiveConversations() {
            try {
                const response = await fetch('/api/lawyer/active-conversations');
                const conversations = await response.json();
                if (conversations.length > 0) {
                    this.setCurrentConversation(conversations[0]);
                }
            } catch (error) {
                console.error('Error loading active conversations:', error);
            }
        },

        async setCurrentConversation(conversation) {
            this.currentConversation = conversation;
            const response = await fetch(`/api/chat/messages/${conversation.id}`);
            this.messages = (await response.json()).messages;
            this.scrollToBottom();
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.currentConversation) return;

            try {
                const response = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        content: this.newMessage,
                        conversation_id: this.currentConversation.id
                    })
                });

                const message = await response.json();
                this.messages.push(message);
                this.newMessage = '';
                this.scrollToBottom();
            } catch (error) {
                console.error('Error sending message:', error);
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        },

        formatDate(date) {
            return new Date(date).toLocaleString();
        }
    }
}
</script>
@endpush
@endsection