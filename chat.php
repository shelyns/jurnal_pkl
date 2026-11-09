<?php
// chat.php
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser();
?>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[75vh] overflow-hidden">
    <!-- Header Chat -->
    <div class="bg-primary text-white px-6 py-4 flex items-center gap-3 shrink-0">
        <div class="bg-white/20 p-2 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        </div>
        <div>
            <h2 class="font-bold text-lg leading-tight">Chat Room Kelompok</h2>
            <p class="text-indigo-100 text-xs">Komunikasi internal PKL</p>
        </div>
    </div>
    
    <!-- Area Pesan -->
    <div class="flex-1 p-6 overflow-y-auto bg-gray-50 flex flex-col gap-4" id="chat-box">
        <p class="text-center text-gray-400 text-sm mt-4" id="loading-msg">Memuat pesan...</p>
    </div>
    
    <!-- Input Chat -->
    <div class="p-4 bg-white border-t border-gray-100 shrink-0">
        <form id="chat-form" class="flex gap-3">
            <input type="text" id="chat-input" placeholder="Ketik pesan..." autocomplete="off" required class="flex-1 px-5 py-3 bg-gray-50 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
            <button type="submit" id="send-btn" class="bg-primary hover:bg-indigo-700 text-white w-12 h-12 rounded-full flex items-center justify-center transition-colors shadow-sm shrink-0">
                <svg class="w-5 h-5 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    let lastRenderedId = 0;

    async function fetchMessages() {
        try {
            const res = await fetch('<?= BASE_URL ?>/api/chat_fetch.php');
            const messages = await res.json();
            
            if (messages.length > 0) {
                let html = '';
                let newLastId = 0;
                
                messages.forEach(msg => {
                    if (msg.id > lastRenderedId) {
                        // Desain Bubble Chat
                        const bubbleStyle = msg.is_me 
                            ? 'bg-primary text-white rounded-t-2xl rounded-bl-2xl' 
                            : 'bg-white border border-gray-100 shadow-sm text-gray-800 rounded-t-2xl rounded-br-2xl';
                        const wrapperStyle = msg.is_me ? 'ml-auto flex-row-reverse' : '';

                        html += `
                            <div class="flex items-end gap-2 max-w-[85%] md:max-w-[70%] ${wrapperStyle}">
                                <img src="${msg.photo}" class="w-8 h-8 rounded-full object-cover shrink-0">
                                <div>
                                    <div class="flex gap-2 mb-1 px-1 ${msg.is_me ? 'justify-end' : ''}">
                                        <span class="text-[11px] font-bold text-gray-500">${msg.name}</span>
                                        <span class="text-[10px] text-gray-400">${msg.created_at}</span>
                                    </div>
                                    <div class="px-4 py-2 text-sm ${bubbleStyle}">
                                        ${msg.content}
                                    </div>
                                </div>
                            </div>
                        `;
                        newLastId = msg.id;
                    }
                });

                if (html !== '') {
                    document.getElementById('loading-msg')?.remove();
                    chatBox.insertAdjacentHTML('beforeend', html);
                    lastRenderedId = newLastId;
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            }
        } catch (err) {
            console.error("Gagal mengambil chat", err);
        }
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const content = chatInput.value.trim();
        if (!content) return;
        chatInput.value = '';
        
        try {
            await fetch('<?= BASE_URL ?>/api/chat_send.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content: content })
            });
            fetchMessages();
        } catch (err) {
            alert("Gagal mengirim pesan.");
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 2000);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>