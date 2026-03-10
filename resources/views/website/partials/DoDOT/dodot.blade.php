{{-- DoDOT AI Chatbot Partial --}}
{{-- Include in your layout: @include('website.partials.DoDOT.dodot') --}}

{{-- Chatbot CSS --}}
<link rel="stylesheet" href="{{ asset('assets/chatbotasset/assets/css/libs/bootstrap-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/chatbotasset/assets/css/libs/fontawesome-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/chatbotasset/assets/css/libs/themify-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/chatbotasset/assets/css/spin.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/chatbotasset/minimizablewebchat.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/chatbotasset/chatbox-style.css') }}" />

{{-- Chat Container --}}
<div class="chat-container" id="chat-container" style="display: none;">
    <div class="headersssss">
        <div class="title">
            <img src="{{ asset('assets/chatbotasset/dodotgif.gif') }}" alt="DoDOT" class="profilepicssss">
            <span style="letter-spacing: .2rem">DoDOT</span>
        </div>
        <div class="btnsssss">
            <a id="reset_chat" title="Reset">
                <svg fill="#000000" width="28px" height="28px" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 27.9999 51.9062 C 41.0546 51.9062 51.9063 41.0547 51.9063 28.0000 C 51.9063 14.9219 41.0312 4.0938 27.9765 4.0938 C 14.8983 4.0938 4.0937 14.9219 4.0937 28.0000 C 4.0937 41.0547 14.9218 51.9062 27.9999 51.9062 Z M 17.3593 28.9609 C 17.3593 23.125 22.1640 18.5313 27.5312 18.5313 C 27.7421 18.5313 27.9999 18.5547 28.1640 18.5781 L 26.4765 16.8906 C 26.2187 16.6094 26.0780 16.2109 26.0780 15.7891 C 26.0780 14.9453 26.7109 14.2656 27.5780 14.2656 C 27.9999 14.2656 28.3983 14.4297 28.6796 14.7344 L 33.2499 19.3984 C 33.7890 19.9609 33.8124 21.0391 33.2499 21.6016 L 28.6327 26.1953 C 28.3514 26.4766 27.9530 26.6640 27.5780 26.6640 C 26.7109 26.6640 26.0780 25.9844 26.0780 25.1640 C 26.0780 24.7187 26.2187 24.3438 26.5234 24.0625 L 28.8202 21.7891 C 28.5858 21.7422 28.2812 21.7422 27.9765 21.7422 C 23.8749 21.7422 20.6171 24.9766 20.6171 29.0313 C 20.6171 33.1328 23.8749 36.4140 27.9765 36.4140 C 32.0546 36.4140 35.3124 33.1328 35.3124 29.0313 C 35.3124 28.1640 36.0390 27.4375 36.9530 27.4375 C 37.8436 27.4375 38.5702 28.1640 38.5702 29.0313 C 38.5702 34.9140 33.8593 39.6953 27.9765 39.6953 C 22.0936 39.6953 17.3593 34.9140 17.3593 28.9609 Z" />
                </svg>
            </a>
            <a id="minimize_chat" title="Minimize">
                <i class="bi bi-dash-circle-fill"></i>
            </a>
            <a id="close_chat" title="Close">
                <i class="bi bi-x-circle-fill"></i>
            </a>
        </div>
    </div>

    <div id="chatbox" role="main">
        <div id="messages"></div>
        <div class="input_box">
            <textarea id="user-input" placeholder="Type your message..." rows="1"></textarea>
            <button id="send-button">Send</button>
        </div>
    </div>
</div>

{{-- Toggle Button (replaces .ai-chat-btn) --}}
<button class="float" id="chatbutton"></button>

{{-- Hover Label --}}
<div class="label-container">
    <div class="label-text">
        <img src="{{ asset('assets/chatbotasset/dodotchatbubbles.png') }}" alt="DoDOT" class="hoverimage">
    </div>
</div>

{{-- Chatbot JS --}}
<script src="{{ asset('assets/chatbotasset/assets/js/bundle.js') }}?ver=3.1.2"></script>
<script src="{{ asset('assets/chatbotasset/assets/js/scripts.js') }}?ver=3.1.2"></script>
<script src="{{ asset('assets/chatbotasset/assets/js/formwizard.js') }}"></script>

<script>
    const toggleButton = document.getElementById('chatbutton');
    const webchatContainer = document.getElementById('chat-container');
    const messageInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    const chatArea = document.getElementById('messages');
    const minimizeChatButton = document.getElementById('minimize_chat');
    const closeChatButton = document.getElementById('close_chat');
    const resetChatButton = document.getElementById('reset_chat');
    const labelContainer = document.querySelector('.label-container');

    let isChatOpen = false;
    let hasWelcomed = false;

    // -------------------------------------------------------
    // Send message to Laravel backend route: POST /dodot/chat
    // -------------------------------------------------------
    function sendMessageToServer(message) {
        fetch('{{ route("dodot.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ Prompt: message })
        })
        .then(response => response.json())
        .then(data => {
            displayBotMessage(data.response);
        })
        .catch(error => console.error('Error:', error));
    }

    function displayUserMessage(message) {
        const userBubble = document.createElement('div');
        userBubble.classList.add('bubble', 'user-bubble');
        userBubble.innerText = message;
        chatArea.appendChild(userBubble);
        scrollToBottom();
        saveMessagesToLocalStorage();
    }

    function displayBotMessage(response) {
        const responseBubble = document.createElement('div');
        responseBubble.classList.add('bubble', 'bot-bubble');
        responseBubble.innerHTML = response;
        chatArea.appendChild(responseBubble);
        scrollToBottom();
        saveMessagesToLocalStorage();
    }

    function scrollToBottom() {
        chatArea.scrollTop = chatArea.scrollHeight;
    }

    function displayWelcomeMessage() {
        const existingMessages = JSON.parse(localStorage.getItem('chatMessages')) || [];
        if (!hasWelcomed && existingMessages.length === 0) {
            displayBotMessage("👋 Hello! I'm DoDOT, your virtual assistant at CLSU Distance, Open, and Transnational University (DOT-Uni). How can I assist you today? You can ask me about admissions, tuition, programs, and more!");
            hasWelcomed = true;
        }
    }

    function resetChat() {
        chatArea.innerHTML = '';
        localStorage.removeItem('chatMessages');
        hasWelcomed = false;
        displayWelcomeMessage();
    }

    function saveMessagesToLocalStorage() {
        const messages = Array.from(chatArea.children).map(child => ({
            text: child.innerHTML,
            type: child.classList.contains('user-bubble') ? 'user' : 'bot'
        }));
        localStorage.setItem('chatMessages', JSON.stringify(messages));
    }

    function loadMessagesFromLocalStorage() {
        const messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
        messages.forEach(msg => {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble', msg.type === 'user' ? 'user-bubble' : 'bot-bubble');
            bubble.innerHTML = msg.text;
            chatArea.appendChild(bubble);
        });
        scrollToBottom();
    }

    sendButton.addEventListener('click', function () {
        const userMessage = messageInput.value.trim();
        if (!userMessage) return;
        displayUserMessage(userMessage);
        sendMessageToServer(userMessage);
        messageInput.value = '';
    });

    messageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendButton.click();
        }
    });

    toggleButton.addEventListener('click', () => {
        webchatContainer.style.display = 'block';
        toggleButton.style.display = 'none';
        labelContainer.style.display = 'none';
        isChatOpen = true;
        scrollToBottom();
    });

    minimizeChatButton.addEventListener('click', () => {
        webchatContainer.style.display = 'none';
        toggleButton.style.display = 'block';
        labelContainer.style.display = 'block';
        setTimeout(() => { labelContainer.style.opacity = 1; }, 10);
        isChatOpen = false;
    });

    let clearChatTimeout;

    closeChatButton.addEventListener('click', () => {
        webchatContainer.style.display = 'none';
        toggleButton.style.display = 'block';
        labelContainer.style.display = 'block';
        setTimeout(() => { labelContainer.style.opacity = 1; }, 10);
        isChatOpen = false;

        clearChatTimeout = setTimeout(() => {
            const chatMessages = document.querySelector('#messages');
            if (chatMessages) chatMessages.innerHTML = '';
        }, 240000);
    });

    toggleButton.addEventListener('click', () => { clearTimeout(clearChatTimeout); });

    resetChatButton.addEventListener('click', () => { resetChat(); });

    toggleButton.addEventListener('mouseenter', () => {
        if (!isChatOpen) {
            labelContainer.style.display = 'block';
            setTimeout(() => { labelContainer.style.opacity = 1; }, 10);
        }
    });

    toggleButton.addEventListener('mouseleave', () => {
        if (!isChatOpen) {
            labelContainer.style.opacity = 0;
            setTimeout(() => { labelContainer.style.display = 'none'; }, 300);
        }
    });

    // Init
    labelContainer.style.display = 'none';
    loadMessagesFromLocalStorage();
    displayWelcomeMessage();
</script>
