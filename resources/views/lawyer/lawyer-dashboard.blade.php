@extends('layouts.app')

@section('content')
    <div class="py-6" x-data="lawyerDashboard">
        <!-- Stats Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                            <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Active Chats</h2>
                            <p class="text-2xl font-semibold text-gray-800" x-text="stats.activeChats">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                            <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Pending Chats</h2>
                            <p class="text-2xl font-semibold text-gray-800" x-text="stats.pendingChats">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Closed Chats</h2>
                            <p class="text-2xl font-semibold text-gray-800" x-text="stats.closedChats">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                            <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Chats</h2>
                            <p class="text-2xl font-semibold text-gray-800" x-text="stats.totalChats">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg">
                <!-- Actions Bar -->
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex space-x-2">
                        <button 
                            @click="bulkDelete"
                            x-show="selectedConversations.length > 0"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Delete Selected
                        </button>
                        <button 
                            @click="bulkClose"
                            x-show="selectedConversations.length > 0"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                            Close Selected
                        </button>
                    </div>
                    <div class="flex items-center space-x-4">
                        <select x-model="statusFilter" class="rounded-md border-gray-300">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="closed">Closed</option>
                        </select>
                        <input 
                            type="text" 
                            x-model="search" 
                            placeholder="Search conversations..." 
                            class="rounded-md border-gray-300"
                        >
                    </div>
                </div>

                <!-- Conversations Table -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input 
                                    type="checkbox" 
                                    @click="toggleAll"
                                    :checked="selectedConversations.length === filteredConversations.length"
                                    class="rounded border-gray-300 text-blue-600"
                                >
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="conversation in filteredConversations" :key="conversation.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input 
                                        type="checkbox" 
                                        :value="conversation.id" 
                                        x-model="selectedConversations"
                                        class="rounded border-gray-300 text-blue-600"
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="conversation.user.name"></div>
                                            <div class="text-sm text-gray-500" x-text="conversation.user.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': conversation.status === 'active',
                                            'bg-yellow-100 text-yellow-800': conversation.status === 'pending',
                                            'bg-gray-100 text-gray-800': conversation.status === 'closed'
                                        }"
                                        x-text="conversation.status"
                                    ></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div x-text="conversation.last_message?.content || 'No messages'"></div>
                                    <div class="text-xs text-gray-400" x-text="formatDate(conversation.last_message_at)"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(conversation.created_at)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a :href="`/lawyer/conversation/${conversation.id}`" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <button @click="deleteConversation(conversation.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('lawyerDashboard', () => ({
                conversations: @json($conversations),
                selectedConversations: [],
                statusFilter: '',
                search: '',
                stats: {
                    activeChats: 0,
                    totalChats: 0,
                    pendingChats: 0,
                    closedChats: 0
                },

                init() {
                    this.updateStats();
                },

                get filteredConversations() {
                    return this.conversations.filter(c => {
                        const matchesStatus = !this.statusFilter || c.status === this.statusFilter;
                        const matchesSearch = !this.search || 
                            c.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            c.user.email.toLowerCase().includes(this.search.toLowerCase());
                        return matchesStatus && matchesSearch;
                    });
                },

                async updateStats() {
                    try {
                        const response = await fetch('/lawyer/stats');
                        this.stats = await response.json();
                    } catch (error) {
                        console.error('Error fetching stats:', error);
                    }
                },

                toggleAll() {
                    if (this.selectedConversations.length === this.filteredConversations.length) {
                        this.selectedConversations = [];
                    } else {
                        this.selectedConversations = this.filteredConversations.map(c => c.id);
                    }
                },

                async bulkDelete() {
                    if (!confirm('Are you sure you want to delete these conversations?')) return;

                    try {
                        const response = await fetch('/lawyer/conversations/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                conversation_ids: this.selectedConversations
                            })
                        });

                        if (response.ok) {
                            this.conversations = this.conversations.filter(c => !this.selectedConversations.includes(c.id));
                            this.selectedConversations = [];
                            this.updateStats();
                        }
                    } catch (error) {
                        console.error('Error deleting conversations:', error);
                    }
                },

                async bulkClose() {
                    if (!confirm('Are you sure you want to close these conversations?')) return;

                    try {
                        const response = await fetch('/lawyer/conversations/bulk-close', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                conversation_ids: this.selectedConversations
                            })
                        });

                        if (response.ok) {
                            this.conversations = this.conversations.map(c => {
                                if (this.selectedConversations.includes(c.id)) {
                                    return { ...c, status: 'closed' };
                                }
                                return c;
                            });
                            this.selectedConversations = [];
                            this.updateStats();
                        }
                    } catch (error) {
                        console.error('Error closing conversations:', error);
                    }
                },

                async deleteConversation(id) {
                    if (!confirm('Are you sure you want to delete this conversation?')) return;

                    try {
                        const response = await fetch(`/lawyer/conversations/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.conversations = this.conversations.filter(c => c.id !== id);
                            this.updateStats();
                        }
                    } catch (error) {
                        console.error('Error deleting conversation:', error);
                    }
                },

                formatDate(date) {
                    if (!date) return '';
                    return new Date(date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }));
        });
    </script>
    @endpush
@endsection 