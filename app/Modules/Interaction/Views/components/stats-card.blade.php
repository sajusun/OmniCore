@props(['model'])

@php
    $likesCount = method_exists($model, 'likesCount') ? $model->likesCount() : ($model->likes_count ?? 0);
    $commentsCount = method_exists($model, 'commentsCount') ? $model->commentsCount() : ($model->comments_count ?? 0);
    $viewsCount = method_exists($model, 'viewsCount') ? $model->viewsCount() : ($model->views_count ?? 0);
    $uniqueViews = method_exists($model, 'uniqueViewsCount') ? $model->uniqueViewsCount() : 0;
    $bookmarksCount = method_exists($model, 'bookmarksCount') ? $model->bookmarksCount() : ($model->bookmarks_count ?? 0);
    $sharesCount = method_exists($model, 'sharesCount') ? $model->sharesCount() : 0;
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 my-4">
    {{-- Likes Card --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center font-bold text-lg">
            ❤️
        </div>
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Likes</div>
            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($likesCount) }}</div>
        </div>
    </div>

    {{-- Comments Card --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-500 flex items-center justify-center font-bold text-lg">
            💬
        </div>
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Comments</div>
            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($commentsCount) }}</div>
        </div>
    </div>

    {{-- Views Card --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 flex items-center justify-center font-bold text-lg">
            👁️
        </div>
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Views</div>
            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                {{ number_format($viewsCount) }}
                @if($uniqueViews > 0)
                    <span class="text-xs font-normal text-gray-400">({{ $uniqueViews }} unique)</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Bookmarks Card --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center font-bold text-lg">
            🔖
        </div>
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Bookmarks</div>
            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($bookmarksCount) }}</div>
        </div>
    </div>

    {{-- Shares Card --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-500 flex items-center justify-center font-bold text-lg">
            🔗
        </div>
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Shares</div>
            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($sharesCount) }}</div>
        </div>
    </div>
</div>
