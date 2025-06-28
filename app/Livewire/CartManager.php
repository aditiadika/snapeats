<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Livewire\Component;

class CartManager extends Component
{
    public $tableId;

    public $carts = [];

    public $showCart = false;

    public $notes = '';

    protected $listeners = [
        'addToCart' => 'handleAddToCart',
        'showCart' => 'showCart',
        'cartUpdated' => 'refreshCart',
    ];

    public function mount($tableId)
    {
        $this->tableId = $tableId;
        $this->refreshCart();
    }

    public function showCart()
    {
        $this->showCart = true;
        $this->refreshCart();
    }

    public function handleAddToCart($productId, $tableId)
    {
        $this->addToCart($productId, $tableId);
    }

    public function refreshCart()
    {
        $this->carts = Cart::with('product')
            ->where('table_id', $this->tableId)
            ->get();
    }

    public function addToCart($productId, $tableId)
    {
        $product = Product::findOrFail($productId);
        dd($product);

        $cart = Cart::firstOrNew([
            'table_id' => $this->tableId,
            'product_id' => $productId,
        ]);

        if ($cart->exists) {
            $cart->quantity += 1;
        } else {
            $cart->price = $product->price;
            $cart->quantity = 1;
        }

        $cart->save();
        $this->refreshCart();
        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($cartId)
    {
        Cart::find($cartId)->delete();
        $this->refreshCart();
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($cartId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($cartId);

            return;
        }

        Cart::find($cartId)->update(['quantity' => $quantity]);
        $this->refreshCart();
        $this->dispatch('cartUpdated');
    }

    public function confirmOrder()
    {
        // Validasi cart tidak kosong
        if ($this->carts->isEmpty()) {
            $this->dispatch('showToast', ['message' => 'Cart is empty', 'type' => 'error']);

            return;
        }

        // Buat order baru
        $order = Order::create([
            // 'user_id' => auth()->id(),
            'table_id' => $this->tableId,
            'total' => $this->carts->sum(function ($item) {
                return $item->price * $item->quantity;
            }),
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        // Tambahkan order items
        foreach ($this->carts as $cart) {
            $order->items()->create([
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->price,
            ]);
        }

        // Kosongkan cart
        Cart::query()
            // ->where('user_id', auth()->id())
            ->where('table_id', $this->tableId)
            ->delete();

        $this->refreshCart();
        $this->dispatch('showToast', ['message' => 'Order confirmed!', 'type' => 'success']);
        $this->showCart = false;
    }

    public function render()
    {
        return view('livewire.cart-manager');
    }
}
