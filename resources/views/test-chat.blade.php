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
                    <div class="ml-3 bg-gray-100 rounded-lg py-2 px-4 max-w-[80%]">
                        <p class="text-gray-900">Hello! I'm your legal AI assistant. How can I help you today?</p>
                    </div>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div id="typing-indicator" class="typing-indicator ml-16">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- Input Area -->
            <div class="border-t p-4">
                <div class="flex space-x-3">
                    <input type="text" 
                           id="message-input" 
                           class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="Type your legal question here..."
                           onkeypress="if(event.key === 'Enter') sendMessage()">
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

        function addMessage(message, isUser = false) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex items-start';

            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'flex-shrink-0';
            avatarDiv.innerHTML = `
                <div class="h-10 w-10 rounded-full ${isUser ? 'bg-green-500' : 'bg-blue-500'} flex items-center justify-center">
                    <span class="text-white font-bold">${isUser ? 'You' : 'AI'}</span>
                </div>
            `;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'ml-3 bg-gray-100 rounded-lg py-2 px-4 max-w-[80%]';
            contentDiv.innerHTML = `<p class="text-gray-900">${message}</p>`;

            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
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
                const response = await fetch('/chat/send', {
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

                if (response.status === 419) {
                    // CSRF token mismatch, refresh the page
                    window.location.reload();
                    return;
                }

                const data = await response.json();
                
                if (data.success) {
                    conversationId = data.conversation_id;
                    hideTypingIndicator();
                    addMessage(data.message);
                } else {
                    hideTypingIndicator();
                    const errorMessage = data.details || data.error || 'Sorry, I encountered an error. Please try again.';
                    addMessage('Error: ' + errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                hideTypingIndicator();
                addMessage('Sorry, I encountered an error. Please try again.');
            }
        }
    </script>
</body>
</html>