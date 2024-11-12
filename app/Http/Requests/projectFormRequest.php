<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class projectFormRequest extends FormRequest
{

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }

    public function authorize(): bool
    {
        return true;
    }





    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|min:4|max:25',
                'description' => 'required|string|min:10|max:90',
            ];
        } elseif ($this->isMethod('put')) {
            return [
                'name' => 'nullable|string|min:4|max:25',
                'description' => 'nullable|string|min:10|max:90',
            ];
        } else {
            return [
                'status' => ' nullable |boolean',
            ];
        }
    }

    public function attributes()
    {
        return [
            'name' => ' الاسم المهمة ',
            'description' => 'الوصف المهمة',
        ];
    }

    public function messages()
    {
        return [
            'string' => 'ان حقل  :attribute من نوع محرفي',
            'required' => 'ان حقل :attribute مطلوب',
            'min' => 'ان حقل :attribute يجب ان يكون اكبر من :min',
            'max' => 'ان حقل  :attribute يجب ان يكون اقل من :max',
        ];
    }
}
