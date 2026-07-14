import { marked } from 'marked';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark-dimmed.css';

marked.setOptions({
    breaks: true,
    gfm: true,
    highlight: function (code, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(code, { language: lang }).value;
            } catch (_) { }
        }
        return hljs.highlightAuto(code).value;
    }
});

function chatApp() {
    return {
        conversations: [],
        activeConversation: null,
        messages: [],
        newMessage: '',
        isLoading: false,
        sidebarOpen: true,
        editingTitle: null,

        init() {
            this.loadConversations();

            window.addEventListener('toggle-sidebar', () => {
                this.toggleSidebar();
            });

            this.$watch('messages', () => {
                this.$nextTick(() => this.scrollToBottom());
            });

            this.$watch('isLoading', () => {
                this.$nextTick(() => this.scrollToBottom());
            });
        },

        loadConversations() {
            if (typeof axios === 'undefined') return;

            axios.get('/api/conversations')
                .then(res => {
                    this.conversations = res.data;
                })
                .catch(() => {});
        },

        selectConversation(id) {
            this.activeConversation = id;
            this.messages = [];

            if (typeof axios === 'undefined') return;

            axios.get(`/api/conversations/${id}/messages`)
                .then(res => {
                    this.messages = res.data.map(msg => ({
                        ...msg,
                        content: this.renderMarkdown(msg.content)
                    }));
                    this.$nextTick(() => this.scrollToBottom());
                })
                .catch(() => {});
        },

        newConversation() {
            if (typeof axios === 'undefined') return;

            this.isLoading = true;
            axios.post('/api/conversations', { title: 'Percakapan baru' })
                .then(res => {
                    this.conversations.unshift(res.data);
                    this.selectConversation(res.data.id);
                })
                .catch(() => {})
                .finally(() => {
                    this.isLoading = false;
                });
        },

        sendMessage() {
            const message = this.newMessage.trim();
            if (!message || this.isLoading) return;

            if (!this.activeConversation) {
                this.isLoading = true;
                axios.post('/api/conversations', { title: message.substring(0, 50) })
                    .then(res => {
                        this.conversations.unshift(res.data);
                        this.activeConversation = res.data.id;

                        return this.doSendMessage(res.data.id, message);
                    })
                    .catch(() => {})
                    .finally(() => {
                        this.isLoading = false;
                    });
            } else {
                this.doSendMessage(this.activeConversation, message);
            }
        },

        doSendMessage(conversationId, message) {
            this.newMessage = '';
            this.isLoading = true;

            this.messages.push({
                id: Date.now(),
                role: 'user',
                content: this.escapeHtml(message)
            });

            this.$nextTick(() => this.scrollToBottom());

            return axios.post(`/api/conversations/${conversationId}/messages`, {
                message: message
            })
            .then(res => {
                const reply = {
                    ...res.data,
                    content: this.renderMarkdown(res.data.content)
                };
                this.messages.push(reply);

                const conv = this.conversations.find(c => c.id === conversationId);
                if (conv && conv.title === 'Percakapan baru') {
                    conv.title = message.substring(0, 50);
                }
            })
            .catch(err => {
                const errorMsg = err.response?.data?.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.';
                this.messages.push({
                    id: Date.now() + 1,
                    role: 'assistant',
                    content: `<p class="text-red-500">${errorMsg}</p>`
                });
            })
            .finally(() => {
                this.isLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            });
        },

        suggestPrompt(text) {
            this.newMessage = text;
            this.$nextTick(() => {
                const input = this.$refs.messageInput;
                if (input) {
                    input.focus();
                    input.style.height = 'auto';
                    input.style.height = input.scrollHeight + 'px';
                }
            });
        },

        deleteConversation(id) {
            if (!confirm('Hapus percakapan ini?')) return;

            axios.delete(`/api/conversations/${id}`)
                .then(() => {
                    this.conversations = this.conversations.filter(c => c.id !== id);
                    if (this.activeConversation === id) {
                        this.activeConversation = null;
                        this.messages = [];
                    }
                })
                .catch(() => {});
        },

        startEditingTitle(conv) {
            this.editingTitle = conv.id;
        },

        updateConversationTitle(conv) {
            const newTitle = conv.title.trim() || 'Percakapan baru';
            conv.title = newTitle;
            this.editingTitle = null;

            axios.patch(`/api/conversations/${conv.id}`, { title: newTitle })
                .catch(() => {});
        },

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },

        scrollToBottom() {
            const container = this.$refs.chatContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        renderMarkdown(text) {
            try {
                const html = marked.parse(text);
                return html;
            } catch (e) {
                return this.escapeHtml(text);
            }
        }
    };
}

window.chatApp = chatApp;