<?php

namespace App\Http\Livewire\Admin;

use App\Models\Company;
use Livewire\WithPagination;

use Livewire\Component;

class CompaniesIndex extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function render()
    {
        $companies = Company::where('name', 'LIKE', '%'.$this->search.'%')
        ->paginate(20);
        return view('livewire.admin.companies-index', compact('companies'));
    }
}
