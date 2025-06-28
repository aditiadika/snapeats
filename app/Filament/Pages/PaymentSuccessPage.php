<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PaymentSuccessPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.payment-success-page';

    protected static bool $shouldRegisterNavigation = false;

    public $checkout;

    public function mount($orderId): void
    {
        dd($orderId);
        $this->checkout = $order;
        dd($this->checkout);

        if (! $this->checkout) {
            abort(403, 'Tidak ada data transaksi.');
        }
    }
}
