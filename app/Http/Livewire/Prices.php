<?php

namespace App\Http\Livewire;

use App\Models\CarType;
use App\Models\Price;
use Livewire\Component;

class Prices extends Component
{
    protected $listeners = ['some-event' => '$refresh'];
    public CarType $car_type;
    public $car_type_id;

    public $news;

    protected $rules = [
        'car_type.prices.*.start_km' => 'required',
        'car_type.prices.*.finish_km' => 'required',
        'car_type.prices.*.opening_fee' => 'required',
        'car_type.prices.*.km_fee' => 'required'
    ];

    public function mount($id)
    {
        $this->news = collect();
        if ($id == -1) {

            $this->news->add(new Price);
        } else {
            $this->car_type_id = $id;
            $this->car_type = CarType::find($this->car_type_id);
        }
    }

    public function render()
    {
        return view('livewire.prices');
    }

    public function save()
    {
        $this->car_type->prices->each->save();

        foreach ($this->news as $i => $price) {
            if (
                isset($price['km_fee']) &&
                isset($price['opening_fee']) &&
                isset($price['start_km']) &&
                isset($price['finish_km'])
            ) {
                $this->car_type->prices()->create($price);
            }
        }
        $this->news = collect();
        $this->car_type = CarType::find($this->car_type_id);
        $this->emit('some-event');
    }
}
