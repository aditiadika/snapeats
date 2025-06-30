<?php

namespace App\Filament\Pages;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Filament\Notifications\Notification;
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
            if ($splitTotal !== (int) $checkout['total']) {
                $this->addError('split', 'Total split payment harus sama dengan total tagihan.');

                return;
            }
        }

        if ($this->paymentMethod === 'qris') {
            // Show QR code modal or section
            $this->showQrisCode = true;

            return;
        }

        $order = DB::transaction(function () use ($checkout) {

            $order = Order::create([
                'trx_no' => 'TRX-' . now()->format('YmdHis'),
                'entity_id' => auth()->user()->entity_id,
                'branch_id' => $checkout['branch_id'],
                'table_id' => $checkout['table_id'],
                'customer_name' => $checkout['customer_name'],
                'total_amount' => $checkout['total'],
                'type' => $checkout['type'],
                'created_by' => auth()->user()->name,
            ]);

            $order->items()->createMany($this->checkout['items']);

            Payment::create([
                'trx_no' => 'PAY/' . $order->trx_no,
                'order_id' => $order->id,
                'total' => $this->checkout['total'],
                'payment_method' => $this->paymentMethod,
                'split_cash' => $this->paymentMethod === 'split' ? $this->splitCash : null,
                'split_qris' => $this->paymentMethod === 'split' ? $this->splitQris : null,
            ]);

            Session::forget('checkout_data');

            Cart::with('product')
                ->where('user_id', auth()->id())
                ->delete();

            Notification::make()
                ->title('Transaction Success')
                ->success()
                ->send();

            return $order;
        });

        $this->redirectRoute('filament.admin.pages.payment-success-page', ['order_id' => $order->id]);
    }
}
