<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidFormElement extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|min:2|max:30',
            'symbol'  => 'required|min:2|max:5',
            'price'      => 'required|numeric|min:10|max:120',
        ];
    }
    public function messages()
    {
        return [

            'name.required' => 'El :attribute es obligatorio.',
            'name.min' => 'El :attribute debe contener minimo de 2 caracteres.',
            'name.max' => 'El :attribute debe contener maximo 30 caracteres.',

            'symbol.required' => 'El :attribute es obligatorio.',
            'symbol.min' => 'El :attribute debe contener minimo de 2 caracteres.',
            'symbol.max' => 'El :attribute debe contener maximo 5 caracteres.',

            'price.required' => 'El :attribute es obligatorio.',
            'price.numeric' => 'El :attribute debe ser númerico.',
            'price.min' => 'El :attribute no debe ser menor a los 10 Bs.',
            'price.max' => 'El :attribute no debe pasar los 80 Bs.',

        ];
    }
    public function attributes()
    {
        return [
            'name'          => 'Nombre del elemento',
            'symbol'         => 'Simbolo del elemento',
            'price'       => 'Precio del elemento',
        ];
    }
}
