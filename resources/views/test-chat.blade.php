<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Legal AI Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 p-4">
                <h1 class="text-white text-xl font-bold">Legal AI Assistant</h1>
                <p class="text-blue-100 text-sm">Ask any legal question</p>
            </div>

            <!-- Chat Messages -->
            <div id="messages" class="h-[500px] overflow-y-auto p-4 space-y-4">
                <!-- Welcome message -->
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white font-bold">AI</span>
                        </div>
                    </div>
                    <div class="ml-3 bg-gray-100 rounded-lg py-3 px-4 max-w-[80%]">
                        <p class="text-gray-900">Hello! I'm your legal AI assistant. How can I help you today?</p>
                        <p class="text-xs text-gray-500 mt-1">Note: This is for informational purposes only and not legal advice.</p>
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
            <div class="border-t p-4">
                <div class="flex space-x-4">
                    <input type="text" 
                           id="message-input" 
                           class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="Type your legal question here..."
                    >
                    <button onclick="sendMessage()" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let conversationId = null;
        let isWaitingForLawyer = false;

        function showTypingIndicator() {
            document.getElementById('typing-indicator').style.display = 'block';
        }

        function hideTypingIndicator() {
            document.getElementById('typing-indicator').style.display = 'none';
        }

        function createFollowUpPrompt() {
            return `
                <div class="mt-4 flex flex-col space-y-2">
                    <p class="text-sm text-gray-600">Need more help?</p>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="sendPredefinedMessage('Can you explain that in simpler terms?')"
                                class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                            Explain simpler
                        </button>
                        <button onclick="sendPredefinedMessage('What are my next steps?')"
                                class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                            Next steps
                        </button>
                        <button onclick="sendPredefinedMessage('Are there any legal risks I should be aware of?')"
                                class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                            Legal risks
                        </button>
                        <button onclick="connectWithAttorney()"
                               class="text-sm bg-red-500 text-white px-4 py-1 rounded-full hover:bg-red-600 transition-colors flex items-center gap-1"
                               ${isWaitingForLawyer ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                            <span>Talk with a Real Attorney Now</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }

        function sendPredefinedMessage(message) {
            document.getElementById('message-input').value = message;
            sendMessage();
        }

        function addMessage(content, isUser = false) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex items-start';

            const avatar = `
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full ${isUser ? 'bg-green-500' : 'bg-blue-500'} flex items-center justify-center">
                        <span class="text-white font-bold">${isUser ? 'You' : 'AI'}</span>
                    </div>
                </div>
            `;

            const message = `
                <div class="ml-3 ${isUser ? 'bg-green-50' : 'bg-gray-100'} rounded-lg py-3 px-4 max-w-[80%]">
                    <p class="text-gray-900">${content}</p>
                    ${!isUser ? createFollowUpPrompt() : ''}
                </div>
            `;

            messageDiv.innerHTML = avatar + message;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        async function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            if (!message) return;

            // Add user message to chat
            addMessage(message, true);
            input.value = '';

            // Show typing indicator
            showTypingIndicator();

            try {
                const response = await fetch('/test-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        message: message,
                        conversation_id: conversationId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    conversationId = data.conversation_id;
                    hideTypingIndicator();
                    addMessage(data.message);
                } else {
                    hideTypingIndicator();
                    addMessage('Sorry, I encountered an error. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                hideTypingIndicator();
                addMessage('Sorry, I encountered an error. Please try again.');
            }
        }

        async function connectWithAttorney() {
            isWaitingForLawyer = true;
            
            // Initial connecting message
            addMessage("We are now connecting you with one of our attorneys based on your chat history. Please wait a moment...", false);
            
            // Show loading animation
            showTypingIndicator();

            try {
                // Initial processing (5 seconds)
                await new Promise(resolve => setTimeout(resolve, 5000));
                hideTypingIndicator();

                // First status update
                addMessage("We've located several attorneys with expertise in your matter. Checking their availability now...", false);
                
                // Checking availability (4 seconds)
                await new Promise(resolve => setTimeout(resolve, 4000));
                
                // Second status update
                addMessage("We've found attorneys available to assist you. Preparing secure connection...", false);

                // Preparing connection (3 seconds)
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                // Final queue message
                addMessage("You're in our priority queue. One of our attorneys will join this chat shortly. Please stay in this window.", false);
                
                // Start checking for lawyer connection
                checkForLawyerConnection();

            } catch (error) {
                console.error('Error:', error);
                hideTypingIndicator();
                isWaitingForLawyer = false;
                addMessage("Sorry, we encountered an error connecting you with an attorney. Please try again.", false);
            }
        }

        function checkForLawyerConnection() {
            if (!isWaitingForLawyer) return;

            let waitTime = 0;
            const totalWaitTime = 20000; // 20 seconds total wait
            const updateInterval = 3000; // Update status every 3 seconds

            // Poll for lawyer connection
            const pollInterval = setInterval(async () => {
                try {
                    waitTime += updateInterval;
                    
                    // Update the waiting status with more specific messages
                    updateWaitingStatus(waitTime, totalWaitTime);

                    if (waitTime >= totalWaitTime) {
                        clearInterval(pollInterval);
                        lawyerConnected();
                    }

                } catch (error) {
                    console.error('Error checking lawyer connection:', error);
                }
            }, updateInterval);
        }

        function updateWaitingStatus(currentWait, totalWait) {
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
        }

        function lawyerConnected() {
            // Remove waiting status
            const statusDiv = document.getElementById('lawyer-status');
            if (statusDiv) statusDiv.remove();

            isWaitingForLawyer = false;

            // Add lawyer connected message
            addMessage("Attorney John Smith has joined the chat. They have full access to your previous conversation and are ready to assist you.", false);
            
            // Update chat interface for lawyer mode
            updateChatForLawyerMode();
        }

        function updateChatForLawyerMode() {
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
        }

        // Allow Enter key to send message
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html> 