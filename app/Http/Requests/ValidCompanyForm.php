<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidCompanyForm extends FormRequest
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
            'name'      => 'required|min:3|max:50',
            'phone'     => 'min:7|max:12',
            'address'   => 'min:5|max:40',
            // 'delegate'   => 'required',
        ];
    }
    public function messages()
    {
        return [

            'name.required' => 'El :attribute es obligatorio.',
            'name.min' => 'El :attribute debe contener minimo de 3 caracteres.',
            'name.max' => 'El :attribute debe contener maximo 30 caracteres.',

            'phone.required' => 'El :attribute es obligatorio.',
            'phone.min' => 'El :attribute debe contener minimo de 7 caracteres.',
            'phone.max' => 'El :attribute debe contener maximo 40 caracteres.',

            'address.required' => 'La :attribute es obligatorio.',
            'address.min' => 'La :attribute debe contener minimo de 5 caracteres.',
            'address.max' => 'La :attribute debe contener maximo 40 caracteres.',

            'delegate.required' => 'La :attribute es obligatorio.',
        ];
    }
    public function attributes()
    {
        return [
            'name'          => 'Nombre de la empresa',
            'phone'         => 'Teléfono de la empresa',
            'address'       => 'Dirección de la empresa',
            'delegate'       => 'Delegado de la empresa',
        ];
    }
}
