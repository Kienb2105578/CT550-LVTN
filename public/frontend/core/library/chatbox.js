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

            HT.appendMessage("user", messageText);
            chatInput.value = "";
            HT.showTypingIndicator();

            $.ajax({
                url: "/ajax/chatbot/create",
                type: "POST",
                data: {
                    _token: _token,
                    message: messageText,
                    session_id: "default-session",
                },
                success: function (response) {
                    const products = response.products;
                    const userMessage = response.message;
                    console.log(response.message);

                    let productList = products
                        .map((product, index) => {
                            const stockStatus =
                                product.stock > 0
                                    ? `Còn ${product.stock} cái`
                                    : "Hết hàng";
                            const imageHtml = product.image
                                ? `<img src="${product.image}" style="max-width:100px;">`
                                : "";
                            const productLink = product.link
                                ? `<a href="${product.link}" target="_blank">Xem chi tiết</a>`
                                : "";

                            return `
                                    <div style="margin-bottom: 10px;">
                                        ${imageHtml}<br>
                                        <strong>${index + 1}. ${
                                product.name
                            }</strong><br>
                                        Giá: ${product.price}K<br>
                                        Tình trạng: ${stockStatus}<br>
                                        Danh mục: ${product.category}<br>
                                        ${productLink}
                                    </div>
                                    `.trim();
                        })
                        .join("<br>");

                    const prompt = `
                            Người dùng hỏi: "${userMessage}"
                            Dưới đây là danh sách sản phẩm hiện có (bao gồm hình ảnh và liên kết HTML):
                            ${productList}

                            Yêu cầu:
                                - Phân tích và hiểu câu hỏi của người dùng.
                                - Chọn lọc dữ liệu phù hợp.
                                - Trả lời tự nhiên, lịch sự, dễ hiểu.
                                - Không được bịa thêm sản phẩm không có trong danh sách.
                                - Giữ nguyên định dạng HTML có sẵn (đặc biệt là thẻ <a href> và hình ảnh).
                                - Nếu sản phẩm không phù hợp, hãy gợi ý các sản phẩm gần nhất.
                        `;

                    // Gửi đến Gemini API
                    $.get("/ajax/get-gemini-key", function (res) {
                        const apiKey = res.key;
                        const geminiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;

                        $.ajax({
                            url: geminiUrl,
                            type: "POST",
                            contentType: "application/json",
                            data: JSON.stringify({
                                contents: [{ parts: [{ text: prompt }] }],
                            }),
                            success: function (geminiRes) {
                                const reply =
                                    geminiRes?.candidates?.[0]?.content
                                        ?.parts?.[0]?.text ??
                                    "Không có phản hồi từ AI.";
                                HT.appendMessage("bot", reply);
                            },
                            error: function () {
                                HT.appendMessage(
                                    "bot",
                                    "Không thể kết nối đến Gemini."
                                );
                            },
                        });
                    });

                    HT.hideTypingIndicator();
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
                    <div class="message-text">${HT.decodeHtml(text)}</div>
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

    HT.decodeHtml = function (html) {
        const txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    };

    $(document).ready(function () {
        HT.initChat();
    });
})(jQuery);
