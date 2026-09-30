@include('layouts.header')

<div class="min-h-screen flex">
    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col">
        <main class="flex-1 p-2 m-4">
            {{ $slot }}
        </main>
        @include('layouts.footer')
    </div>

    <!-- Uniform Loader Component -->
    <x-ui.loader id="global-loader" text="Loading..." subtext="Please wait a moment" />


    <!-- Chat Box -->
    <div id="chatbox"
        class="fixed bottom-24 right-6 w-96 h-[500px] bg-white rounded-2xl shadow-2xl z-50
                             opacity-0 scale-95 pointer-events-none transition-all duration-300 flex flex-col overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold">AI Assistant</h3>
                    <p class="text-white/80 text-xs">Always here to help</p>
                </div>
            </div>
            <button id="close-chat" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
            <!-- Messages will be appended here -->
        </div>

        <!-- Chat Input -->
        <div class="p-4 bg-white border-t border-gray-200">
            <div class="flex gap-2">
                <input type="text" id="chat-input" placeholder="Type your message..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button id="send-message" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg
                                                 hover:from-blue-700 hover:to-purple-700 transition-all font-medium">
                    Send
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Chatbot Button -->
    <div id="chatbot-container" class="fixed bottom-6 right-6 z-50">
        <!-- Help Popup -->
        <div id="help-popup"
            class="absolute bottom-20 right-0 opacity-0 scale-0 transition-all duration-300 pointer-events-none">
            <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg shadow-xl
                        whitespace-nowrap animate-bounce-gentle cursor-pointer">
                <span class="font-medium text-sm">Need help?</span>
                <!-- Arrow pointing down -->
                <div class="absolute -bottom-2 right-6 w-0 h-0 
                            border-l-8 border-l-transparent 
                            border-r-8 border-r-transparent 
                            border-t-8 border-t-purple-600">
                </div>
            </div>
        </div>

        <!-- Animated Rings -->
        <div id="chatbot-rings" class="absolute inset-0 -m-2 opacity-0 transition-opacity duration-300">
            <div class="absolute inset-0 rounded-full border-4 border-blue-400/40 animate-ping-slow"></div>
            <div class="absolute inset-0 rounded-full border-4 border-purple-400/40 animate-ping-slower"></div>
        </div>

        <!-- Rotating Gradient Border -->
        <div id="chatbot-border" class="absolute -inset-1 rounded-full bg-gradient-to-r from-blue-400 via-purple-500 via-pink-500 to-blue-400 
                    opacity-0 blur-sm transition-opacity duration-300"></div>

        <button id="chatbot-button" class="relative w-16 h-16 rounded-full shadow-2xl
                       bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500
                       hover:from-blue-600 hover:via-purple-600 hover:to-pink-600
                       flex items-center justify-center
                       transition-all duration-300 ease-in-out
                       hover:scale-110 hover:shadow-purple-500/50
                       group">

            <!-- Sparkle Effects -->
            <div id="chatbot-sparkles"
                class="absolute inset-0 overflow-hidden rounded-full opacity-0 transition-opacity duration-300">
                <div class="sparkle sparkle-1"></div>
                <div class="sparkle sparkle-2"></div>
                <div class="sparkle sparkle-3"></div>
            </div>

            <!-- Chat Icon -->
            <svg class="w-8 h-8 text-white transition-transform group-hover:scale-110 group-hover:rotate-12 relative z-10"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                </path>
            </svg>

            <!-- Notification Badge -->
            <span id="chatbot-badge" class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-red-400 to-red-600 rounded-full 
                         flex items-center justify-center text-xs text-white font-bold
                         shadow-lg shadow-red-500/50 z-20 opacity-0 scale-0 transition-all duration-300">
                !
            </span>

            <!-- Pulsing Glow Effect -->
            <span id="chatbot-glow" class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-400 via-purple-400 to-pink-400 
                         opacity-0 blur-2xl transition-opacity duration-300">
            </span>
        </button>
    </div>

    <style>
        /* Floating Animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            25% {
                transform: translateY(-8px) scale(1.05);
            }

            50% {
                transform: translateY(-4px) scale(1.08);
            }

            75% {
                transform: translateY(-10px) scale(1.03);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Gentle bounce for popup */
        @keyframes bounce-gentle {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-bounce-gentle {
            animation: bounce-gentle 2s ease-in-out infinite;
        }

        /* Gradient Shift - More Dramatic */
        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        #chatbot-button {
            background-size: 300% 300%;
        }

        #chatbot-button.error-active {
            animation: gradient-shift 3s ease infinite, float 3s ease-in-out infinite;
        }

        /* Spinning Gradient Border */
        @keyframes spin-gradient {
            0% {
                transform: rotate(0deg) scale(1);
            }

            50% {
                transform: rotate(180deg) scale(1.1);
            }

            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        .animate-spin-gradient {
            background-size: 200% 200%;
            animation: spin-gradient 3s linear infinite;
        }

        /* Ping Effects */
        @keyframes ping-slow {
            0% {
                transform: scale(1);
                opacity: 0.4;
            }

            50% {
                transform: scale(1.3);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 0.4;
            }
        }

        @keyframes ping-slower {
            0% {
                transform: scale(1);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 0.3;
            }
        }

        .animate-ping-slow {
            animation: ping-slow 2s ease-in-out infinite;
        }

        .animate-ping-slower {
            animation: ping-slower 3s ease-in-out infinite 0.5s;
        }

        /* Sparkle Effects */
        .sparkle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 6px 2px rgba(255, 255, 255, 0.8);
        }

        .sparkle-1 {
            top: 15%;
            left: 20%;
            animation: sparkle-twinkle 1.5s ease-in-out infinite;
        }

        .sparkle-2 {
            top: 70%;
            right: 15%;
            animation: sparkle-twinkle 2s ease-in-out infinite 0.5s;
        }

        .sparkle-3 {
            bottom: 25%;
            left: 70%;
            animation: sparkle-twinkle 1.8s ease-in-out infinite 1s;
        }

        @keyframes sparkle-twinkle {

            0%,
            100% {
                opacity: 0;
                transform: scale(0);
            }

            50% {
                opacity: 1;
                transform: scale(1.5);
            }
        }

        /* Strong Bounce for Badge */
        @keyframes bounce-strong {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-8px) scale(1.1);
            }
        }

        .animate-bounce-strong {
            animation: bounce-strong 1s ease-in-out infinite;
        }

        /* Pulsing Glow */
        @keyframes pulse-glow {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(0.95);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.05);
            }
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        /* Enhanced hover effects */
        #chatbot-button:hover {
            transform: scale(1.25);
        }

        #chatbot-button.error-active:hover {
            animation: float 1.5s ease-in-out infinite;
        }

        /* Shake animation for error state */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        /* Popup visibility */
        #help-popup.show {
            opacity: 1;
            scale: 1;
            pointer-events: auto;
        }

        /* Chatbox visibility */
        #chatbox.show {
            opacity: 1;
            scale: 1;
            pointer-events: auto;
        }

        /* Typing indicator animation */
        @keyframes typing {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        .typing-dot {
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Custom scrollbar for chat */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* ///////////////////////////// */
        /* AI markdown rendering */
#chat-messages .prose p       { margin: 0.2rem 0; }
#chat-messages .prose ul      { list-style: disc; padding-left: 1.2rem; margin: 0.3rem 0; }
#chat-messages .prose ol      { list-style: decimal; padding-left: 1.2rem; margin: 0.3rem 0; }
#chat-messages .prose li      { margin: 0.15rem 0; }
#chat-messages .prose strong  { font-weight: 600; }
#chat-messages .prose code    { background: #f3f4f6; padding: 0.1rem 0.3rem; border-radius: 4px; font-size: 0.82em; }
#chat-messages .prose pre     { background: #f3f4f6; padding: 0.6rem; border-radius: 6px; overflow-x: auto; }
#chat-messages .prose h1,
#chat-messages .prose h2,
#chat-messages .prose h3      { font-weight: 600; margin: 0.4rem 0 0.2rem; }
    </style>

    
</div>