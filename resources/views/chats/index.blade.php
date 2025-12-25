@extends('layouts.app')

@section('title', 'Messages')
@section('header_title', 'Messages')

@section('content')
    <!-- Modern Messaging Interface -->
    <div class="messaging-container">
        <!-- Left Panel: Conversations List -->
        <div class="conversations-panel">
            <div class="conversations-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Messages</h5>
                    <button type="button" class="btn btn-sm btn-primary-premium" data-bs-toggle="modal"
                        data-bs-target="#newMessageModal">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
            <div id="conversationsList">
                <div class="messaging-empty-state">
                    <i class="bi bi-chat-dots"></i>
                    <p>Loading conversations...</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Messages Thread -->
        <div class="messages-panel" id="messagesPanel">
            <div class="messaging-empty-state">
                <i class="bi bi-chat-text"></i>
                <h5>Select a conversation</h5>
                <p>Choose a conversation from the list to start messaging</p>
            </div>
        </div>
    </div>

    <!-- New Message Modal -->
    <div class="modal fade" id="newMessageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <select id="newMessageRecipient" class="form-select" required>
                            <option value="">Select recipient...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea id="newMessageText" class="form-control" rows="4" required placeholder="Type your message..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-premium" onclick="sendNewMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentConversation = null;
        let currentUser = null;
        let availableContacts = [];

        document.addEventListener('DOMContentLoaded', async () => {
            // Get current user
            try {
                const userRes = await axios.get('/api/v1/user');
                currentUser = userRes.data?.data || userRes.data;
            } catch (e) {
                console.error('Failed to fetch user:', e);
            }

            // Load available contacts for new messages
            loadAvailableContacts();

            // Load conversations
            await loadConversations();

            // Handle partner_id from URL (e.g., from notifications)
            const urlParams = new URLSearchParams(window.location.search);
            const partnerId = urlParams.get('partner_id');
            if (partnerId) {
                // Find the partner in available contacts to get their name
                const contact = availableContacts.find(c => c.id == partnerId);
                if (contact) {
                    loadConversation(null, partnerId, contact.name);
                } else {
                    // Fallback to searching in loaded conversations
                    const conv = Object.values(groupChatsByPartner([])).find(c => c.partner.id == partnerId);
                    if (conv) loadConversation(null, partnerId, conv.partner.name);
                }
            }

            // Start listening for real-time messages
            listenForMessages();
        });

        function listenForMessages() {
            if (!currentUser) return;

            console.log('Listening for messages on channel:', `chat.${currentUser.id}`);

            window.Echo.private(`chat.${currentUser.id}`)
                .subscribed(() => {
                    console.log('Successfully subscribed to private channel chat.' + currentUser.id);
                })
                .error((err) => {
                    console.error('Subscription error on private channel chat.' + currentUser.id, err);
                })
                .listen('.MessageSent', (e) => {
                    console.log('Real-time message received in chat module:', e);

                    // Normalize IDs for comparison
                    const senderId = String(e.sender_id || '').toLowerCase();
                    const currentPartnerId = currentConversation?.partner?.id ? String(currentConversation.partner.id)
                        .toLowerCase() : null;

                    // If this message belongs to the currently open conversation, append it
                    if (currentConversation && currentPartnerId === senderId) {
                        console.log('Appending received message to active thread');
                        appendMessage(e, 'received');

                        // Mark as read immediately since the chat is open
                        axios.patch('/api/v1/chats/mark-as-read', {
                                partner_id: e.sender_id
                            })
                            .catch(err => console.error('Failed to mark received message as read:', err));
                    } else {
                        console.log('Message is for different conversation or no conversation open', {
                            senderId,
                            currentPartnerId
                        });
                    }

                    // Refresh conversations list to update snippets and ordering
                    loadConversations();
                });
        }

        async function loadAvailableContacts() {
            try {
                const studentId = window.App?.currentStudentId || '';
                const res = await axios.get(`/api/v1/chats/available-contacts?student_id=${studentId}`);
                availableContacts = res.data?.data || res.data || [];

                const select = document.getElementById('newMessageRecipient');
                select.innerHTML = '<option value="">Select recipient...</option>';

                availableContacts.forEach(contact => {
                    const option = document.createElement('option');
                    option.value = contact.id;
                    option.textContent = contact.name;
                    select.appendChild(option);
                });
            } catch (e) {
                console.error('Failed to load contacts:', e);
            }
        }

        async function loadConversations() {
            try {
                const res = await axios.get('/api/v1/chats');
                const chats = res.data?.data || [];

                // Group chats by conversation partner
                const conversations = groupChatsByPartner(chats);
                renderConversations(conversations);
            } catch (e) {
                console.error('Failed to load conversations:', e);
                document.getElementById('conversationsList').innerHTML = `
                <div class="messaging-empty-state">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <p>Failed to load conversations</p>
                </div>
            `;
            }
        }

        function groupChatsByPartner(chats) {
            const conversationsMap = {};

            chats.forEach(chat => {
                const partnerId = chat.sender_id === currentUser.id ? chat.receiver_id : chat.sender_id;
                let partner = chat.sender_id === currentUser.id ? chat.receiver : chat.sender;

                if (partner.school) {
                    partner = {
                        ...partner,
                        name: `${partner.name} (${partner.school.name})`
                    };
                }

                if (!conversationsMap[partnerId]) {
                    conversationsMap[partnerId] = {
                        partner: partner,
                        lastMessage: chat.message,
                        lastMessageTime: chat.created_at,
                        unreadCount: 0,
                        messages: []
                    };
                } else {
                    // Update latest message info if this chat is newer
                    if (new Date(chat.created_at) > new Date(conversationsMap[partnerId].lastMessageTime)) {
                        conversationsMap[partnerId].lastMessage = chat.message;
                        conversationsMap[partnerId].lastMessageTime = chat.created_at;
                    }
                }

                conversationsMap[partnerId].messages.push(chat);

                if (!chat.is_read && chat.receiver_id === currentUser.id) {
                    conversationsMap[partnerId].unreadCount++;
                }
            });

            return Object.values(conversationsMap).sort((a, b) =>
                new Date(b.lastMessageTime) - new Date(a.lastMessageTime)
            );
        }

        function backToList() {
            const panel = document.getElementById('messagesPanel');
            panel.classList.remove('active');
            // Clear active state from sidebar items
            document.querySelectorAll('.conversation-item').forEach(item => item.classList.remove('active'));
        }

        function renderConversations(conversations) {
            const container = document.getElementById('conversationsList');

            if (conversations.length === 0) {
                container.innerHTML = `
                <div class="messaging-empty-state">
                    <i class="bi bi-chat-dots"></i>
                    <p>No conversations yet</p>
                </div>
            `;
                return;
            }

            container.innerHTML = conversations.map(conv => `
            <div class="conversation-item ${currentConversation?.partner?.id === conv.partner.id ? 'active' : ''}" 
                 onclick="loadConversation(event, '${conv.partner.id}', '${escapeHtml(conv.partner.name)}')">
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(conv.partner.name)}&background=2563eb&color=fff" 
                     class="avatar" alt="${escapeHtml(conv.partner.name)}">
                <div class="conversation-info">
                    <div class="name">${escapeHtml(conv.partner.name)}</div>
                    <div class="last-message">${escapeHtml(conv.lastMessage.substring(0, 40))}${conv.lastMessage.length > 40 ? '...' : ''}</div>
                </div>
                <div class="timestamp">${formatTime(conv.lastMessageTime)}</div>
                ${conv.unreadCount > 0 ? `<div class="unread-badge">${conv.unreadCount}</div>` : ''}
            </div>
        `).join('');
        }

        async function loadConversation(event, partnerId, partnerName) {
            currentConversation = {
                partner: {
                    id: partnerId,
                    name: partnerName
                }
            };

            const panel = document.getElementById('messagesPanel');
            panel.classList.add('active'); // Show on mobile

            panel.innerHTML = `
            <div class="messages-header">
                <button class="btn btn-link link-dark d-lg-none me-2 p-0" onclick="backToList()">
                    <i class="bi bi-chevron-left fs-4"></i>
                </button>
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(partnerName)}&background=2563eb&color=fff" 
                     class="avatar" alt="${escapeHtml(partnerName)}">
                <div>
                    <div class="name">${escapeHtml(partnerName)}</div>
                </div>
            </div>
            <div class="messages-body" id="messagesBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="messages-footer">
                <textarea id="messageInput" rows="1" class="form-control" placeholder="Type a message..." onkeypress="handleMessageKeyPress(event)"></textarea>
                <button onclick="sendMessage()"><i class="bi bi-send-fill"></i></button>
            </div>
        `;

            // Mark conversation as active
            document.querySelectorAll('.conversation-item').forEach(item => item.classList.remove('active'));
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }

            // Load messages
            try {
                const res = await axios.get(`/api/v1/chats?partner_id=${partnerId}`);
                const messages = res.data?.data || [];
                renderMessages(messages);

                // Mark messages as read on the backend
                axios.patch('/api/v1/chats/mark-as-read', {
                        partner_id: partnerId
                    })
                    .catch(e => console.error('Failed to mark messages as read:', e));

                // Hide unread badge locally in the sidebar
                const item = document.querySelector(`.conversation-item[onclick*="'${partnerId}'"]`);
                if (item) {
                    const badge = item.querySelector('.unread-badge');
                    if (badge) badge.remove();
                }
            } catch (e) {
                console.error('Failed to load messages:', e);
                document.getElementById('messagesBody').innerHTML = `
                <div class="messaging-empty-state">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <p>Failed to load messages</p>
                </div>
            `;
            }
        }

        function renderMessages(messages) {
            const container = document.getElementById('messagesBody');

            if (messages.length === 0) {
                container.innerHTML = `
                <div class="messages-empty">
                    <i class="bi bi-chat"></i>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            `;
                return;
            }

            container.innerHTML = messages.map(msg => {
                const isSent = msg.sender_id === currentUser.id;
                return `
                <div class="message ${isSent ? 'sent' : 'received'}">
                    <div class="message-content">
                        <div class="message-text">${escapeHtml(msg.message)}</div>
                        <div class="message-time">${formatTime(msg.created_at)}</div>
                    </div>
                </div>
            `;
            }).join('');

            // Scroll to bottom
            container.scrollTop = container.scrollHeight;
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();

            if (!message || !currentConversation || !currentUser) return;

            const tempId = 'temp-' + Date.now();
            const messageData = {
                id: tempId,
                sender_id: currentUser.id,
                message: message,
                created_at: new Date().toISOString()
            };

            // Append instantly
            appendMessage(messageData, 'sent');
            input.value = '';

            try {
                const res = await axios.post('/api/v1/chats', {
                    receiver_id: currentConversation.partner.id,
                    message: message
                });

                // Update the snippet in the sidebar without full reload
                updateSidebarSnippet(currentConversation.partner.id, message);
            } catch (e) {
                console.error('Failed to send message:', e);
                // Remove the temp message on failure
                const tempMsg = document.querySelector(`[data-temp-id="${tempId}"]`);
                if (tempMsg) tempMsg.remove();

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to send message'
                });
            }
        }

        async function sendNewMessage() {
            const recipientId = document.getElementById('newMessageRecipient').value;
            const message = document.getElementById('newMessageText').value.trim();

            if (!recipientId || !message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select a recipient and enter a message'
                });
                return;
            }

            try {
                const res = await axios.post('/api/v1/chats', {
                    receiver_id: recipientId,
                    message: message
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById('newMessageModal'));
                modal.hide();

                document.getElementById('newMessageText').value = '';
                document.getElementById('newMessageRecipient').value = '';

                // If not in current conversation, reload list and switch
                const recipient = availableContacts.find(c => c.id == recipientId);
                await loadConversations();
                if (recipient) {
                    loadConversation(null, recipientId, recipient.name);
                }
            } catch (e) {
                console.error('Failed to send message:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to send message'
                });
            }
        }

        function appendMessage(msg, type) {
            const container = document.getElementById('messagesBody');
            if (!container) return;

            // Remove empty state if present
            const empty = container.querySelector('.messages-empty');
            if (empty) empty.remove();

            const isSent = type === 'sent' || msg.sender_id === currentUser.id;
            const messageHtml = `
                <div class="message ${isSent ? 'sent' : 'received'}" ${msg.id.toString().startsWith('temp-') ? `data-temp-id="${msg.id}"` : ''}>
                    <div class="message-content">
                        <div class="message-text">${escapeHtml(msg.message)}</div>
                        <div class="message-time">${formatTime(msg.created_at)}</div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', messageHtml);
            container.scrollTop = container.scrollHeight;
        }

        function updateSidebarSnippet(partnerId, message) {
            const item = document.querySelector(`.conversation-item[onclick*="'${partnerId}'"]`);
            if (item) {
                const snippet = item.querySelector('.last-message');
                const timestamp = item.querySelector('.timestamp');
                if (snippet) snippet.textContent = message.substring(0, 40) + (message.length > 40 ? '...' : '');
                if (timestamp) timestamp.textContent = 'Just now';

                // Move to top
                const container = document.getElementById('conversationsList');
                container.prepend(item);
            }
        }


        function handleMessageKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            if (diff < 604800000) return Math.floor(diff / 86400000) + 'd ago';

            return date.toLocaleDateString();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endsection
