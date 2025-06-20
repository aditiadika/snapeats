<?php

namespace App\Livewire;

use Livewire\Component;

class OrderList extends Component
{
    public $qrCode;

    public function mount($qrCode)
    {
        $this->qrCode = $qrCode;
    }

    public function render()
    {
        return view('livewire.order-list');
    }
}
