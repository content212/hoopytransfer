<?php

namespace App\Http\Livewire;

use App\Models\Driver;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AccountingDriverTable extends Component
{
    public $transactions;
    public $total;
    public $amount, $note;
    public $driver_id;
    public $role;
    public function mount($driver_id, $role, $isDetail = false)
    {
        $this->role = strtolower($role);
        if ($isDetail) {
            $this->transactions = Transaction::where(function ($query) {
                $query->where('type', 'driver_payment')
                    ->orWhere('type', 'booking_payment')
                    ->orWhere('type', 'refund');
            })
                ->orderBy('updated_at', 'desc')
                ->get(['*',  DB::raw('(case when type = "booking_payment" then amount when type = "driver_payment" then -amount when type = "refund" then -amount end ) as amount')]);
            $this->total = Transaction::all()
                ->sum(function ($transaction) {
                    return $transaction->type == 'booking_payment' ? $transaction->amount : (($transaction->type == 'driver_payment' or $transaction->type == 'refund') ? -$transaction->amount : 0);
                });
        } else {
            $this->driver_id = $driver_id;
            $this->transactions = Driver::find($driver_id)->transactions()->orderBy('updated_at', 'desc')->get();
            $this->total = Transaction::where('driver_id', $driver_id)
                ->get()
                ->sum(function ($transaction) {
                    return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                });
        }
    }
    public function add()
    {
        $this->validate([
            'amount' => 'required|numeric',
            'note' => 'required'
        ]);
        $total = Transaction::where('driver_id', $this->driver_id)
            ->get()
            ->sum(function ($transaction) {
                return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
            });
        $data = [
            'driver_id' => $this->driver_id,
            'type' => 'driver_payment',
            'amount' => $this->amount,
            'balance' => $total,
            'note' => $this->note
        ];
        Transaction::create($data);
        $this->transactions = Driver::find($this->driver_id)->transactions()->orderBy('id', 'desc')->get();

        $this->total = Transaction::where('driver_id', $this->driver_id)
            ->get()
            ->sum(function ($transaction) {
                return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
            });

        $this->amount = '';
        $this->note = '';
    }
    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction)
            $transaction->delete();
        $this->transactions = Driver::find($this->driver_id)->transactions()->orderBy('id', 'desc')->get();
        $this->total = Transaction::where('driver_id', $this->driver_id)
            ->get()
            ->sum(function ($transaction) {
                return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
            });
    }
    public function render()
    {
        return view('livewire.accounting-driver-table');
    }
}
