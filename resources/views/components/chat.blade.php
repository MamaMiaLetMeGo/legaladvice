<div 
    x-data="chat()"
    class="flex flex-col h-full bg-white rounded-lg shadow-lg"
    x-init="initChat"
>
    <!-- Chat Header -->
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Chat with a Lawyer</h3>
    </div>

    <!-- Messages -->
    <div 
        class="flex-1 overflow-y-auto p-4 space-y-4"
        x-ref="messagesContainer"
    >
        <template x-for="message in messages" :key="message.id">
            <div 
                :class="message.user_id === userId ? 'ml-auto bg-blue-500 text-white' : 'mr-auto bg-gray-100'"
                class="max-w-[80%] rounded-lg p-3"
            >
                <div class="flex items-start gap-2">
                    <div>
                        <p class="text-sm font-semibold" x-text="message.user.name"></p>
                        <p class="text-sm" x-text="message.content"></p>
                        <p class="text-xs mt-1 opacity-75" x-text="formatDate(message.created_at)"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Input Area -->
    <div class="p-4 border-t">
        <form @submit.prevent="sendMessage" class="flex gap-2">
            <input 
                type="text" 
                x-model="newMessage"
                class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Type your message..."
                :disabled="loading"
            >
            <button 
                type="submit"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                :disabled="loading || !newMessage.trim()"
            >
                <span x-show="!loading">Send</span>
                <span x-show="loading">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </form>
    </div>
</div>