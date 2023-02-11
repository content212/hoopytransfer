<?php

namespace App\Http\Livewire;

use App\State;
use App\Country;
use Livewire\Component;

class Dropdowns extends Component
{
    public $country;
    public $states = [];
    public $state;

    public function render()
    {
        if (!empty($this->country)) {
            $this->states = State::where('country_id', '=', $this->country)->get();
        }
        return view('livewire.dropdowns')
            ->withCountries(Country::orderBy('name')->get())->section('content');
    }

    public function changeEvent($value)
    {
        $this->country = $value;
    }
}
