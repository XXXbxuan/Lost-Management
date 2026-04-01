<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            AI Help Assistant
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                <div class="p-6">
                    <div class="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">ALIMS AI Help Assistant</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Ask about lost reports, found items, matching, claim flow, status meanings, or where to find a feature.
                            </p>
                        </div>

                        <button
                            id="clearChatBtn"
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Clear Chat
                        </button>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" class="quick-question rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-question="What does Matched status mean?">
                            Matched status
                        </button>
                        <button type="button" class="quick-question rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-question="What does Claimed status mean?">
                            Claimed status
                        </button>
                        <button type="button" class="quick-question rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-question="How do I undo a match?">
                            Undo match
                        </button>
                        <button type="button" class="quick-question rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-question="How does the claim process work?">
                            Claim process
                        </button>
                        <button type="button" class="quick-question rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-question="What does Removed mean?">
                            Removed status
                        </button>
                    </div>

                    <div id="chatBox" class="border rounded-2xl bg-slate-50 p-4 h-[460px] overflow-y-auto space-y-4">
                        <div class="flex justify-start">
                            <div class="max-w-[80%] rounded-2xl bg-white border px-4 py-3 text-sm text-gray-700 shadow-sm whitespace-pre-wrap leading-6">
                                Hi. I can help explain ALIMS workflows, statuses, and module navigation.
                            </div>
                        </div>
                    </div>

                    <form id="chatForm" class="mt-4 flex gap-3">
                        @csrf
                        <input
                            id="messageInput"
                            type="text"
                            name="message"
                            placeholder="Ask something like: What does Matched status mean?"
                            class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                        <button
                            id="sendButton"
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const chatBox = document.getElementById('chatBox');
        const sendButton = document.getElementById('sendButton');
        const clearChatBtn = document.getElementById('clearChatBtn');

        function appendMessage(content, role, action = null) {
            const wrapper = document.createElement('div');
            wrapper.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

            const bubble = document.createElement('div');
            bubble.className =
                role === 'user'
                    ? 'max-w-[80%] rounded-2xl bg-slate-900 text-white px-4 py-3 text-sm shadow-sm whitespace-pre-wrap leading-6'
                    : 'max-w-[80%] rounded-2xl bg-white border px-4 py-3 text-sm text-gray-700 shadow-sm whitespace-pre-wrap leading-6';

            const textDiv = document.createElement('div');
            textDiv.textContent = content;
            bubble.appendChild(textDiv);

            if (action && action.url && action.label) {
                const actionWrap = document.createElement('div');
                actionWrap.className = 'mt-3';

                const btn = document.createElement('a');
                btn.href = action.url;
                btn.textContent = action.label;
                btn.className = 'inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 no-underline';

                actionWrap.appendChild(btn);
                bubble.appendChild(actionWrap);
            }

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);
            chatBox.scrollTop = chatBox.scrollHeight;

            return wrapper;
        }

        async function sendMessage(message) {
            if (!message) return;

            appendMessage(message, 'user');
            messageInput.value = '';
            sendButton.disabled = true;
            sendButton.textContent = 'Sending...';

            const loadingBubble = appendMessage('Thinking...', 'assistant');

            try {
                const response = await fetch("{{ route('staff.ai-chat.ask') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                loadingBubble.remove();

                if (data.success) {
                    appendMessage(data.reply || 'No response.', 'assistant', data.action || null);
                } else {
                    appendMessage(data.reply || 'Sorry, the AI assistant is temporarily unavailable.', 'assistant');
                }
            } catch (error) {
                loadingBubble.remove();
                appendMessage('Sorry, the AI assistant is temporarily unavailable.', 'assistant');
            } finally {
                sendButton.disabled = false;
                sendButton.textContent = 'Send';
                messageInput.focus();
            }
        }

        chatForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            await sendMessage(message);
        });

        document.querySelectorAll('.quick-question').forEach(button => {
            button.addEventListener('click', async function () {
                const question = this.dataset.question;
                await sendMessage(question);
            });
        });

        clearChatBtn.addEventListener('click', async function () {
            try {
                await fetch("{{ route('staff.ai-chat.clear') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });

                chatBox.innerHTML = `
                    <div class="flex justify-start">
                        <div class="max-w-[80%] rounded-2xl bg-white border px-4 py-3 text-sm text-gray-700 shadow-sm whitespace-pre-wrap leading-6">
                            Hi. I can help explain ALIMS workflows, statuses, and module navigation.
                        </div>
                    </div>
                `;
            } catch (error) {
            }
        });
    </script>
</x-app-layout>