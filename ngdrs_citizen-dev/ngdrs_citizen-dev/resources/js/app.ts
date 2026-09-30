import * as $ from "jquery";
import "./bootstrap";
import "./vendor/datatables/global";
import "datatables.net-dt";
import "./utils/ui/swal";
import "./utils/ui/notyf";
import { initTomSelect } from "./global";
import { initDateTime } from "./layout/header";
import { initProgressSteps } from "./layout/progress-steps";
import {
    initSidebarMenus,
    initProfileMenu,
    initSidebarToggle,
} from "./layout/sidebar";
import "./utils/http/interceptors";
import api from "./utils/http/axios";

document.addEventListener("DOMContentLoaded", () => {
    window.$ = $;
    window.jQuery = $;
    init();

    
});

declare const marked: {
    parse: (markdown: string) => string;
};
function init() {
    initDateTime();
    initProgressSteps();
    initSidebarMenus();
    initProfileMenu();
    initTomSelect();
    initSidebarToggle();
}

// <script>
let currentErrorMessage = null;
let chatHistory = [];

// Global function to trigger chatbot error state
window.triggerChatbotError = function (error) {
    console.log(error);
    currentErrorMessage = error;

    const button = document.getElementById("chatbot-button");
    const border = document.getElementById("chatbot-border");
    const rings = document.getElementById("chatbot-rings");
    const sparkles = document.getElementById("chatbot-sparkles");
    const badge = document.getElementById("chatbot-badge");
    const glow = document.getElementById("chatbot-glow");
    const container = document.getElementById("chatbot-container");
    const popup = document.getElementById("help-popup");

    // Add error state class
    button.classList.add("error-active");

    // Shake animation on trigger
    container.classList.add("shake");
    setTimeout(() => container.classList.remove("shake"), 500);

    // Show all animated elements
    border.style.opacity = "0.75";
    border.classList.add("animate-spin-gradient");

    rings.style.opacity = "1";

    sparkles.style.opacity = "1";

    badge.style.opacity = "1";
    badge.style.transform = "scale(1)";
    badge.classList.add("animate-bounce-strong");

    glow.style.opacity = "0.3";
    glow.classList.add("animate-pulse-glow");

    // Show popup after a short delay
    setTimeout(() => {
        popup.classList.add("show");
    }, 300);

    // Increase scale temporarily for attention
    button.style.transform = "scale(1.25)";
    setTimeout(() => {
        button.style.transform = "";
    }, 300);
};

// Function to add message to chat
// function addMessage(message, isUser = false) {
//     const messagesContainer = document.getElementById("chat-messages");
//     const messageDiv = document.createElement("div");
//     messageDiv.className = `flex ${isUser ? "justify-end" : "justify-start"} mb-3`;

//     const messageBubble = document.createElement("div");
//     messageBubble.className = `max-w-[80%] px-4 py-2 rounded-lg ${
//         isUser
//             ? "bg-gradient-to-r from-blue-600 to-purple-600 text-white"
//             : "bg-white shadow-md text-gray-800"
//     }`;
//     messageBubble.textContent = message;

//     messageDiv.appendChild(messageBubble);
//     messagesContainer.appendChild(messageDiv);

//     // Scroll to bottom
//     messagesContainer.scrollTop = messagesContainer.scrollHeight;

//     // Store in history
//     chatHistory.push({ message, isUser });
// }

function addMessage(message, isUser = false) {
    const messagesContainer = document.getElementById("chat-messages");
    const messageDiv = document.createElement("div");
    messageDiv.className = `flex ${isUser ? "justify-end" : "justify-start"} mb-3`;

    const messageBubble = document.createElement("div");
    messageBubble.className = `max-w-[80%] px-4 py-2 rounded-lg ${
        isUser
            ? "bg-gradient-to-r from-blue-600 to-purple-600 text-white"
            : "bg-white shadow-md text-gray-800 prose prose-sm max-w-none"
    }`;

    if (isUser) {
        messageBubble.textContent = message; // plain text, safe
    } else {
        messageBubble.innerHTML = marked.parse(message); // markdown → HTML
    }

    messageDiv.appendChild(messageBubble);
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    chatHistory.push({ message, isUser });
}

// Function to show typing indicator
function showTypingIndicator() {
    const messagesContainer = document.getElementById("chat-messages");
    const typingDiv = document.createElement("div");
    typingDiv.id = "typing-indicator";
    typingDiv.className = "flex justify-start mb-3";
    typingDiv.innerHTML = `
                <div class="bg-white shadow-md px-4 py-3 rounded-lg flex gap-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                </div>
            `;
    messagesContainer.appendChild(typingDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Function to remove typing indicator
function removeTypingIndicator() {
    const indicator = document.getElementById("typing-indicator");
    if (indicator) {
        indicator.remove();
    }
}

// Function to open chatbox
// function openChatbox() {
//     const chatbox = document.getElementById('chatbox');
//     chatbox.classList.add('show');

//     // If there's an error message and chat is empty, send it
//     if (currentErrorMessage && chatHistory.length === 0) {
//         // Add welcome message
//         setTimeout(() => {
//             addMessage("Hi! I noticed you encountered an error. Let me help you with that.", false);
//         }, 300);

//         // Add error message
//         setTimeout(() => {
//             addMessage(`Error: ${currentErrorMessage}`, false);
//         }, 800);

//         // Add help message
//         setTimeout(() => {
//             addMessage("I'm here to help you resolve this. What would you like to know?", false);
//         }, 1300);
//     }

//     // Reset error state
//     resetChatbot();

//     // Focus input
//     document.getElementById('chat-input').focus();
// }
function openChatbox() {
    const chatbox = document.getElementById("chatbox");
    chatbox.classList.add("show");

    // If there's an error message and chat is empty, send it as the user
    if (currentErrorMessage && chatHistory.length === 0) {
        const errorMsg = `I'm encountering this error: "${currentErrorMessage}". Help me identify the cause and fix it.`;

        setTimeout(() => {
            // Add as USER message
            addMessage(errorMsg, true);

            // Show typing indicator while AI thinks
            showTypingIndicator();

            // Call AI with the error message
            fetchAIResponse(errorMsg);
        }, 300);
    }

    // Reset error state visuals
    window.resetChatbot();

    // Focus input
    document.getElementById("chat-input").focus();
}
// Function to close chatbox
function closeChatbox() {
    const chatbox = document.getElementById("chatbox");
    chatbox.classList.remove("show");
}

// Global function to reset chatbot to normal state
window.resetChatbot = function () {
    const button = document.getElementById("chatbot-button");
    const border = document.getElementById("chatbot-border");
    const rings = document.getElementById("chatbot-rings");
    const sparkles = document.getElementById("chatbot-sparkles");
    const badge = document.getElementById("chatbot-badge");
    const glow = document.getElementById("chatbot-glow");
    const popup = document.getElementById("help-popup");

    // Remove error state
    button.classList.remove("error-active");

    // Hide all animated elements
    border.style.opacity = "0";
    border.classList.remove("animate-spin-gradient");

    rings.style.opacity = "0";

    sparkles.style.opacity = "0";

    badge.style.opacity = "0";
    badge.style.transform = "scale(0)";
    badge.classList.remove("animate-bounce-strong");

    glow.style.opacity = "0";
    glow.classList.remove("animate-pulse-glow");

    // Hide popup
    popup.classList.remove("show");
};

// Function to send message
// function sendMessage() {
//     const input = document.getElementById('chat-input');
//     const message = input.value.trim();

//     if (message) {
//         // Add user message
//         addMessage(message, true);
//         input.value = '';

//         // Show typing indicator
//         showTypingIndicator();

//         // Simulate AI response (replace with your actual API call)
//         setTimeout(() => {
//             removeTypingIndicator();
//             addMessage("I understand. Let me help you with that. This is where your AI response would go based on the user's question.", false);
//         }, 1500);
//     }
// }

function sendMessage() {
    const input = document.getElementById("chat-input") as HTMLInputElement;
    const message = input.value.trim();

    if (message) {
        addMessage(message, true);
        input.value = "";
        showTypingIndicator();
        fetchAIResponse(message);
    }
}

async function fetchAIResponse(userMessage) {
    try {
        console.log("sending request");
        const { data } = await api.get("/chat", {
            params: {
                message: userMessage,
            },
        });

        console.log(data);
        removeTypingIndicator();

        if (!data.success) {
            addMessage(data.error ?? "Something went wrong.", false);
            return;
        }

        addMessage(data.reply, false);
    } catch (err) {
        removeTypingIndicator();
        addMessage("Could not reach the server. Please try again.", false);
        console.error("Chat error:", err);
    }
}
// Event Listeners
document
    .getElementById("chatbot-button")
    .addEventListener("click", openChatbox);
document.getElementById("help-popup").addEventListener("click", openChatbox);
document.getElementById("close-chat").addEventListener("click", closeChatbox);

document.getElementById("send-message").addEventListener("click", sendMessage);

document
    .getElementById("chat-input")
    .addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            sendMessage();
        }
    });
// </script>
