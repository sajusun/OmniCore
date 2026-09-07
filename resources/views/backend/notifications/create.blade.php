<x-admin-layout>
    @slot('title')
        Bulk Notification
    @endslot
    @slot('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Send Bulk Notification
        </h2>
    @endslot

    <div class="max-w-4xl mx-auto mt-8">
        <x-card title="Send to All Users">
            @if(session('success'))
                <div class="mb-4 p-4 text-green-700 bg-green-100 dark:bg-green-200 dark:text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                    <input type="text" name="subject" id="subject" class="mt-1 block w-full border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required placeholder="e.g. System Update">
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                    <textarea name="message" id="message" rows="4" class="mt-1 block w-full border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required placeholder="Enter the notification content..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Send Notification
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-admin-layout>
