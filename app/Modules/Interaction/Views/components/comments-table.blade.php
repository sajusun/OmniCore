@props(['model', 'perPage' => 10])

@php
    $comments = method_exists($model, 'getComments') 
        ? $model->getComments($perPage) 
        : $model->comments()->with('user', 'replies.user')->paginate($perPage);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden my-4">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h3 class="font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <span>💬 Comments</span>
            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full font-semibold">
                {{ $comments->total() }}
            </span>
        </h3>
    </div>

    @if($comments->isEmpty())
        <div class="p-8 text-center text-gray-400 dark:text-gray-500 text-sm">
            No comments yet on this item.
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($comments as $comment)
                <div class="p-4 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name ?? 'User') }}" 
                                 alt="{{ $comment->user->name ?? 'Anonymous' }}" 
                                 class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-gray-100">
                                        {{ $comment->user->name ?? 'Unknown User' }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded {{ $comment->status === 'approved' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/30' }}">
                                        {{ $comment->status }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                    {{ $comment->body }}
                                </div>
                            </div>
                        </div>

                        {{-- Moderation Action --}}
                        <div class="flex items-center gap-2">
                            <form action="{{ url('/api/interactions/comments/' . $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Nested Replies --}}
                    @if($comment->replies && $comment->replies->isNotEmpty())
                        <div class="ml-12 mt-3 space-y-2 border-l-2 border-gray-100 dark:border-gray-700 pl-3">
                            @foreach($comment->replies as $reply)
                                <div class="flex items-start justify-between gap-2 text-xs">
                                    <div>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $reply->user->name ?? 'User' }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $reply->body }}</span>
                                        <span class="text-[10px] text-gray-400 ml-1">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <form action="{{ url('/api/interactions/comments/' . $reply->id) }}" method="POST" onsubmit="return confirm('Delete this reply?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-600">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($comments->hasPages())
            <div class="p-3 border-t border-gray-100 dark:border-gray-700">
                {{ $comments->links() }}
            </div>
        @endif
    @endif
</div>
