<div 
    x-data="notifications"
    @notification.window="add($event.detail)"
    class="fixed bottom-4 right-4 z-50 space-y-4"
>
    <template x-for="notification in notifications" :key="notification.id">
        <div 
            x-show="notification.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-8"
            class="bg-white rounded-lg shadow-lg p-4 max-w-sm w-full border-l-4 border-blue-500"
        >
            <div class="flex items-start">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                    <p class="mt-1 text-sm text-gray-500" x-text="notification.message"></p>
                </div>
                <button 
                    @click="remove(notification.id)" 
                    class="ml-4 text-gray-400 hover:text-gray-500"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div> 