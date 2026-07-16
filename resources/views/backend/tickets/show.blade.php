<x-admin-layout>
    @slot('title')
        Ticket #{{ $ticket->id }}
    @endslot
    @slot('header')
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $ticket->subject }}
            </h2>
            @if($ticket->status === 'open')
                <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Close Ticket
                    </button>
                </form>
            @else
                <span class="px-3 py-1 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full text-sm font-semibold">Closed</span>
            @endif
        </div>
    @endslot

    <div class="max-w-5xl mx-auto mt-8 flex flex-col gap-6">
        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @foreach($ticket->messages as $msg)
                <div class="flex {{ $msg->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="{{ $msg->user_id === Auth::id() ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }} shadow-sm p-4 rounded-lg max-w-2xl border {{ $msg->user_id === Auth::id() ? 'border-indigo-700' : 'border-gray-200 dark:border-gray-700' }}">
                        <div class="text-xs {{ $msg->user_id === Auth::id() ? 'text-indigo-200' : 'text-gray-500' }} mb-2 font-semibold">
                            {{ $msg->user->name ?? 'Unknown' }} &bull; {{ $msg->created_at->diffForHumans() }}
                        </div>
                        <p class="whitespace-pre-wrap">{{ $msg->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <x-card title="Reply">
            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required placeholder="Type your reply here..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Send Reply
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-admin-layout>
