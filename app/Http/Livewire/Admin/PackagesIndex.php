<?php

namespace App\Http\Livewire\Admin;

use App\Models\Package;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class PackagesIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = "bootstrap";
    public $search;
    public function render()
    {
        $fechaHoy  = Carbon::now()->isoFormat('LL');
        $packagesQuery = Package::query()->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))
            ->orderBy('id', 'desc');

        if($this->search){
            $packagesQuery = Package::where('code', $this->search)->orderBy('id', 'desc');
        }
        $packages = $packagesQuery->paginate();

        $packagesT = Package::whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))->get();
        $total = 0;
        foreach($packagesT as $package){
            $totalPaquete = 0;
            foreach($package->elements as $element){
                $totalPaquete += $element->price;
            }
            $total += $totalPaquete;
        }

        return view('livewire.admin.packages-index', compact('packages', 'fechaHoy', 'total'));
    }
}
