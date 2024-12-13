@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Lawyer Dashboard</h2>
                    
                    <!-- Pending Conversations -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Pending Conversations</h3>
                        <div id="pending-conversations" class="space-y-4">
                            <!-- Conversations will be loaded here -->
                        </div>
                    </div>

                    <!-- Active Conversations -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Your Active Conversations</h3>
                        <div id="active-conversations" class="space-y-4">
                            <!-- Active conversations will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function loadConversations() {
            // Load pending conversations
            fetch('/lawyer/pending-conversations')
                .then(response => response.json())
                .then(data => {
                    const pendingContainer = document.getElementById('pending-conversations');
                    pendingContainer.innerHTML = data.map(conv => `
                        <div class="p-4 border rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Conversation #${conv.id}</p>
                                    <p class="text-sm text-gray-600">Started: ${new Date(conv.created_at).toLocaleString()}</p>
                                </div>
                                <button 
                                    onclick="claimConversation(${conv.id})"
                                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                                >
                                    Claim
                                </button>
                            </div>
                        </div>
                    `).join('');
                });

            // Load active conversations
            fetch('/lawyer/active-conversations')
                .then(response => response.json())
                .then(data => {
                    const activeContainer = document.getElementById('active-conversations');
                    activeContainer.innerHTML = data.map(conv => `
                        <div class="p-4 border rounded-lg">
                            <p class="font-medium">Conversation #${conv.id}</p>
                            <p class="text-sm text-gray-600">Started: ${new Date(conv.created_at).toLocaleString()}</p>
                            <a 
                                href="/lawyer/conversation/${conv.id}" 
                                class="mt-2 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                            >
                                View Conversation
                            </a>
                        </div>
                    `).join('');
                });
        }

        function claimConversation(id) {
            const button = event.target;
            button.disabled = true; // Prevent double clicks
            
            fetch(`/lawyer/claim-conversation/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = `/lawyer/conversation/${id}`;
                } else {
                    throw new Error(data.error || 'Failed to claim conversation');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to claim conversation. Please try again.');
                button.disabled = false; // Re-enable the button on error
            });
        }

        // Load conversations on page load
        loadConversations();

        // Refresh every 30 seconds
        setInterval(loadConversations, 30000);
    </script>
    @endpush
@endsection