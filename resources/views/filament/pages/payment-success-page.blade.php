<x-filament::page>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="max-w-2xl mx-auto bg-white p-6 md:p-8 shadow-lg rounded-lg text-sm" id="receipt">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800 border-b pb-2">
            <span class="px-4 py-2 rounded-lg">Receipt</span>
        </h2>

        <div class="mb-6 space-y-2 text-gray-700 p-2 rounded-lg">
            <p class="flex items-center space-x-2">
                <span><strong>Trx No:</strong> {{ $checkout['trx_no'] }}</span>
            </p>
            <p class="flex items-center space-x-2">
                <span><strong>Date:</strong>
                    {{ \Carbon\Carbon::parse($checkout['created_at'])->format('d M Y H:i') }}</span>
            </p>
            <p class="flex items-center space-x-2">
                <span><strong>Customer:</strong> {{ $checkout['customer_name'] }}</span>
            </p>
            <p class="flex items-center space-x-2">
                <span><strong>Type:</strong>
                    <span class="px-2 py-1 rounded-md">{{ $checkout['type'] }}</span>
                    @if ($checkout['type'] === 'Dine in')
                        (Table: {{ $checkout['table_id'] ?? '-' }})
                    @endif
                </span>
            </p>
        </div>

        <hr class="my-4 border-gray-300">

        <h3 class="font-bold text-gray-700 mb-3">Items</h3>

        <div class="space-y-3 text-gray-800">
            @foreach ($checkout['items'] as $item)
                <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <div>
                            <p class="font-medium">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['quantity'] }} × Rp
                                {{ number_format($item['price'], 0) }}</p>
                        </div>
                    </div>
                    <div class="text-right font-semibold">
                        Rp {{ number_format($item['price'] * $item['quantity'], 0) }}
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-2 border-gray-300">

        <div class=" text-gray-700 p-4 rounded-lg">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="font-medium">Rp {{ number_format($checkout['total_amount'] ?? 0, 0) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tax ({{ $checkout['tax_percentage'] ?? 0 }}%)</span>
                <span class="font-medium">Rp {{ number_format($checkout['tax'] ?? 0, 0) }}</span>
            </div>
            {{-- <div class="flex justify-between text-lg font-bold text-primary-600 pt-2 border-t border-gray-200">
                <span>Total Pembayaran</span>
                <span>Rp {{ number_format($checkout['total_amount'], 0) }}</span>
            </div> --}}
        </div>

        <hr class="my-2 border-gray-300">


        <div class="space-y-3 text-gray-700 p-4 rounded-lg">
            <div class="flex justify-between">
                <span>Total Amount</span>
                <span class="font-medium">Rp {{ number_format($checkout['total_amount'] ?? 0, 0) }}</span>
            </div>
            @foreach ($checkout['payments'] as $payment)
                <div class="flex justify-between text-lg font-bold text-primary-600 pt-2 border-t border-gray-200">
                    <span>{{ $payment->payment_method }}</span>
                    <span>Rp {{ number_format($payment->total, 0) }}</span>
                </div>
            @endforeach
            {{-- <div class="flex justify-between">
                <span>Tax ({{ $checkout['tax_percentage'] ?? 0 }}%)</span>
                <span class="font-medium">Rp {{ number_format($checkout['tax'] ?? 0, 0) }}</span>
            </div> --}}

        </div>

        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3 no-print">
            <x-filament::button icon="heroicon-o-printer" size="md" class="w-full sm:w-auto justify-center"
                onclick="window.print()">
                Cetak Struk
            </x-filament::button>

            <x-filament::button tag="a" href="" icon="heroicon-o-arrow-left" size="md"
                color="primary" class="w-full sm:w-auto justify-center">
                Kembali ke POS
            </x-filament::button>
        </div>
    </div>
</x-filament::page>
