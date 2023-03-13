<?php

namespace App\Http\Livewire;

use App\Models\Driver;
use App\Models\Transaction;
use Livewire\Component;
use \DB;

class AccountingTable extends Component
{
    public $transactions = [];
    public $total;
    public function render()
    {
        $drivers = Driver::all();
        foreach ($drivers as $driver) {
            $sum = Transaction::where('driver_id', $driver->id)
                ->get()
                ->sum(function ($transaction) {
                    return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                });
            array_push($this->transactions, [
                'id' => $driver->id,
                'name' => $driver->user->name,
                'balance' => number_format($sum, 2)
            ]);
        }
        $this->total = number_format(Transaction::all()->sum(function ($transaction) {
            return ($transaction->type == 'driver_payment' or $transaction->type == 'refund') ? -$transaction->amount : ($transaction->type == 'booking_payment' ? $transaction->amount : 0);
        }), 2);
        return view('livewire.accounting-table');
    }
}