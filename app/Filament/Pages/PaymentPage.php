<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Payment;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.payment-page';

    protected static bool $shouldRegisterNavigation = false;

    public $checkout;

    public $paymentMethod;

    public $splitCash = 0;

    public $splitQris = 0;

    public $showQrisCode = false;

    public function mount(): void
    {
        $this->checkout = Session::get('checkout_data');
    }

    public function payNow()
    {
        $this->validate([
            'paymentMethod' => 'required',
        ]);

        $checkout = $this->checkout;

        if ($this->paymentMethod === 'split') {
            $splitTotal = (int) $this->splitCash + (int) $this->splitQris;
            if ($splitTotal !== (int) $this->checkout['total']) {
                $this->addError('split', 'Total split payment harus sama dengan total tagihan.');

                return;
            }
        }

        if ($this->paymentMethod === 'qris') {
            // Show QR code modal or section
            $this->showQrisCode = true;

            return;
        }

        $order = DB::transaction(function () {

            $order = Order::create([
                'trx_no' => 'TRX-'.now()->format('YmdHis'),
                'entity_id' => auth()->user()->entity_id,
                'branch_id' => $this->checkout['branch_id'],
                'table_id' => $this->checkout['table_id'],
                'customer_name' => $this->checkout['customer_name'],
                'total_amount' => $this->checkout['total'],
            ]);

            $order->items()->createMany($this->checkout['items']);

            Payment::create([
                'trx_no' => 'PAY/'.$order->trx_no,
                'order_id' => $order->id,
                'total' => $this->checkout['total'],
                'payment_method' => $this->paymentMethod,
                'split_cash' => $this->paymentMethod === 'split' ? $this->splitCash : null,
                'split_qris' => $this->paymentMethod === 'split' ? $this->splitQris : null,
            ]);

            Session::forget('checkout_data');

            return $order;
        });

        // Redirect ke halaman sukses
        // return Redirect::to('/payment-success'); // atau route filament yang kamu punya

        $this->redirectRoute('filament.admin.pages.payment-success-page', ['order' => $order->id]);
    }
}
