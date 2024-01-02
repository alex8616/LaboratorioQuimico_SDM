<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidFormUser extends FormRequest
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
            // 'email'     => 'required|email|unique:users',
            // 'password'  => 'required|min:3|max:16',
            'name'      => 'required|min:2|max:25',
            'app'       => 'required|min:2|max:30',
            'ci'        => 'required|min:6|max:12|unique:people',
            'phone'     => 'required|min:7|max:12|unique:people',
            'address'   => 'required|min:2|max:35',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'El :attribute es obligatorio.',
            'email.email' => 'El :attribute debe ser un correo válido.',
            'email.unique' => 'El :attribute ya se encuentra registrado.',

            'password.required' => 'El :attribute es obligatorio.',
            'password.min' => 'El :attribute debe contener minimo de 2 caracteres.',
            'password.max' => 'El :attribute debe contener maximo 16 caracteres.',

            'name.required' => 'El :attribute es obligatorio.',
            'name.min' => 'El :attribute debe contener minimo de 3 caracteres.',
            'name.max' => 'El :attribute debe contener maximo 20 caracteres.',

            'app.required' => 'El :attribute es obligatorio.',
            'app.min' => 'El :attribute debe contener minimo de 2 caracteres.',
            'app.max' => 'El :attribute debe contener maximo 30 caracteres.',

            'ci.required' => 'El :attribute es obligatorio.',
            'ci.min' => 'El :attribute debe contener minimo de 7 caracteres.',
            'ci.max' => 'El :attribute debe contener maximo 12 caracteres.',
            'ci.unique' => 'El :attribute ya se encuentra registrado.',

            'phone.required' => 'El :attribute es obligatorio.',
            'phone.min' => 'El :attribute debe contener minimo de 7 caracteres.',
            'phone.max' => 'El :attribute debe contener maximo 12 caracteres.',
            'phone.unique' => 'El :attribute ya se encuentra registrado.',

            'address.required' => 'La :attribute es obligatorio.',
            'address.min' => 'La :attribute debe contener minimo de 2 caracteres.',
            'address.max' => 'La :attribute debe contener maximo 30 caracteres.',

        ];
    }
    public function attributes()
    {
        return [
            'email'     => 'Correo Electronico',
            'password'  => 'Contraseña',
            'name'      => 'Nombre del Usuario',
            'app'  => 'Apellido Paterno del Usuario',
            'ci'     => 'Carnet de Identidad',
            'phone'     => 'Telefono',
            'address'   => 'Dirección',
        ];
    }
}
