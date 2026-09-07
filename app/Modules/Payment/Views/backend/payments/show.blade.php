<x-admin-layout>
    <x-slot name="title">Payment #{{ $payment->payment_id }}</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Details #{{ $payment->payment_id }}</h2>
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 text-sm border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 text-sm border border-red-200">
                {{ session('error') }}
            </div>
            @endif

            <!-- Main Overview Card -->
            <div class="bg-white shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Overview</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Payment ID:</dt>
                                <dd class="font-mono font-bold text-gray-800">{{ $payment->payment_id }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Status:</dt>
                                <dd class="font-bold uppercase text-xs px-2 py-1 {{ $payment->isCompleted() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $payment->status?->value ?? $payment->status }}
                                </dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Method:</dt>
                                <dd class="font-semibold uppercase text-gray-700">{{ $payment->method?->value ?? $payment->method }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Gateway Trx ID:</dt>
                                <dd class="font-mono text-gray-700">{{ $payment->gateway_transaction_id ?: 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Paid At:</dt>
                                <dd class="text-gray-700">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i:s') : 'Pending' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Customer & Payable</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Customer Name:</dt>
                                <dd class="font-semibold text-gray-800">{{ $payment->user?->name ?? 'Guest/Deleted' }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Customer Email:</dt>
                                <dd class="text-gray-700">{{ $payment->user?->email }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Payable Target:</dt>
                                <dd class="font-medium text-gray-800">{{ class_basename($payment->payable_type) }} #{{ $payment->payable_id }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Amount:</dt>
                                <dd class="text-gray-700">${{ number_format($payment->amount, 2) }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Gateway Fee:</dt>
                                <dd class="text-gray-700">${{ number_format($payment->fee, 2) }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="font-bold text-gray-800">Total Charged:</dt>
                                <dd class="font-bold text-emerald-600 text-base">${{ number_format($payment->amount + ($payment->fee ?? 0), 2) }} {{ $payment->currency }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Admin Action Buttons -->
                <div class="mt-8 pt-6 border-t flex gap-4">
                    @if($payment->isPending())
                    <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" onsubmit="return confirm('Mark this payment as manually completed?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm">
                            Mark as Paid (Admin Confirm)
                        </button>
                    </form>
                    @endif

                    @if($payment->isCompleted())
                    <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" onsubmit="return confirm('Process refund for this payment?')">
                        @csrf
                        <input type="text" name="reason" placeholder="Refund reason (optional)" class="text-xs border px-2 py-1 mr-2">
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-sm">
                            Process Refund
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
