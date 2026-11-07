<?php
// chat.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

// Ambil data user yang sedang login
$user = currentUser();
?>

<style>
    .chat-container {
        background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex; flex-direction: column; height: 75vh; overflow: hidden;
    }
    .chat-header { background: #2b6cb0; color: white; padding: 15px 20px; font-weight: bold; }
    .chat-messages { flex: 1; padding: 20px; overflow-y: auto; background: #edf2f7; display: flex; flex-direction: column; gap: 15px; }
    .msg-wrapper { display: flex; gap: 10px; max-width: 75%; }
    .msg-wrapper.me { align-self: flex-end; flex-direction: row-reverse; }
    .msg-avatar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .msg-bubble { background: white; padding: 10px 15px; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 14px; }
    .msg-wrapper.me .msg-bubble { background: #bee3f8; }
    .msg-info { font-size: 11px; color: #718096; margin-bottom: 4px; display: flex; justify-content: space-between; gap: 15px; }
    .msg-wrapper.me .msg-info { flex-direction: row-reverse; }
    .msg-name { font-weight: bold; color: #2d3748; }
    
    .chat-input { display: flex; gap: 10px; padding: 15px; background: white; border-top: 1px solid #e2e8f0; }
    .chat-input input { margin-bottom: 0; flex: 1; border-radius: 20px; padding: 10px 20px; }
    .chat-input button { border-radius: 20px; padding: 10px 25px; }
</style>

<div class="chat-container">
    <div class="chat-header">💬 Chat Room Kelompok PKL</div>
    <div class="chat-messages" id="chat-box">
        <!-- Pesan akan dimuat oleh JavaScript -->
        <p style="text-align: center; color: #a0aec0; font-size: 14px;" id="loading-msg">Memuat pesan...</p>
    </div>
    <form class="chat-input" id="chat-form">
        <input type="text" id="chat-input" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit" id="send-btn">Kirim</button>
    </form>
</div>

<script>
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    let lastRenderedId = 0; // Untuk melacak pesan terakhir

    // Fungsi untuk mengambil pesan dari server
    async function fetchMessages() {
        try {
            const res = await fetch('<?= BASE_URL ?>/api/chat_fetch.php');
            const messages = await res.json();
            
            if (messages.length > 0) {
                let html = '';
                let newLastId = 0;
                
                messages.forEach(msg => {
                    // Hanya render pesan yang lebih baru dari yang sudah tampil
                    if (msg.id > lastRenderedId) {
                        html += `
                            <div class="msg-wrapper ${msg.is_me ? 'me' : ''}">
                                <div class="msg-avatar"><img src="${msg.photo}" alt="${msg.name}"></div>
                                <div>
                                    <div class="msg-info">
                                        <span class="msg-name">${msg.name} ${msg.role === 'teacher' ? '(Guru)' : ''}</span>
                                        <span>${msg.created_at}</span>
                                    </div>
                                    <div class="msg-bubble">${msg.content}</div>
                                </div>
                            </div>
                        `;
                        newLastId = msg.id;
                    }
                });

                if (html !== '') {
                    document.getElementById('loading-msg')?.remove(); // Hapus teks loading awal
                    chatBox.insertAdjacentHTML('beforeend', html);
                    lastRenderedId = newLastId;
                    // Auto-scroll ke bawah
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            }
        } catch (err) {
            console.error("Gagal mengambil chat", err);
        }
    }

    // Kirim pesan via AJAX
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const content = chatInput.value.trim();
        if (!content) return;

        // Kosongkan input langsung
        chatInput.value = '';
        
        try {
            await fetch('<?= BASE_URL ?>/api/chat_send.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content: content })
            });
            // Panggil fetch secara instan agar pesan kita langsung muncul
            fetchMessages();
        } catch (err) {
            alert("Gagal mengirim pesan. Cek koneksi.");
        }
    });

    // Polling: Panggil fetchMessages setiap 2 detik
    fetchMessages();
    setInterval(fetchMessages, 2000);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>