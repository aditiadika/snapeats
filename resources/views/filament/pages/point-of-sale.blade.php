<x-filament::page>
    <div class="flex flex-col md:flex-row gap-6">
        {{-- Produk Kiri --}}
        <div class="w-full md:w-2/3">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Cari produk..."
                class="w-full mb-4 rounded-lg border-gray-300 px-4 py-2" />

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($products as $product)
                    <button wire:click="addToCart({{ $product->id }})"
                        class="rounded-lg border bg-white shadow hover:shadow-md p-3 text-left">
                        <img src="{{ $product->image_path ?? 'https://placehold.co/150' }}"
                            class="w-full h-32 object-cover rounded mb-2" />
                        <div class="font-bold">{{ $product->name }}</div>
                        <div class="text-sm text-gray-500">Rp {{ number_format($product->price, 0) }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $product->stock ?? 0 }} Qty</div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Cart Kanan --}}
        <div
            class="w-full md:w-1/3 flex flex-col justify-between bg-white shadow rounded p-4 max-h-[85vh] overflow-y-auto">
            <div>
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-700">Customer Name</label>
                    <input type="text" wire:model="customerName" class="w-full border rounded px-3 py-2 mt-1"
                        placeholder="e.g. Robert Taylor" />
                </div>
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-700">Table No</label>
                    <input type="text" wire:model="tableNo" class="w-full border rounded px-3 py-2 mt-1"
                        placeholder="e.g. 5" />
                </div>

                <h2 class="text-lg font-bold mb-3">Cart</h2>

                @forelse ($cart as $index => $item)
                    <div class="flex justify-between items-start mb-3 border-b pb-2">
                        <div class="flex flex-col">
                            <div class="font-semibold">{{ $item['name'] }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0) }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-semibold text-sm text-gray-800">
                                Rp {{ number_format($item['total'], 0) }}
                            </div>
                            <button wire:click="removeItem({{ $index }})"
                                class="text-red-500 text-sm mt-1">✕</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Cart kosong</p>
                @endforelse

            </div>

            {{-- Footer Cart --}}
            <div class="mt-6 border-t pt-4">
                <div class="flex justify-between text-sm mb-1">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Tax</span>
                    <span>Rp {{ number_format($tax, 0) }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg mb-4">
                    <span>Payable</span>
                    <span>Rp {{ number_format($total, 0) }}</span>
                </div>

                <div class="flex gap-2">
                    <x-filament::button wire:click="holdTransaction" color="gray" size="md"
                        class="w-1/2 justify-center" icon="heroicon-s-lock-closed">
                        Hold Order
                    </x-filament::button>

                    <x-filament::button wire:click="checkout" color="success" size="md"
                        class="w-1/2 justify-center" icon="heroicon-s-check-circle">
                        Proceed
                    </x-filament::button>
                </div>

            </div>
        </div>
    </div>
</x-filament::page>
