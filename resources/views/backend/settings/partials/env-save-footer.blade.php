<div class="mt-8 pt-6 border-t border-gray-200/60 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-4">
    <div class="text-sm text-gray-500 dark:text-gray-400">
        <span class="inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Changes directly update the <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-xs">.env</code> file
        </span>
    </div>
    <button
        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200 hover:shadow-indigo-300/50 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-300/50"
        type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
        </svg>
        {{ $label ?? 'Save Settings' }}
    </button>
</div>
