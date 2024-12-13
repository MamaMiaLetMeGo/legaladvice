export default function chat() {
    return {
        messages: [],
        newMessage: '',
        conversation: null,
        userId: window.userId,

        init() {
            if (this.userId) {
                this.loadExistingConversation();
            }
            this.setupEchoListeners();
        },

        async loadExistingConversation() {
            try {
                const response = await axios.get('/api/chat/conversation');
                if (response.data.conversation) {
                    this.conversation = response.data.conversation;
                    this.messages = response.data.messages;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading conversation:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            const messageToSend = this.newMessage;
            this.newMessage = ''; // Clear input immediately

            try {
                const response = await axios.post('/api/chat/send', {
                    content: messageToSend,
                    conversation_id: this.conversation?.id,
                    new_conversation: !this.conversation
                });

                if (!this.conversation) {
                    this.conversation = response.data.conversation;
                }
                
                this.messages.push(response.data.message);
                this.scrollToBottom();
            } catch (error) {
                console.error('Error sending message:', error);
                this.newMessage = messageToSend; // Restore message if failed
            }
        },

        setupEchoListeners() {
            if (!this.userId) return;

            Echo.private(`chat.${this.userId}`)
                .listen('NewMessage', (e) => {
                    this.messages.push(e.message);
                    this.scrollToBottom();
                })
                .listen('ConversationUpdated', (e) => {
                    this.conversation = e.conversation;
                });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        },

        formatDate(date) {
            return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }
}