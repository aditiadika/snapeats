<x-filament::page>
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Pembayaran</h2>

        <div class="mb-4">
            <p><strong>Customer:</strong> {{ $checkout['customer_name'] }}</p>
            <p><strong>Type:</strong> {{ $checkout['type'] }}</p>
            @if ($checkout['type'] === 'Dine in')
                <p><strong>Table:</strong> {{ $checkout['table_id'] }}</p>
            @endif
        </div>

        <div class="border-t pt-3">
            @foreach ($checkout['items'] as $item)
                <div class="flex justify-between text-sm mb-2">
                    <span>{{ $item['quantity'] }} x {{ $item['name'] }}</span>
                    <span>Rp {{ number_format($item['total'], 0) }}</span>
                </div>
            @endforeach
        </div>

        <div class="border-t pt-4 mt-3 text-sm">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rp {{ number_format($checkout['subtotal'], 0) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tax</span>
                <span>Rp {{ number_format($checkout['tax'], 0) }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg mt-2">
                <span>Total</span>
                <span>Rp {{ number_format($checkout['total'], 0) }}</span>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
            <select wire:model.live="paymentMethod" class="w-full border rounded px-3 py-2">
                <option value="">Pilih metode</option>
                <option value="qris">QRIS</option>
                <option value="cash">Tunai</option>
                <option value="split">Split Payment</option>
            </select>
        </div>

        @if ($paymentMethod === 'split')
            <div class="mt-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Pembayaran Tunai</label>
                    <input type="number" wire:model.live="splitCash" class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Pembayaran QRIS</label>
                    <input type="number" wire:model.live="splitQris" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="text-sm text-gray-500">
                    Total Split: Rp {{ number_format(($splitCash ?? 0) + ($splitQris ?? 0), 0) }}
                </div>
            </div>
        @endif

        <div class="mt-6">
            <x-filament::button color="primary" wire:click="payNow">
                Bayar Sekarang
            </x-filament::button>
        </div>

        @if ($paymentMethod === 'qris' && $showQrisCode)
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Silakan scan QRIS untuk membayar</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('https://contoh-pembayaran.com/tx/123456') }}"
                    alt="QR Code QRIS" class="mx-auto" />
            </div>
        @endif

        @if ($errors->has('split'))
            <div class="mt-4 text-sm text-red-500">
                {{ $errors->first('split') }}
            </div>
        @endif
    </div>
</x-filament::page>
