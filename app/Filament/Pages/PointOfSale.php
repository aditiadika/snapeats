<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'POS';
    protected static string $view = 'filament.pages.point-of-sale';

    public $search = '';
    public $customerName;
    public $tableNo;
    public $products;
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;

    public function mount(): void
    {
        $this->loadProducts();
        $this->calculateTotals();
    }

    public function updatedSearch(): void
    {
        $this->loadProducts();
    }

    public function loadProducts(): void
    {
        $this->products = Product::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->get();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $index = collect($this->cart)->search(fn($item) => $item['product_id'] === $productId);

        if ($index !== false) {
            $this->cart[$index]['quantity']++;
            $this->cart[$index]['total'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
                'total' => $product->price,
            ];
        }

        $this->calculateTotals();
    }

    public function removeItem(int $index): void
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->cart)->sum('total');
        $this->tax = 0; // Tambahkan logic pajak jika perlu
        $this->total = $this->subtotal + $this->tax;
    }

    public function checkout(): void
    {
        $order = Order::create([
            'customer_name' => $this->customerName,
            'table_no' => $this->tableNo,
            'total' => $this->total,
            'payment_method' => 'cash',
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'pos_transaction_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        $this->reset(['cart', 'customerName', 'tableNo', 'search']);
        $this->loadProducts();
        $this->calculateTotals();

        $this->dispatch('notify', title: 'Transaksi berhasil diproses');
    }

    public function holdTransaction(): void
    {
        // Simpan status sebagai 'hold' jika sistem Anda mendukung
        $this->dispatch('notify', title: 'Transaksi di-hold');
    }
}
