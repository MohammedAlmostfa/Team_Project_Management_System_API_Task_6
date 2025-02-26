<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserFortmRequestCreat extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }

    public function authorize()
    {
        return auth()->check();
    }
    public function rules()
    {
        return [
            'email' =>  'required|string|email|max:255',
            'password' =>  'required|string|min:6',
            'name'  =>  'required|string|max:255',
            'role' =>  'nullable|string',
        ];
    }
}
