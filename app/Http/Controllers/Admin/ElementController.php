<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidFormElement;
use App\Models\Element;
use Illuminate\Http\Request;

class ElementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $elements = Element::all();
        return view('admin.elements.index', compact('elements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.elements.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidFormElement $request)
    {
        $element = Element::create($request->all());
        return redirect()->route('admin.elements.edit', $element)->with('info', 'Elemento creado con exito');
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
    public function edit(Element $element)
    {
        return view('admin.elements.edit', compact('element'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  Element $element
     * @return \Illuminate\Http\Response
     */
    public function update(ValidFormElement $request, Element $element)
    {
        $actualizarelement = Element::findOrFail($element->id);

        $actualizarelement->name = $request->name;
        $actualizarelement->symbol = $request->symbol;
        $actualizarelement->price = $request->price;

        $actualizarelement->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  Element $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(Element $element)
    {
        $element->delete();
        return redirect()->route('admin.elements.index')->with('info', 'Elemento eliminado correctamente');
    }
}
