<div 
    x-data="lawyerActiveChats()"
    class="flex h-full"
    x-init="initActiveChats"
>
    <!-- Active Conversations List -->
    <div class="w-1/3 border-r">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">Active Conversations</h3>
        </div>
        <div class="overflow-y-auto h-[calc(100%-4rem)]">
            <template x-for="conversation in activeConversations" :key="conversation.id">
                <button
                    @click="selectConversation(conversation)"
                    class="w-full p-4 text-left hover:bg-gray-50 border-b"
                    :class="{'bg-blue-50': currentConversation?.id === conversation.id}"
                >
                    <div>
                        <p class="font-semibold" x-text="conversation.user.name"></p>
                        <p class="text-sm text-gray-500" x-text="formatLastMessage(conversation)"></p>
                        <p class="text-xs text-gray-400" x-text="formatDate(conversation.last_message_at)"></p>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1">
        <template x-if="currentConversation">
            <div class="h-full">
                <!-- Chat Header -->
                <div class="p-4 border-b bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold" x-text="currentConversation.user.name"></h3>
                            <p class="text-sm text-gray-500">
                                Active since: <span x-text="formatDate(currentConversation.updated_at)"></span>
                            </p>
                        </div>
                        <button 
                            @click="closeConversation(currentConversation)"
                            class="px-3 py-1 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600"
                        >
                            Close Chat
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div 
                    class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 h-[calc(100%-8rem)]"
                    x-ref="messagesContainer"
                >
                    <template x-for="message in messages" :key="message.id">
                        <div 
                            :class="message.user_id === userId ? 'ml-auto' : 'mr-auto'"
                            class="max-w-[80%]"
                        >
                            <div 
                                :class="message.user_id === userId ? 
                                    'bg-blue-500 text-white' : 
                                    'bg-white border border-gray-200'"
                                class="rounded-lg p-3 shadow-sm"
                            >
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-semibold" x-text="message.user.name"></p>
                                    <p class="text-sm break-words" x-text="message.content"></p>
                                    <p class="text-xs opacity-75" x-text="formatDate(message.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t bg-white">
                    <form @submit.prevent="sendMessage" class="flex gap-2">
                        <input 
                            type="text" 
                            x-model="newMessage"
                            class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            placeholder="Type your message..."
                            :disabled="loading"
                        >
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            :disabled="loading || !newMessage.trim()"
                        >
                            <span x-show="!loading">Send</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </template>
        <template x-if="!currentConversation">
            <div class="flex items-center justify-center h-full text-gray-500">
                Select a conversation to start chatting
            </div>
        </template>
    </div>
</div> 