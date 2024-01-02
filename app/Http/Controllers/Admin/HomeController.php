<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Element;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        
        $packagesTotal = Package::all()->count();
        $usersTotal = User::all()->count();
        $companiesTotal = Company::all()->count();
        $elementTotal = Element::all()->count();
        return view('admin.index', compact('packagesTotal', 'usersTotal', 'companiesTotal', 'elementTotal'));
    }


}
