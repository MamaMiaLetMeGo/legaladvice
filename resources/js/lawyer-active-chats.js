export default function lawyerActiveChats() {
    return {
        activeConversations: [],
        currentConversation: null,
        messages: [],
        newMessage: '',
        loading: false,
        userId: window.userId,

        async initActiveChats() {
            await this.loadActiveConversations();
            this.listenForNewMessages();
        },

        async loadActiveConversations() {
            try {
                const response = await axios.get('/lawyer/api/active-conversations');
                this.activeConversations = response.data;
            } catch (error) {
                console.error('Error loading active conversations:', error);
            }
        },

        async selectConversation(conversation) {
            this.currentConversation = conversation;
            this.loading = true;
            try {
                const response = await axios.get(`/chat/messages/${conversation.id}`);
                this.messages = response.data.messages;
                this.scrollToBottom();
            } catch (error) {
                console.error('Error loading messages:', error);
            }
            this.loading = false;
        },

        listenForNewMessages() {
            this.activeConversations.forEach(conversation => {
                window.Echo.private(`chat.${conversation.id}`)
                    .listen('NewChatMessage', (e) => {
                        if (this.currentConversation?.id === conversation.id) {
                            this.messages.push(e.message);
                            this.scrollToBottom();
                        }
                        this.updateConversationLastMessage(conversation.id, e.message);
                    });
            });
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.currentConversation) return;

            this.loading = true;
            try {
                const response = await axios.post('/chat/send', {
                    content: this.newMessage,
                    conversation_id: this.currentConversation.id
                });

                this.messages.push(response.data);
                this.newMessage = '';
                this.scrollToBottom();
            } catch (error) {
                console.error('Error sending message:', error);
            }
            this.loading = false;
        },

        async closeConversation(conversation) {
            if (!confirm('Are you sure you want to close this conversation?')) return;

            try {
                await axios.post(`/lawyer/api/close-conversation/${conversation.id}`);
                this.activeConversations = this.activeConversations.filter(c => c.id !== conversation.id);
                if (this.currentConversation?.id === conversation.id) {
                    this.currentConversation = null;
                    this.messages = [];
                }
            } catch (error) {
                console.error('Error closing conversation:', error);
            }
        },

        updateConversationLastMessage(conversationId, message) {
            const conversation = this.activeConversations.find(c => c.id === conversationId);
            if (conversation) {
                conversation.messages = [message];
                conversation.last_message_at = message.created_at;
                // Move conversation to top of list
                this.activeConversations = [
                    conversation,
                    ...this.activeConversations.filter(c => c.id !== conversationId)
                ];
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        },

        formatLastMessage(conversation) {
            return conversation.messages[0]?.content || 'No messages';
        },

        formatDate(date) {
            return new Date(date).toLocaleString();
        }
    };
} 