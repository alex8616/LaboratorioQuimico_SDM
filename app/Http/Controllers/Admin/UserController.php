<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidFormUser;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.users.index')->only('index');
        $this->middleware('can:admin.users.edit')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidFormUser $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['name'].$request['app'].$request['apm'].'@gmail.com',
            'password' => Hash::make($request['ci']),
        ])->assignRole('Cliente');

        $user->people()->create([
            'name' => strtoupper($request['name']),
            'app' => strtoupper($request['app']),
            'apm' => strtoupper($request['apm']),
            'ci' => $request['ci'],
            'phone' => $request['phone'],
            'address' => $request['address'],
        ]);


        return redirect()->route('admin.users.index')->with('info', 'El Usuario se creó con exito');
    }

    /**
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
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRol = $user->roles->pluck('id')->toArray();
        // dd($userRol);
        // $role->permissions->pluck('id')
        return view('admin.users.edit', compact('user', 'roles', 'userRol'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // dd($request->all());
        $user->update([
            'name' => $request['name'],
            'email' => $request['email']
        ]);
        if(isset($request->password)){
            $user->update([
                'password' => Hash::make($request['password']),
            ]);
        }
        $user->people()->update([
            'name' => $request['name'],
            'app' => $request['app'],
            'apm' => $request['apm'],
            'ci' => $request['ci'],
            'phone' => $request['phone'],
            'address' => $request['address'],
        ]);
        $user->roles()->sync($request->roles);
        return redirect()->route('admin.users.edit', $user)->with('info', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
