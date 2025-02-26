<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserFortmRequestUpdate extends FormRequest
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
                    'email' =>  'nullable|string|email|max:255',
                    'password' =>  'nullable|string|min:6',
                    'name'  =>  'nullable|string|max:255',
                    'role' =>  'nullable|string',
                ];





    }
}
