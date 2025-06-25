<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.pages.point-of-sale';

    public function getTitle(): string | Htmlable
    {
        $user = auth()->user();
        $branchId = session('pos_selected_branch') ?? $user->default_branch_id ?? null;

        if ($branchId && $branch = \App\Models\Branch::find($branchId)) {
            return $branch->name;
        }

        return 'Point of Sale';
    }


    public bool $showBranchModal = true;
    public $branches = [];
    public ?int $selectedBranchId = null;

    public $search = '';
    public $customerName;
    public $tableNo;
    public $products;
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;

    public $types = ['Dine in', 'Take away', 'Delivery'];
    public string $selectedType = '';

    public $tables;
    public $selectedTableId = null;

    public function selectBranch($branchId)
    {
        session()->put('pos_selected_branch', $branchId);
        $this->selectedBranchId = $branchId;
        $this->showBranchModal = false;
        $this->loadProducts();
        $this->loadTables();
    }

    public function mount(): void
    {
        $this->calculateTotals();
        $this->branches = Branch::all();
    }

    public function updatedSearch(): void
    {
        $this->loadProducts();
    }

    public function loadProducts(): void
    {
        $branch = Branch::findOrFail($this->selectedBranchId);
        $this->products = $this->selectedBranchId ? $branch->products()->get() :
            [];
    }

    public function loadTables(): void
    {
        $this->tables = $this->selectedBranchId ? Table::query()->where('branch_id', $this->selectedBranchId)->get() :
            [];
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
