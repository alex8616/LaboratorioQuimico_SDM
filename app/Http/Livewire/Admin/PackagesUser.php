<?php

namespace App\Http\Livewire\Admin;

use App\Models\Person;
use Livewire\Component;

class PackagesUser extends Component
{
    public $client;
    public $people=[];

    public function render()
    {
        if(!empty($this->client)){
            $this->people = Person::where('app', 'LIKE', '%'.$this->client.'%')
            // ->orWhere('name', 'LIKE', '%'.$this->client.'%')
            ->orWhere('ci', $this->client)
            ->get();
            // dd($this->people);
        }
        return view('livewire.admin.packages-user')->with('people', $this->people);
    }
}


    // public $country;
    // public $cities=[];
    // public $city;

    // public function render()
    // {
    //     if(!empty($this->country)) {
    //         $this->cities = City::where('country_id', $this->country)->get();
    //     }
    //     return view('livewire.dropdowns')
    //         ->withCountries(Country::orderBy('name')->get());
    // }
