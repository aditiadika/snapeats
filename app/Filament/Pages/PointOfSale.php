<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Validate;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.pages.point-of-sale';

    public function getTitle(): string|Htmlable
    {
        return 'Point of Sale';
    }

    public bool $showBranchModal = true;

    public $branches = [];

    public ?int $selectedBranchId = null;

    public $branchName;

    public $search = '';

    #[Validate]
    public $customerName;

    public $tableNo;

    public $products;

    public $cart = [];

    public $subtotal = 0;

    public $tax = 0;

    public $total = 0;

    public $types = ['Dine in', 'Take away', 'Delivery'];

    #[Validate]
    public string $selectedType = '';

    public $tables;

    public $selectedTableId = null;

    public $categories = [];

    public $selectedCategoryId = null; // Ganti dari selectedCategory

    protected function rules()
    {
        return [
            'customerName' => 'required',
            'selectedType' => 'required|in:Dine in,Take away,Delivery',
        ];
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->showBranchModal = false;
        $this->loadProducts();
        $this->loadTables();
    }

    public function mount(): void
    {
        $this->calculateTotals();
        $this->branches = Branch::all();
        $this->loadCart();
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::query()
            ->where('entity_id', auth()->user()->entity_id)
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadProducts(); // Trigger ulang load
    }

    public function updatedSelectedCategoryId()
    {
        $this->loadProducts(); // Trigger ulang load
    }

    public function loadProducts(): void
    {
        $branch = Branch::findOrFail($this->selectedBranchId);
        $this->branchName = $branch->name;

        $this->products = $branch->products()
            ->when($this->selectedCategoryId, function ($query) {
                $query->where('category_id', $this->selectedCategoryId);
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%');
            })
            ->get();
    }

    public function loadCart()
    {
        $this->cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => optional($item->product)->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ];
            })
            ->toArray();

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->cart)->sum('total');
        $this->tax = 0;
        $this->total = $this->subtotal + $this->tax;
    }

    public function loadTables(): void
    {
        $this->tables = $this->selectedBranchId ? Table::query()->where('branch_id', $this->selectedBranchId)->get() :
            [];
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
                'notes' => null,
            ]);
        }

        $this->loadCart();
    }

    public function removeItem($id): void
    {
        Cart::where('id', $id)->where('user_id', auth()->id())->delete();
        $this->loadCart();
    }

    public function checkout(): void
    {
        $this->validate();

        // Simpan cart ke session atau database
        Session::put('checkout_data', [
            'customer_name' => $this->customerName,
            'branch_id' => $this->selectedBranchId,
            'table_id' => $this->selectedTableId,
            'type' => $this->selectedType,
            'items' => $this->cart,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
        ]);

        Notification::make()
            ->title('Proceeding to Payment')
            ->success()
            ->send();

        $this->redirectRoute('filament.admin.pages.payment-page');
    }

    public function holdTransaction(): void
    {
        // Simpan status sebagai 'hold' jika sistem Anda mendukung
        $this->dispatch('notify', title: 'Transaksi di-hold');
    }
}
