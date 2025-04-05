<link rel="stylesheet" href="frontend\core\css\chatbox.css">

<!-- Chat Widget Container -->
<div class="chat-widget" id="chatWidget">
    <!-- Chat Icon -->
    <div class="chat-icon" id="chatIcon" role="button" aria-label="Open chat">
        💬
    </div>

    <!-- Chat Box -->
    <div class="chat-container" id="chatContainer">
        <!-- Chat Header -->
        <header class="chat-header">
            <div class="header-info">
                <span class="header-title">Hỗ trợ khách hàng</span>
                <span class="header-status" id="chatStatus">Online</span>
            </div>
            <div class="header-actions">
                <button class="btn-options" aria-label="Chat options">⋮</button>
                <button class="btn-minimize" id="minimizeChat" aria-label="Minimize chat">─</button>
            </div>
        </header>

        <!-- Chat Messages -->
        <div class="chat-body" id="chatBody">
            <div class="chat-messages" id="chatMessages">
                <!-- Error Message Template -->
                <div class="chat-message bot">
                    <div class="message-avatar">
                        <img src="frontend\resources\core\image\chatbox.jpg" alt="Bot Avatar">
                    </div>
                    <div class="message-content">
                        <div class="message-text">
                            Chào bạn đến với cửa hàng của chúng tôi! Tôi là Chatbot, rất vui được hỗ trợ bạn. Nếu bạn
                            có bất kỳ câu hỏi nào hoặc cần sự giúp đỡ, đừng ngần ngại hỏi tôi. 🌟
                        </div>
                        <div class="message-meta">
                            <span class="message-time" id="messageTime"></span>
                            <span class="message-status">✓✓</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div class="typing-indicator" id="typingIndicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Chat Input Area -->
        <div class="chat-footer">
            <div class="chat-input-wrapper" id="chatInputWrapper">
                <textarea class="chat-input" id="chatInput" placeholder="Nhập tin nhắn ..." rows="1"></textarea>
                <button class="btn-send" id="sendMessage" aria-label="Send message">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                    </svg>
                </button>
            </div>
            <button class="btn-new-chat" id="startNewChat">
                Bắt đầu cuộc trò chuyện mới
            </button>
            <div class="chat-powered">⚙ INCOM</div>
        </div>
    </div>
</div>
<script src="frontend\core\library\chatbox.js"></script>
{{-- CREATE TABLE chatbox (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT(20) UNSIGNED NULL,
    session_id VARCHAR(100) NOT NULL,
    sender ENUM('customer', 'bot') NOT NULL,
    message TEXT NOT NULL,
    response TEXT NULL,
    intent VARCHAR(191) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
); --}}
