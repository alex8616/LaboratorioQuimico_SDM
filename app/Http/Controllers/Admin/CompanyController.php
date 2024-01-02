<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidCompanyForm;
use App\Models\Company;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::paginate();
        // dd($companies);
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $peoples = DB::table('people')
            ->select(DB::raw('concat (name," ",app," ",apm) as fullname, user_id'))->get();
        $users = array('' => 'Seleccione Delegado') + $peoples->pluck('fullname', 'user_id')->toArray();
        return view('admin.companies.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidCompanyForm $request)
    {
        // dd($request->all());
        $company = Company::create($request->all());
        return redirect()->route('admin.companies.edit', $company)->with('info', 'La Empresa se creó con exito');
    }

    /*
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        // $people = DB::table('people')
        // ->where('user_id', '=', $company->user_id)
        // ->select(DB::raw('concat (name," ",app," ",apm) as fullname, user_id'))->get();
        $people = User::find($company->user_id);
        $peoples = DB::table('people')
        ->select(DB::raw('concat (name," ",app) as fullname, user_id'))->get();
        // dd($people);
        $users = array('' => 'Seleccione Delegado') + $peoples->pluck('fullname', 'user_id')->toArray();
            // dd($users);
        return view('admin.companies.edit', compact('company', 'users', 'people'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ValidCompanyForm $request, Company $company)
    {
        $company->update($request->all());
        return redirect()->route('admin.companies.edit', $company)->with('info', 'La Empresa se actualizó con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.companies.index')->with('info', 'La Empresa se elimino con exito');
    }
}
