<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class TeamFormRequset extends FormRequest
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
                'manager' => 'required|integer|exists:users,id',
                'tester' => 'required|integer|exists:users,id',
                'developers_ids' => 'required|array|min:1',
                'developers_ids.*' => 'exists:users,id',
            ];
        } elseif ($this->isMethod('put')) {
            return [
                'new_user_id' => 'required|exists:users,id',
                'old_user_id' => 'required|integer|exists:project_user,user_id',
                'role' => 'required|string',
            ];
        }
    }
    public function attributes(): array
    {
        return [
            'manager' => 'مدير المشروع',
            'tester' => 'المختبر',
            'developers_ids.*' => 'المطور',
            'developers_ids' => 'المطورين',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'ان حق ال :attribute مطلوب',
            'integer' => 'يجب ان يكون :attribute من نوع عدد صحيح',
            'exists' => 'يجب ان يكون :attribute موجود في جدول المستخدمين',
            'min' => 'يجب ان يكون حق ال :attribute اكبر من 1',
        ];
    }
}
