<?php

namespace App\Http\Livewire;

use App\Driver;
use App\Transaction;
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
                    info($transaction->amount);
                    return $transaction->type == 'pay' ? $transaction->amount : ($transaction->type == 'wage' ? -$transaction->amount : 0);
                });
            array_push($this->transactions, [
                'id' => $driver->id,
                'name' => $driver->user->name,
                'balance' => number_format($sum, 2)
            ]);
        }
        $this->total = number_format(Transaction::all()->sum(function ($transaction) {
            info($transaction->amount);
            return $transaction->type == 'pay' ? -$transaction->amount : ($transaction->type == 'receipt' ? $transaction->amount : 0);
        }), 2);
        return view('livewire.accounting-table');
    }
}
