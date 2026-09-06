<x-admin-layout>
    @slot('title')
        AI Assistant & Knowledge Base
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                AI Assistant & Automated Knowledge Base
            </h2>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Conversations</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalConversations) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Messages Handled</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalMessages) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Knowledge FAQs</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalFaqs) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Tokens Processed</p>
                <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totalTokens) }}</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add Knowledge Base Form -->
        <x-card title="Add New Knowledge Base Entry / FAQ">
            <form action="{{ route('admin.ai.knowledge-base.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <input type="text" name="category" required placeholder="e.g. Orders, Refunds, Shipping" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keywords (Comma separated)</label>
                        <input type="text" name="keywords" placeholder="e.g. return, refund, damaged item" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question / Trigger Prompt</label>
                    <input type="text" name="question" required placeholder="e.g. How do I request a refund?" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resolution / Bot Answer</label>
                    <textarea name="answer" rows="3" required placeholder="Provide the exact instructions or answer the AI bot will give..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium text-sm">
                    Save Knowledge Base Entry
                </button>
            </form>
        </x-card>

        <!-- Knowledge Base List -->
        <x-card title="Knowledge Base Index">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Answer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hits</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($knowledgeBases as $kb)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">{{ $kb->category }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">{{ $kb->question }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-sm truncate">{{ $kb->answer }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $kb->hit_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('admin.ai.knowledge-base.destroy', $kb->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this entry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No knowledge base items yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $knowledgeBases->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
