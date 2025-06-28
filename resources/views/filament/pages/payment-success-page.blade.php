{{-- <x-filament::page>
    <div class="text-center p-10">
        <h1 class="text-2xl font-bold text-green-600 mb-4">Pembayaran Berhasil!</h1>
        <p class="text-gray-600">Terima kasih. Silakan ambil struk atau kembali ke halaman utama.</p>
        <x-filament::button tag="a" href="" class="mt-6">
            Kembali ke POS
        </x-filament::button>
    </div>
</x-filament::page> --}}

<x-filament::page>
    <div class="max-w-md mx-auto bg-white p-6 shadow rounded text-sm" id="receipt">
        <h2 class="text-xl font-bold text-center mb-4">Struk Pembelian</h2>

        <p><strong>Customer:</strong> {{ $checkout->customer_name }}</p>
        <p><strong>Tipe:</strong> {{ $checkout['type'] }}</p>
        @if ($checkout['type'] === 'Dine in')
            <p><strong>Meja:</strong> {{ $checkout['table_id'] }}</p>
        @endif

        <hr class="my-3">

        @foreach ($checkout['items'] as $item)
            <div class="flex justify-between">
                <span>{{ $item['quantity'] }} x {{ $item['name'] }}</span>
                <span>Rp {{ number_format($item['total'], 0) }}</span>
            </div>
        @endforeach

        <hr class="my-3">

        <div class="flex justify-between">
            <span>Subtotal</span>
            <span>Rp {{ number_format($checkout['subtotal'], 0) }}</span>
        </div>
        <div class="flex justify-between">
            <span>Tax</span>
            <span>Rp {{ number_format($checkout['tax'], 0) }}</span>
        </div>
        <div class="flex justify-between font-bold text-lg">
            <span>Total</span>
            <span>Rp {{ number_format($checkout->total_amount, 0) }}</span>
        </div>

        <div class="mt-4 text-center">
            <x-filament::button color="gray" onclick="window.print()">
                Cetak Struk
            </x-filament::button>
        </div>
    </div>
</x-filament::page>
