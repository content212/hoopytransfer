<?php

namespace App\Http\Livewire;

use App\Models\CarType;
use App\Models\Price;
use Livewire\Request;
use Livewire\Component;
use Livewire\WithFileUploads;

class Servicesmodal extends Component
{
    use WithFileUploads;

    protected $listeners = ['setId', 'some-event' => '$refresh'];

    public CarType $car_type;
    public $car_type_id, $news, $newImage;

    protected $rules = [
        'car_type.name' => 'required',
        'car_type.baggage_capacity' => 'required',
        'car_type.person_capacity' => 'required',
        'car_type.discount_rate' => 'required',
        'car_type.free_cancellation' => 'required',
        'car_type.prices.*.start_km' => 'required',
        'car_type.prices.*.finish_km' => 'required|gt:car_type.prices.*.start_km',
        'car_type.prices.*.opening_fee' => 'required',
        'car_type.prices.*.km_fee' => 'required',
        'newImage' => 'image|nullable'
    ];
    public function updated($name, $value)
    {
        $nameArray = explode('.', $name);
        if (count($nameArray) > 1) {
            if ($nameArray[0] == 'news' and $nameArray[2] == 'finish_km' and $nameArray[1] != count($this->news) - 1) {
                //dd($this->news[$nameArray[1] + 1]['start_km']);
                $new = $this->news[$nameArray[1] + 1];
                $new['start_km'] = $value + 1;
                $this->news[$nameArray[1] + 1] = $new;
            }
            if ($nameArray[1] == 'prices' and $nameArray[3] == 'finish_km') {
                if ($nameArray[2] == count($this->car_type->prices) - 1) {
                    if (count($this->news) > 0) {
                        $new = $this->news[0];
                        $new['start_km'] = $value + 1;
                    }
                } else {
                    $price = $this->car_type->prices[$nameArray[2] + 1];
                    $price['start_km'] = $value + 1;
                }
            }
        }
    }
    public function updatedCar_type($value)
    {
        dd($value);
    }

    public function setId($car_type_id)
    {
        $this->news = collect();
        if ($car_type_id == -1) {
            $this->car_type = new CarType;
            $this->car_type_id = $car_type_id;
            $price = new Price(['start_km' => 0]);
            $this->news->add($price);
        } else {
            $this->car_type_id = $car_type_id;
            $this->car_type = CarType::find($this->car_type_id);
        }
    }

    public function mount($car_type_id)
    {
        $this->news = collect();
        if ($car_type_id == -1) {
            $this->car_type = new CarType;
            $price = new Price(['start_km' => 0]);
            $this->news->add($price);
        } else {
            $this->car_type_id = $car_type_id;
            $this->car_type = CarType::find($this->car_type_id);
        }
    }
    public function render()
    {
        return view('livewire.servicesmodal');
    }

    public function delete($id)
    {
        $this->car_type->prices[$id]->delete();
        $this->car_type = CarType::find($this->car_type_id);
        $this->emit('some-event');
    }
    public function add()
    {
        if ($this->car_type_id == -1) {
            $this->validate([
                'news.*.start_km' => 'required',
                'news.*.finish_km' => 'required|gt:news.*.start_km',
                'news.*.opening_fee' => 'required',
                'news.*.km_fee' => 'required'
            ]);
            $price = new Price(['start_km' => $this->news->last()['finish_km'] + 1]);
            $this->news->add($price);
        } else {
            $a = count($this->car_type->prices);
            $this->validate([
                'news.*.start_km' => 'required',
                'news.*.finish_km' => 'required|gt:news.*.start_km',
                'news.*.opening_fee' => 'required',
                'news.*.km_fee' => 'required',
                'car_type.prices.*.start_km' => 'required',
                'car_type.prices.*.finish_km' => 'required',
                'car_type.prices.*.opening_fee' => 'required',
                'car_type.prices.*.km_fee' => 'required'
            ]);
            $price = new Price(['start_km' => count($this->news) > 0 ? $this->news->last()['finish_km'] + 1  : $this->car_type->prices->last()['finish_km'] + 1]);
            $this->news->add($price);
        }
        $this->emit('some-event');
    }
    public function newsdelete($id)
    {
        unset($this->news[$id]);
        $this->emit('some-event');
    }

    public function save()
    {
        if ($this->car_type_id == -1) {
            $this->validate([
                'car_type.name' => 'required',
                'car_type.person_capacity' => 'required',
                'car_type.baggage_capacity' => 'required',
                'car_type.discount_rate' => 'required',
                'car_type.free_cancellation' => 'required',
                'news.*.start_km' => 'required',
                'news.*.finish_km' => 'required|gt:news.*.start_km',
                'news.*.opening_fee' => 'required',
                'news.*.km_fee' => 'required'
            ]);
            $car_type = CarType::create($this->car_type->toArray());
            if ($car_type) {
                $this->car_type = $car_type;
                foreach ($this->news as $i => $price) {
                    if (
                        isset($price['km_fee']) &&
                        isset($price['opening_fee']) &&
                        isset($price['start_km']) &&
                        isset($price['finish_km'])
                    ) {
                        $car_type->prices()->create($price);
                    }
                }
            }
        } else {
            $this->validate();
            if (count($this->news) > 0) {
                $this->validate([
                    'car_type.name' => 'required',
                    'car_type.person_capacity' => 'required',
                    'car_type.baggage_capacity' => 'required',
                    'car_type.discount_rate' => 'required',
                    'car_type.free_cancellation' => 'required',
                    'news.*.start_km' => 'required',
                    'news.*.finish_km' => 'required|gt:news.*.start_km',
                    'news.*.opening_fee' => 'required',
                    'news.*.km_fee' => 'required'
                ]);
            }
            $this->car_type->prices->each->save();
            unset($this->car_type->prices);
            $this->car_type->save();

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
        }
        if ($this->newImage) {
            $image = $this->newImage->store('/', 'images');
            $this->car_type->image = $image;
            $this->car_type->save();
            unset($this->newImage);
        }
        $this->news = collect();
        $this->car_type = CarType::find($this->car_type->id);
        $this->emit('some-event');
        $this->emit('saved');
    }
}
