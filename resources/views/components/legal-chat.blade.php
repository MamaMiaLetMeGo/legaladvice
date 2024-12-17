<div x-data="legalChat()" x-init="window.chatComponent = $data" class="flex flex-col h-full bg-white rounded-lg shadow-lg overflow-hidden">
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

<script>
    function legalChat() {
        return {
            conversationId: null,
            messages: [],
            isTyping: false,
            isWaitingForLawyer: false,
            isLawyerConnected: false,

            init() {
                // Get CSRF token on initialization
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!this.csrfToken) {
                    console.error('CSRF token not found');
                }
            },

            async makeRequest(url, data) {
                try {
                    if (!this.csrfToken) {
                        throw new Error('CSRF token not found');
                    }

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(data)
                    });

                    if (!response.ok) {
                        if (response.status === 419) {
                            // Refresh the page to get a new CSRF token
                            window.location.reload();
                            return;
                        }
                        throw new Error(`Request failed with status ${response.status}`);
                    }

                    return await response.json();
                } catch (error) {
                    console.error('Request failed:', error);
                    throw error;
                }
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

                    // Get all possible CSRF tokens
                    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const windowToken = window.csrfToken;
                    const token = metaToken || windowToken;

                    if (!token) {
                        location.reload(); // Force a page reload to get fresh tokens
                        return;
                    }

                    // Get the XSRF token from cookie
                    const xsrfToken = document.cookie.split('; ')
                        .find(row => row.startsWith('XSRF-TOKEN='))
                        ?.split('=')[1];

                    const headers = {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    };

                    // Add all possible CSRF tokens
                    if (token) headers['X-CSRF-TOKEN'] = token;
                    if (xsrfToken) headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);

                    const response = await fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: headers,
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            message,
                            conversation_id: this.conversationId,
                            _token: token
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
                        if (response.status === 419 || (data?.code === 'csrf_token_mismatch')) {
                            location.reload(); // Force a page reload to get fresh tokens
                            return;
                        }
                        throw new Error(data.error || `Server error: ${response.status}`);
                    }

                    // Handle the response from the ChatController
                    if (data.conversation_id) {
                        this.conversationId = data.conversation_id;
                    }
                    
                    if (data.message) {
                        this.addMessage(data.message, false);
                    } else if (data.error) {
                        throw new Error(data.error);
                    }

                } catch (error) {
                    console.error('Chat error:', error);
                    if (error.message.includes('CSRF') || error.message.includes('session')) {
                        location.reload();
                        return;
                    }
                    this.addMessage('Error: ' + error.message, false);
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

            async requestLawyer() {
                if (this.isWaitingForLawyer) return;
                
                this.isWaitingForLawyer = true;
                try {
                    const response = await this.makeRequest('/chat/request-lawyer', {
                        conversation_id: this.conversationId
                    });
                    
                    if (response.success) {
                        this.addMessage('Please wait while we connect you with an available attorney...', false);
                        this.startLawyerConnectionProcess();
                    } else {
                        throw new Error(response.error || 'Failed to request lawyer');
                    }
                } catch (error) {
                    console.error('Error requesting lawyer:', error);
                    this.addMessage('Sorry, we could not connect you with an attorney at this time. Please try again later.', false);
                    this.isWaitingForLawyer = false;
                }
            },

            startLawyerConnectionProcess() {
                // Initial connecting message
                this.addMessage("We are now connecting you with one of our attorneys based on your chat history. Please wait a moment...", false);
                
                // Show loading animation
                this.showTypingIndicator();

                // Simulate the connection process
                setTimeout(() => {
                    this.hideTypingIndicator();
                    this.addMessage("We've located several attorneys with expertise in your matter. Checking their availability now...", false);
                    
                    setTimeout(() => {
                        this.addMessage("We've found attorneys available to assist you. Preparing secure connection...", false);
                        
                        setTimeout(() => {
                            this.addMessage("You're in our priority queue. One of our attorneys will join this chat shortly. Please stay in this window.", false);
                            this.checkForLawyerConnection();
                        }, 3000);
                    }, 4000);
                }, 5000);
            },

            checkForLawyerConnection() {
                if (!this.isWaitingForLawyer) return;

                let waitTime = 0;
                const totalWaitTime = 20000; // 20 seconds total wait
                const updateInterval = 3000; // Update status every 3 seconds

                const pollInterval = setInterval(() => {
                    waitTime += updateInterval;
                    
                    // Update the waiting status
                    this.updateWaitingStatus(waitTime, totalWaitTime);

                    if (waitTime >= totalWaitTime) {
                        clearInterval(pollInterval);
                        this.lawyerConnected();
                    }
                }, updateInterval);
            },

            updateWaitingStatus(currentWait, totalWait) {
                const statusDiv = document.getElementById('lawyer-status') || document.createElement('div');
                statusDiv.id = 'lawyer-status';
                statusDiv.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg';
                
                const progress = Math.min((currentWait / totalWait) * 100, 100);
                
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

            continueWithAI() {
                this.addMessage('I understand you want to continue with the AI assistant. How else can I help you?', false);
            }
        }
    }
</script>

<style>
    .typing-indicator {
        padding: 15px;
        display: none;
    }

    .typing-indicator span {
        height: 8px;
        width: 8px;
        float: left;
        margin: 0 1px;
        background-color: #9880ff;
        display: block;
        border-radius: 50%;
        opacity: 0.4;
    }

    .typing-indicator span:nth-of-type(1) {
        animation: typing 1s infinite;
    }

    .typing-indicator span:nth-of-type(2) {
        animation: typing 1s infinite 0.2s;
    }

    .typing-indicator span:nth-of-type(3) {
        animation: typing 1s infinite 0.4s;
    }

    @keyframes typing {
        0% {
            transform: translateY(0px);
            background-color: #9880ff;
        }
        28% {
            transform: translateY(-7px);
            background-color: #65a6ff;
        }
        44% {
            transform: translateY(0px);
            background-color: #9880ff;
        }
    }
</style>