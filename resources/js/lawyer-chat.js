export default function lawyerChat() {
    return {
        pendingConversations: [],
        currentConversation: null,

        async initLawyerChat() {
            await this.loadPendingConversations();
            this.listenForNewConversations();
        },

        async loadPendingConversations() {
            try {
                const response = await axios.get('/lawyer/api/pending-conversations');
                this.pendingConversations = response.data;
            } catch (error) {
                console.error('Error loading conversations:', error);
            }
        },

        listenForNewConversations() {
            window.Echo.private('conversations')
                .listen('NewConversation', (e) => {
                    this.pendingConversations.unshift(e.conversation);
                });
        },

        async claimConversation(conversation) {
            try {
                const response = await axios.post(`/lawyer/api/claim-conversation/${conversation.id}`);
                this.pendingConversations = this.pendingConversations.filter(c => c.id !== conversation.id);
                this.currentConversation = response.data;
            } catch (error) {
                console.error('Error claiming conversation:', error);
            }
        },

        selectConversation(conversation) {
            this.currentConversation = conversation;
        }
    };
} 