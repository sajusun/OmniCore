@props(['model', 'limit' => 12])

@php
    $likers = method_exists($model, 'likers') ? $model->likers($limit) : collect();
    $totalLikes = method_exists($model, 'likesCount') ? $model->likesCount() : ($model->likes_count ?? 0);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm my-4">
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <span>❤️ Liked by</span>
            <span class="text-xs bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-semibold px-2 py-0.5 rounded-full">
                {{ $totalLikes }}
            </span>
        </h4>
    </div>

    @if($likers->isEmpty())
        <div class="text-xs text-gray-400 dark:text-gray-500 py-2">
            No likes yet.
        </div>
    @else
        <div class="flex items-center -space-x-2 overflow-hidden py-1">
            @foreach($likers as $liker)
                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover" 
                     src="{{ $liker->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($liker->name ?? 'User') }}" 
                     alt="{{ $liker->name ?? 'User' }}" 
                     title="{{ $liker->name ?? 'User' }}">
            @endforeach

            @if($totalLikes > $limit)
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold ring-2 ring-white dark:ring-gray-800">
                    +{{ $totalLikes - $limit }}
                </span>
            @endif
        </div>
    @endif
</div>
