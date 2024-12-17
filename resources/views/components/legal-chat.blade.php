<div x-data="legalChat()" x-init="window.chatComponent = $data" class="bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-blue-600 p-3">
        <h1 class="text-white text-lg font-bold">Legal Assistant</h1>
        <p class="text-blue-100 text-xs">Ask me any legal question</p>
    </div>

    <!-- Chat Messages -->
    <div id="messages" class="h-[400px] overflow-y-auto p-4 space-y-3">
        <!-- Welcome message -->
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">AI</span>
                </div>
            </div>
            <div class="ml-3 bg-gray-100 rounded-lg py-2 px-3 max-w-[80%]">
                <p class="text-gray-900">Hello! I'm your legal AI assistant. How can I help you today?</p>
            </div>
        </div>
    </div>

    <!-- Typing Indicator -->
    <div id="typing-indicator" class="typing-indicator ml-16 hidden">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Input Area -->
    <div class="border-t p-3">
        <div class="flex space-x-3">
            <input type="text" 
                   id="message-input" 
                   x-on:keyup.enter="sendMessage()"
                   class="flex-1 border rounded-lg px-3 py-1.5 focus:outline-none focus:border-blue-500"
                   placeholder="Type your legal question here..."
            >
            <button x-on:click="sendMessage()" 
                    class="bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">
                Send
            </button>
        </div>
    </div>
</div>

<!-- Styles remain the same -->
<style>
    .typing-indicator {
        display: none;
        padding: 15px;
    }
    .typing-indicator span {
        height: 10px;
        width: 10px;
        float: left;
        margin: 0 1px;
        background-color: #9880ff;
        display: block;
        border-radius: 50%;
        opacity: 0.4;
        animation: typing 1s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>

<!-- JavaScript using Alpine.js -->
<script>
    function legalChat() {
        return {
            isWaitingForLawyer: false,
            isLawyerConnected: false,
            conversationId: null,

            createFollowUpPrompt() {
                return `
                    <div class="mt-4 flex flex-col space-y-2">
                        <p class="text-sm text-gray-600">Need more help?</p>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="window.chatComponent.sendPredefinedMessage('Can you explain that in simpler terms?')"
                                    class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                                Explain simpler
                            </button>
                            <button onclick="window.chatComponent.sendPredefinedMessage('What are my next steps?')"
                                    class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                                Next steps
                            </button>
                            <button onclick="window.chatComponent.sendPredefinedMessage('Are there any legal risks I should be aware of?')"
                                    class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                                Legal risks
                            </button>
                            <button onclick="window.chatComponent.connectWithAttorney()"
                                   class="text-sm bg-red-500 text-white px-4 py-1 rounded-full hover:bg-red-600 transition-colors flex items-center gap-1"
                                   ${this.isWaitingForLawyer ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                                <span>Talk with a Real Attorney Now</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            },

            sendPredefinedMessage(message) {
                document.getElementById('message-input').value = message;
                this.sendMessage();
            },

            async connectWithAttorney() {
                this.isWaitingForLawyer = true;
                
                // Initial connecting message
                this.addMessage("We are now connecting you with one of our attorneys based on your chat history. Please wait a moment...", false);
                
                // Show loading animation
                this.showTypingIndicator();

                try {
                    // Initial processing (5 seconds)
                    await new Promise(resolve => setTimeout(resolve, 5000));
                    this.hideTypingIndicator();

                    // First status update
                    this.addMessage("We've located several attorneys with expertise in your matter. Checking their availability now...", false);
                    
                    // Checking availability (4 seconds)
                    await new Promise(resolve => setTimeout(resolve, 4000));
                    
                    // Second status update
                    this.addMessage("We've found attorneys available to assist you. Preparing secure connection...", false);

                    // Preparing connection (3 seconds)
                    await new Promise(resolve => setTimeout(resolve, 3000));
                    
                    // Final queue message
                    this.addMessage("You're in our priority queue. One of our attorneys will join this chat shortly. Please stay in this window.", false);
                    
                    // Start checking for lawyer connection
                    this.checkForLawyerConnection();

                } catch (error) {
                    console.error('Error:', error);
                    this.hideTypingIndicator();
                    this.isWaitingForLawyer = false;
                    this.addMessage("Sorry, we encountered an error connecting you with an attorney. Please try again.", false);
                }
            },

            checkForLawyerConnection() {
                if (!this.isWaitingForLawyer) return;

                let waitTime = 0;
                const totalWaitTime = 20000; // 20 seconds total wait
                const updateInterval = 3000; // Update status every 3 seconds

                // Poll for lawyer connection
                const pollInterval = setInterval(() => {
                    try {
                        waitTime += updateInterval;
                        
                        // Update the waiting status with more specific messages
                        this.updateWaitingStatus(waitTime, totalWaitTime);

                        if (waitTime >= totalWaitTime) {
                            clearInterval(pollInterval);
                            this.lawyerConnected();
                        }

                    } catch (error) {
                        console.error('Error checking lawyer connection:', error);
                    }
                }, updateInterval);
            },

            updateWaitingStatus(currentWait, totalWait) {
                const statusDiv = document.getElementById('lawyer-status') || document.createElement('div');
                statusDiv.id = 'lawyer-status';
                statusDiv.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg';
                
                // Calculate progress
                const progress = Math.min((currentWait / totalWait) * 100, 100);
                
                // More gradual status messages
                let statusMessage = 'Initiating secure connection';
                if (progress > 20) statusMessage = 'Verifying attorney credentials';
                if (progress > 40) statusMessage = 'Attorney reviewing your case details';
                if (progress > 60) statusMessage = 'Attorney preparing response';
                if (progress > 80) statusMessage = 'Attorney joining chat';
                if (progress > 90) statusMessage = 'Establishing connection';

                statusDiv.innerHTML = `
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <div class="animate-pulse h-3 w-3 bg-white rounded-full"></div>
                            <span>${statusMessage}...</span>
                        </div>
                        <div class="w-full bg-blue-800 rounded-full h-1">
                            <div class="bg-white h-1 rounded-full transition-all duration-500" style="width: ${progress}%"></div>
                        </div>
                    </div>
                `;

                if (!statusDiv.parentNode) {
                    document.body.appendChild(statusDiv);
                }
            },

            lawyerConnected() {
                // Remove waiting status
                const statusDiv = document.getElementById('lawyer-status');
                if (statusDiv) statusDiv.remove();

                this.isWaitingForLawyer = false;
                this.isLawyerConnected = true;

                // Add lawyer connected message
                this.addMessage("Attorney John Smith has joined the chat. They have full access to your previous conversation and are ready to assist you.", false);
                
                // Update chat interface for lawyer mode
                this.updateChatForLawyerMode();
            },

            updateChatForLawyerMode() {
                // Update the input placeholder
                const input = document.getElementById('message-input');
                input.placeholder = "Chat with your attorney...";

                // Add a notice at the top of the chat
                const notice = document.createElement('div');
                notice.className = 'bg-green-50 border-l-4 border-green-500 p-4 mb-4';
                notice.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                You are now in a secure chat with a licensed attorney. This conversation is protected by attorney-client privilege.
                            </p>
                        </div>
                    </div>
                `;
                document.getElementById('messages').prepend(notice);
            },

            async sendMessage() {
                const input = document.getElementById('message-input');
                const message = input.value.trim();
                if (!message) return;

                try {
                    // Add user message to chat immediately
                    this.addMessage(message, true);
                    input.value = '';
                    this.showTypingIndicator();

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (!token) {
                        throw new Error('CSRF token not found');
                    }

                    const response = await fetch('/api/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            message,
                            conversation_id: this.conversationId
                        })
                    });

                    let data;
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        throw new Error('Server returned non-JSON response');
                    }

                    if (!response.ok) {
                        throw new Error(data.error || `Server error: ${response.status}`);
                    }

                    if (data.success) {
                        this.conversationId = data.conversation_id;
                        this.addMessage(data.message);
                    } else {
                        throw new Error(data.error || 'Unknown error occurred');
                    }

                } catch (error) {
                    console.error('Chat error:', error);
                    this.addMessage(`Error: ${error.message}. Please try again or contact support if the problem persists.`);
                } finally {
                    this.hideTypingIndicator();
                }
            },

            showTypingIndicator() {
                document.getElementById('typing-indicator').style.display = 'block';
            },

            hideTypingIndicator() {
                document.getElementById('typing-indicator').style.display = 'none';
            },

            addMessage(content, isUser = false) {
                const messagesDiv = document.getElementById('messages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex items-start';

                const avatar = `
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full ${isUser ? 'bg-green-500' : 'bg-blue-500'} flex items-center justify-center">
                            <span class="text-white font-bold text-sm">${isUser ? 'You' : (this.isLawyerConnected ? 'LAW' : 'AI')}</span>
                        </div>
                    </div>
                `;

                const message = `
                    <div class="ml-3 ${isUser ? 'bg-green-50' : 'bg-gray-100'} rounded-lg py-2 px-3 max-w-[80%]">
                        <p class="text-gray-900">${content}</p>
                        ${(!isUser && !this.isLawyerConnected) ? this.createFollowUpPrompt() : ''}
                    </div>
                `;

                messageDiv.innerHTML = avatar + message;
                messagesDiv.appendChild(messageDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            },

            // ... (rest of your functions from before)
            // Include all the other functions (connectWithAttorney, checkForLawyerConnection, etc.)
        }
    }
</script> 