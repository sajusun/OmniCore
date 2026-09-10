<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AI Copilot Engine</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        dark: {
                            900: '#0b0f19',
                            800: '#111827',
                            700: '#1f2937',
                            600: '#374151',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Marked.js for real-time Markdown Parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Highlight.js for Syntax Highlighting -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/styles/atom-one-dark.min.css">
    <script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.9.0/build/highlight.min.js"></script>

    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #111827; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4b5563; }
        
        pre {
            position: relative;
            background: #0d1117 !important;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 0.75rem 0;
            overflow-x: auto;
            border: 1px solid #30363d;
        }
        code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.85rem;
        }
        .prose p { margin-bottom: 0.75rem; }
        .prose p:last-child { margin-bottom: 0; }
        .prose ul { list-style-type: disc; margin-left: 1.25rem; margin-bottom: 0.75rem; }
        .prose ol { list-style-type: decimal; margin-left: 1.25rem; margin-bottom: 0.75rem; }
    </style>
</head>
<body class="bg-dark-900 text-gray-100 h-full font-sans antialiased overflow-hidden select-none"
      x-data="aiCopilot()"
      x-init="initCopilot()">

    <div class="flex h-screen w-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-72 bg-dark-800 border-r border-gray-800 flex flex-col justify-between shrink-0">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">
                        ⚡
                    </div>
                    <span class="font-semibold tracking-wide text-sm">Ollama Copilot</span>
                </div>
                <button @click="startNewConversation()" 
                        class="p-1.5 rounded-md hover:bg-gray-700 text-gray-400 hover:text-white transition-colors"
                        title="New Chat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>

            <!-- History List -->
            <div class="flex-1 overflow-y-auto px-2 py-3 space-y-1">
                <div class="text-[11px] font-semibold text-gray-500 px-3 uppercase tracking-wider mb-2">Recent Sessions</div>
                <template x-for="conv in conversations" :key="conv.id">
                    <div @click="loadConversation(conv.id)"
                         :class="activeConversationId === conv.id ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-gray-300 hover:bg-dark-700'"
                         class="group flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer text-sm transition-all">
                        <div class="flex items-center space-x-2.5 truncate">
                            <span class="text-xs">💬</span>
                            <span class="truncate" x-text="conv.title"></span>
                        </div>
                        <button @click.stop="deleteConversation(conv.id)" 
                                class="opacity-0 group-hover:opacity-100 text-gray-500 hover:text-red-400 p-1 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Persona & Status Footer -->
            <div class="p-4 border-t border-gray-800 bg-dark-900/60">
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Active Persona</label>
                <select x-model="selectedPersonaId" 
                        class="w-full bg-dark-800 border border-gray-700 text-xs rounded-md px-2.5 py-2 text-gray-200 focus:ring-1 focus:ring-indigo-500">
                    <template x-for="persona in personas" :key="persona.id">
                        <option :value="persona.id" x-text="persona.name"></option>
                    </template>
                </select>

                <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500">
                    <span class="flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Ollama Local</span>
                    </span>
                    <span class="font-mono text-[10px]" x-text="selectedModel"></span>
                </div>
            </div>
        </aside>

        <!-- MAIN CHAT AREA -->
        <main class="flex-1 flex flex-col h-full bg-dark-900 relative">
            
            <!-- Top Bar -->
            <header class="h-14 border-b border-gray-800 px-6 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <h2 class="font-medium text-sm text-gray-200" x-text="currentTitle || 'New Code Session'"></h2>
                </div>
                <!-- Model Selector -->
                <div class="flex items-center space-x-3">
                    <select x-model="selectedModel" 
                            class="bg-dark-800 border border-gray-700 text-xs text-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:border-indigo-500">
                        <template x-for="m in models" :key="m">
                            <option :value="m" x-text="m"></option>
                        </template>
                    </select>
                </div>
            </header>

            <!-- Messages Stream Area -->
            <div id="messages-container" 
                 class="flex-1 overflow-y-auto px-4 py-6 space-y-6 max-w-4xl mx-auto w-full">
                
                <template x-if="messages.length === 0 && !streaming">
                    <div class="h-full flex flex-col items-center justify-center text-center text-gray-500 my-auto pt-24">
                        <div class="w-12 h-12 rounded-2xl bg-dark-800 border border-gray-700 flex items-center justify-center text-2xl mb-4">
                            ⚡
                        </div>
                        <h3 class="text-base font-semibold text-gray-300">Local AI Engineering Copilot</h3>
                        <p class="text-xs text-gray-500 max-w-sm mt-1">
                            Ask for refactorings, bug fixes, unit tests, or architecture diagrams without data leaving your local machine.
                        </p>
                    </div>
                </template>

                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'justify-end' : 'justify-start'" class="flex items-start space-x-3">
                        <template x-if="msg.role === 'assistant'">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 font-bold text-xs shrink-0 mt-1">
                                AI
                            </div>
                        </template>

                        <div :class="msg.role === 'user' 
                                ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-xl text-sm leading-relaxed shadow-sm' 
                                : 'bg-dark-800 border border-gray-800 text-gray-200 rounded-2xl rounded-tl-sm px-5 py-4 max-w-3xl w-full text-sm leading-relaxed shadow-lg overflow-x-auto'"
                             class="select-text">
                            <div class="markdown-body prose prose-invert max-w-none text-sm" 
                                 x-html="renderMarkdown(msg.content)"></div>
                        </div>
                    </div>
                </template>

                <!-- Active SSE Streaming Chunk -->
                <div x-show="streaming" class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 font-bold text-xs shrink-0 mt-1">
                        AI
                    </div>
                    <div class="bg-dark-800 border border-gray-800 text-gray-200 rounded-2xl rounded-tl-sm px-5 py-4 max-w-3xl w-full text-sm leading-relaxed shadow-lg overflow-x-auto select-text">
                        <div class="markdown-body prose prose-invert max-w-none text-sm" 
                             x-html="renderMarkdown(currentStreamingChunk)"></div>
                        <span class="inline-block w-2 h-4 bg-indigo-500 animate-pulse ml-1 align-middle"></span>
                    </div>
                </div>
            </div>

            <!-- Input Box Bottom Bar -->
            <div class="p-4 border-t border-gray-800 bg-dark-900/90 backdrop-blur">
                <div class="max-w-4xl mx-auto">
                    <form @submit.prevent="sendMessage()" class="relative flex items-center">
                        <textarea 
                            x-model="inputPrompt" 
                            @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                            rows="1"
                            placeholder="Ask Copilot (Shift + Enter for new line)..."
                            :disabled="streaming"
                            class="w-full bg-dark-800 border border-gray-700 text-gray-200 rounded-xl pl-4 pr-12 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none transition-all placeholder-gray-500"></textarea>

                        <button type="submit" 
                                :disabled="streaming || !inputPrompt.trim()"
                                :class="streaming || !inputPrompt.trim() ? 'opacity-40 cursor-not-allowed' : 'hover:bg-indigo-500 text-white'"
                                class="absolute right-2.5 p-2 rounded-lg bg-indigo-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </form>
                    <div class="text-center mt-2 text-[11px] text-gray-500">
                        Local inferences run on Ollama. Private & strictly offline.
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Alpine.js Copilot Streaming Controller -->
    <script>
        function aiCopilot() {
            return {
                conversations: [],
                personas: @json($personas),
                models: @json($models),
                selectedPersonaId: @json($personas->first()->id ?? null),
                selectedModel: @json($models[0] ?? 'qwen2.5-coder:7b'),
                activeConversationId: null,
                currentTitle: '',
                messages: [],
                inputPrompt: '',
                streaming: false,
                currentStreamingChunk: '',

                initCopilot() {
                    marked.setOptions({
                        highlight: function(code, lang) {
                            const language = hljs.getLanguage(lang) ? lang : 'plaintext';
                            return hljs.highlight(code, { language }).value;
                        },
                        breaks: true,
                        gfm: true
                    });

                    this.fetchConversations();
                },

                renderMarkdown(content) {
                    if (!content) return '';
                    return marked.parse(content);
                },

                async fetchConversations() {
                    try {
                        const res = await fetch("{{ route('ai-copilot.conversations.index') }}");
                        this.conversations = await res.json();
                    } catch (err) {
                        console.error('Failed to load conversations', err);
                    }
                },

                async loadConversation(id) {
                    if (this.streaming) return;
                    this.activeConversationId = id;
                    try {
                        const res = await fetch(`{{ url(config('ai-assistant.routes.prefix', 'ai-copilot')) }}/conversations/${id}`);
                        const data = await res.json();
                        this.currentTitle = data.title;
                        this.selectedModel = data.model_used || this.selectedModel;
                        this.selectedPersonaId = data.persona_id || this.selectedPersonaId;
                        this.messages = data.messages || [];
                        this.scrollToBottom();
                    } catch (err) {
                        console.error('Failed to load conversation details', err);
                    }
                },

                async startNewConversation() {
                    if (this.streaming) return;
                    this.activeConversationId = null;
                    this.currentTitle = 'New Code Session';
                    this.messages = [];
                    this.inputPrompt = '';
                },

                async deleteConversation(id) {
                    if (!confirm('Are you sure you want to delete this session?')) return;
                    try {
                        await fetch(`{{ url(config('ai-assistant.routes.prefix', 'ai-copilot')) }}/conversations/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        this.conversations = this.conversations.filter(c => c.id !== id);
                        if (this.activeConversationId === id) {
                            this.startNewConversation();
                        }
                    } catch (err) {
                        console.error('Failed to delete session', err);
                    }
                },

                async sendMessage() {
                    const prompt = this.inputPrompt.trim();
                    if (!prompt || this.streaming) return;

                    if (!this.activeConversationId) {
                        try {
                            const convRes = await fetch("{{ route('ai-copilot.conversations.store') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    persona_id: this.selectedPersonaId,
                                    model: this.selectedModel,
                                    title: prompt.substring(0, 30)
                                })
                            });
                            const conv = await convRes.json();
                            this.activeConversationId = conv.id;
                            this.currentTitle = conv.title;
                            this.conversations.unshift(conv);
                        } catch (e) {
                            alert('Could not initialize conversation');
                            return;
                        }
                    }

                    this.messages.push({ role: 'user', content: prompt });
                    this.inputPrompt = '';
                    this.streaming = true;
                    this.currentStreamingChunk = '';
                    this.scrollToBottom();

                    try {
                        const response = await fetch("{{ route('ai-copilot.chat.stream') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'text/event-stream'
                            },
                            body: JSON.stringify({
                                conversation_id: this.activeConversationId,
                                message: prompt,
                                persona_id: this.selectedPersonaId,
                                model: this.selectedModel
                            })
                        });

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder('utf-8');
                        let buffer = '';

                        while (true) {
                            const { value, done } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            const lines = buffer.split('\n\n');
                            buffer = lines.pop();

                            for (const line of lines) {
                                if (line.startsWith('data: ')) {
                                    const jsonStr = line.replace('data: ', '').trim();
                                    if (jsonStr === '{}') continue;

                                    try {
                                        const data = JSON.parse(jsonStr);
                                        if (data.chunk) {
                                            this.currentStreamingChunk += data.chunk;
                                            this.scrollToBottom();
                                        }
                                    } catch (err) {}
                                }
                            }
                        }

                        if (this.currentStreamingChunk) {
                            this.messages.push({
                                role: 'assistant',
                                content: this.currentStreamingChunk
                            });
                        }
                    } catch (err) {
                        console.error('Streaming error', err);
                        this.messages.push({
                            role: 'assistant',
                            content: '⚠️ *An error occurred while streaming response from Ollama.*'
                        });
                    } finally {
                        this.streaming = false;
                        this.currentStreamingChunk = '';
                        this.fetchConversations();
                        this.scrollToBottom();
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('messages-container');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
