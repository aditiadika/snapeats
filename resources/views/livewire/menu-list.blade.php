<div class="container py-3">
    
    <!-- Search and Cart Button Row -->
<div class="d-flex align-items-center mb-3">
    <!-- Search Bar (flex-grow) -->
    <div class="flex-grow-1 me-2">
        <input type="text" 
               class="form-control" 
               placeholder="Search menu..." 
               wire:model.live="search">
    </div>

    <!-- Cart Button (right-aligned) -->
    <div class="d-flex align-items-center justify-content-end">
        <button class="btn btn-primary position-relative rounded-circle d-flex justify-content-center align-items-center"
                style="width: 48px; height: 48px;"
                wire:click="$dispatch('showCart')">
            <i class="bi bi-cart-fill"></i>
            @if (!empty($carts) && count($carts) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-circle"
                      style="font-size: 0.75rem; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                {{ $cartItems->count() }}
                </span>
            @endif
        </button>
    </div>
</div>


    <!-- Category Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link" wire:click="selectCategory('all')">
                All
            </button>
        </li>
        @foreach ($categories as $id => $category)
            <li class="nav-item">
                <button class="nav-link {{ $activeCategory == $id ? 'active' : '' }}"
                    wire:click="selectCategory('{{ $id }}')">
                    {{ $category->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <!-- Menu Items -->
    <div class="row">
        @foreach ($menus as $menu)
            <div class="col-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $menu->name }}</h5>
                        <p class="card-text text-muted">
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </p>
                        <button class="btn btn-primary btn-sm w-100" wire:click="addToCart({{ $menu->id }})">
                            Add to Order
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
        

        @if ($menus->isEmpty())
            <div class="col-12 text-center py-4">
                <p class="text-muted">No menu items found</p>
            </div>
        @endif
    </div>
</div>
