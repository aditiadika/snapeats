<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Livewire\Component;

class MenuList extends Component
{
    public $tableId;
    public $qrCode;
    // public $search = '';
    public $activeCategory = 'all';
    public $categories = [];

    public array $search = [];
    public array $carts = [];

    public function mount($qrCode)
    {
        $this->qrCode = $qrCode;
    }

    public function render()
    {
        $table = Table::where('qr_code', $this->qrCode)->firstOrFail();

        $this->tableId = $table->id;

        $products = $table->branch->products()
            ->when($this->activeCategory !== 'all', function ($query) {
                $query->where('category_id', $this->activeCategory);
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'ilike', '%' . $this->search . '%');
            })
            ->where('is_active', true)
            ->get();

        $this->categories = Category::where('entity_id', $table->entity_id)
            ->get()
            ->keyBy('id');

        return view('livewire.menu-list', [
            'menus' => $products,
            'categories' => $this->categories,
            'tableId' => $table->id
        ]);
    }

    public function selectCategory($category)
    {
        $this->activeCategory = $category;
    }

    public function addToCart($menuId)
    {
        $menu = Product::findOrFail($menuId);

        $item = Cart::where('table_id', $this->tableId)
            ->where('product_id', $menuId)
            ->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            Cart::create([
                'table_id' => $this->tableId,
                'product_id' => $menu->id,
                'quantity' => 1,
                'price' => $menu->price,
            ]);
        }
    }

    public function removeFromCart($menuId)
    {
        Cart::where('table_id', $this->tableId)
            ->where('id', $menuId)
            ->delete();
    }

    public function clearCart()
    {
        Cart::where('table_id', $this->tableId)->delete();
    }

    public function getCartItemsProperty()
    {
        return Cart::with('product')
            ->where('table_id', $this->tableId)
            ->get();
    }
}
