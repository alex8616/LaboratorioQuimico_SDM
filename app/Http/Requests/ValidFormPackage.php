<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidFormPackage extends FormRequest
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

            'code' => 'required|min:1',
            'features' => 'required|min:5|max:50',
            'user_id' => 'required',
            'company_id' => 'required',
            // 'elements' => 'required',
        ];
    }

    public function messages()
    {
        return [


            'code.required' => 'El :attribute es obligatorio.',
            'code.min' => 'El :attribute debe contener minimo de 1 caracter.',

            'features.required' => 'El :attribute es obligatorio.',
            'features.min' => 'El :attribute debe contener minimo de 5 caracteres.',
            'features.max' => 'El :attribute debe contener maximo 50 caracteres.',

            'elements'  =>  'El :attribute es obligatorio.'
        ];
    }

    public function attributes()
    {
        return [
            'key'          => 'Key Uuid',
            'code'          => 'Código de paquete',
            'features'          => 'Caracteristica',
            'elements'          => 'Elemento',
        ];
    }
}
