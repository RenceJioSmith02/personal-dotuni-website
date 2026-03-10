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

function sendMessageToServer(message) {
    fetch('/DotUni/GetResponse', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
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
    setTimeout(() => {
        labelContainer.style.opacity = 1;
    }, 10);
    isChatOpen = false;
});

let clearChatTimeout;

closeChatButton.addEventListener('click', () => {
    webchatContainer.style.display = 'none';
    toggleButton.style.display = 'block';
    labelContainer.style.display = 'block';
    setTimeout(() => {
        labelContainer.style.opacity = 1;
    }, 10);
    isChatOpen = false;

    clearChatTimeout = setTimeout(() => {
        const chatMessages = document.querySelector('.chat-messages');
        if (chatMessages) {
            chatMessages.innerHTML = '';
        }
    }, 240000);
});

toggleButton.addEventListener('click', () => {
    clearTimeout(clearChatTimeout);
});

resetChatButton.addEventListener('click', () => {
    resetChat();
});

toggleButton.addEventListener('mouseenter', () => {
    if (!isChatOpen) {
        labelContainer.style.display = 'block';
        setTimeout(() => {
            labelContainer.style.opacity = 1;
        }, 10);
    }
});

toggleButton.addEventListener('mouseleave', () => {
    if (!isChatOpen) {
        labelContainer.style.opacity = 0;
        setTimeout(() => {
            labelContainer.style.display = 'none';
        }, 300);
    }
});

// --- Add this at the bottom to make chat persist! ---
labelContainer.style.display = 'none';
loadMessagesFromLocalStorage(); // Load previous chat history
displayWelcomeMessage(); // Show welcome message only if no previous chat
