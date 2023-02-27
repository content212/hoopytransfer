<?php

namespace App\Http\Livewire;

use App\Driver;
use App\Transaction;
use Livewire\Component;

class AccountingDriverTable extends Component
{
    public $transactions;
    public $total;
    public function mount($driver_id, $isDetail = false)
    {
        if ($isDetail) {
            $this->transactions = Transaction::whereNull('driver_id')->get();
            $this->total = $sum = Transaction::whereNull('driver_id')
                ->get()
                ->sum(function ($transaction) {
                    info($transaction->amount);
                    return $transaction->type == 'receipt' ? $transaction->amount : ($transaction->type == 'pay' ? -$transaction->amount : 0);
                });
        } else {
            $this->transactions = Driver::find($driver_id)->transactions()->orderBy('id', 'desc')->get();
            $this->total = $sum = Transaction::where('driver_id', $driver_id)
                ->get()
                ->sum(function ($transaction) {
                    info($transaction->amount);
                    return $transaction->type == 'pay' ? $transaction->amount : ($transaction->type == 'wage' ? -$transaction->amount : 0);
                });
        }
    }
    public function render()
    {
        return view('livewire.accounting-driver-table');
    }
}
