<?php

namespace App\Http\Requests;

use App\Rules\CheckUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class TaskFormRequset extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }

    public function prepareForValidation()
    {
        if ($this->isMethod('post')) {
            $this->merge([
                'due_date' => Carbon::parse($this->input('due_date'))->format('Y-m-d'),
            ]);
        }
    }

    public function rules(): array
    { //for add task
        if ($this->isMethod('post')) {
            return [
                'title' => 'required|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
                'priority' => 'required|string',
                'due_date' => 'required|date|after:today',
                'project_id' => 'required|integer|exists:projects,id',
                'user_id' => ['required', 'integer', new CheckUser($this->request->get('project_id'), $this->method())],
            ];
            // for updatae task
        } elseif ($this->isMethod('put')) {
            return [
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
                'priority' => 'nullable|string',
                'project_id' => 'nullable|integer|exists:projects,id',
                'due_date' => 'nullable|date|after:today',
                'user_id' => ['nullable', 'integer', new CheckUser($this->route('id'), $this->method())],
            ];
        }
        // for show his task
        elseif ($this->isMethod('get')) {
            return [
                'status' => 'nullable|string',
                'priority' => 'nullable|string',
                'title'=>'nullable|string',
            ];
        }
        return [];
    }


    public function attributes(): array
    {
        return [
            'title' => 'اسم التاسك',
            'description' => 'الوصف',
            'priority' => 'مستوى',
            'user_id' => 'المستخدم',
            'project_id' => 'المشروع',
            'status' => ' الحالة',
            'due_date' => 'تاريخ التسليم',
        ];
    }

    public function messages(): array
    {
        return [
            'string' => 'إن حقل :attribute يجب أن يكون من نوع محرفي',
            'required' => 'إن حقل :attribute مطلوب',
            'date' => 'حقل :attribute يجب أن يكون تاريخًا صالحًا',
            'after' => 'يجب أن يكون :attribute بعد اليوم.',
        ];
    }
}
