<?php

namespace App\Http\Livewire\Admin;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

use Livewire\Component;

class UsersIndex extends Component
{
    use WithPagination;
    public $search;
    protected $paginationTheme = 'bootstrap';
    public function updatingSearch(){
        $this->resetPage();
    }
    public function render()
    {
        $usersQuery = User::query()->whereHas('people', function(Builder $query){
            $query->where('name', 'LIKE', '%'.$this->search.'%')
                ->orWhere('app', 'LIKE', '%'.$this->search.'%');
        });
        $users = $usersQuery->paginate(15);
        return view('livewire.admin.users-index', compact('users'));
    }
}
