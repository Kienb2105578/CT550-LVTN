(function ($) {
    ("use strict");

    var HT = {}; // Khai báo đối tượng HT
    var _token = $('meta[name="csrf-token"]').attr("content");

    HT.initChat = function () {
        const chatWidget = document.getElementById("chatWidget");
        const chatIcon = document.getElementById("chatIcon");
        const chatContainer = document.getElementById("chatContainer");
        const minimizeChat = document.getElementById("minimizeChat");
        const chatInput = document.getElementById("chatInput");
        const sendMessage = document.getElementById("sendMessage");
        const chatMessages = document.getElementById("chatMessages");
        const typingIndicator = document.getElementById("typingIndicator");
        const unreadCounter = document.getElementById("unreadCounter");

        let unreadMessages = 0;
        let isChatOpen = false;

        HT.toggleChat = function () {
            setTimeout(() => {
                if (isChatOpen) {
                    chatContainer.style.display = "none";
                    chatIcon.innerHTML = "💬";
                } else {
                    chatContainer.style.display = "block";
                    chatIcon.innerHTML = '<span class="icon-text">✖</span>';
                }
                isChatOpen = !isChatOpen;
            }, 200);
        };

        HT.minimizeChat = function () {
            setTimeout(() => {
                chatContainer.style.display = "none";
                chatIcon.innerHTML = "💬";
                isChatOpen = false;
            }, 200);
        };

        HT.sendUserMessage = function () {
            const messageText = chatInput.value.trim();
            if (messageText === "") return;

            // Hiển thị tin nhắn của người dùng
            HT.appendMessage("user", messageText);
            chatInput.value = "";
            HT.showTypingIndicator();

            // Gửi tin nhắn lên server qua AJAX
            $.ajax({
                url: "/ajax/chatbot/create",
                type: "POST",
                data: {
                    _token: _token,
                    message: messageText,
                    session_id: "default-session", // Cần đảm bảo session_id hợp lệ
                },
                success: function (response) {
                    HT.hideTypingIndicator();
                    if (response.success) {
                        // Hiển thị tin nhắn phản hồi từ chatbot
                        HT.appendMessage("bot", response.bot_chat.message);
                    } else {
                        HT.appendMessage("bot", "Xin lỗi, có lỗi xảy ra.");
                    }
                },
                error: function () {
                    HT.hideTypingIndicator();
                    HT.appendMessage("bot", "Không thể kết nối đến server.");
                },
            });
        };

        HT.appendMessage = function (sender, text) {
            const messageElement = document.createElement("div");
            messageElement.classList.add("chat-message", sender);
            messageElement.innerHTML = `
                <div class="message-avatar">
                    <img src="${
                        sender === "user"
                            ? "frontend/resources/core/image/user-image.png"
                            : "frontend/resources/core/image/chatbox.jpg"
                    }" alt="${sender} Avatar">
                </div>
                <div class="message-content">
                    <div class="message-text">${text}</div>
                    <div class="message-meta">
                        <span class="message-time">${new Date().toLocaleTimeString(
                            [],
                            { hour: "2-digit", minute: "2-digit" }
                        )}</span>
                    </div>
                </div>
            `;

            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            if (!isChatOpen) {
                unreadMessages++;
                unreadCounter.textContent = unreadMessages;
            }
        };

        HT.showTypingIndicator = function () {
            typingIndicator.style.display = "flex";
        };

        HT.hideTypingIndicator = function () {
            typingIndicator.style.display = "none";
        };

        // Sự kiện click
        chatIcon.addEventListener("click", HT.toggleChat);
        minimizeChat.addEventListener("click", HT.minimizeChat);
        sendMessage.addEventListener("click", HT.sendUserMessage);

        chatInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
                HT.sendUserMessage();
            }
        });

        function getCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, "0"); // Lấy giờ và đảm bảo có 2 chữ số
            const minutes = String(now.getMinutes()).padStart(2, "0"); // Lấy phút và đảm bảo có 2 chữ số
            return `${hours}:${minutes}`;
        }

        // Khi chat mở, hiển thị thông báo chào mừng và thời gian
        function showWelcomeMessage() {
            const currentTime = getCurrentTime(); // Lấy thời gian hiện tại
            messageTime.textContent = currentTime; // Cập nhật thời gian vào phần tử
        }

        // Sự kiện mở chat
        chatIcon.addEventListener("click", function () {
            if (
                chatContainer.style.display === "none" ||
                chatContainer.style.display === ""
            ) {
                chatContainer.style.display = "block";
                showWelcomeMessage(); // Hiển thị thông báo chào mừng khi mở chat
            } else {
                chatContainer.style.display = "none";
            }
        });

        document.addEventListener("click", function (event) {
            if (
                isChatOpen &&
                !chatContainer.contains(event.target) &&
                event.target !== chatIcon
            ) {
                HT.minimizeChat();
            }
        });
    };

    $(document).ready(function () {
        HT.initChat();
    });
})(jQuery);
