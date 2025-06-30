<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;

class PaymentSuccessPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.payment-success-page';

    protected static bool $shouldRegisterNavigation = false;

    public $checkout;

    public function mount(): void
    {
        $orderId = request()->query('order_id');

        if (! $orderId) {
            abort(403, 'Tidak ada data transaksi.');
        }

        $this->checkout = Order::with('items', 'payments')->findOrFail($orderId);
    }
}
