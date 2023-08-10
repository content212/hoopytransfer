<?php

namespace App\Http\Livewire;

use App\Models\Driver;
use App\Models\User;
use App\Models\Transaction;
use DB;
use Livewire\Component;

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

            $user = User::where('id', $driver->user_id)->first();

            $this->transactions[] = [
                'id' => $driver->id,
                'name' => $user->name ?? "-",
                'balance' => number_format($sum, 2)
            ];
        }
        $this->total = number_format(Transaction::all()->sum(function ($transaction) {
            return ($transaction->type == 'driver_payment' or $transaction->type == 'refund') ? -$transaction->amount : ($transaction->type == 'booking_payment' ? $transaction->amount : 0);
        }), 2);
        return view('livewire.accounting-table');
    }
}
