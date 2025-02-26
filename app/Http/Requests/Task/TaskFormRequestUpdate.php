<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class TaskFormRequestUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                 'title' => 'nullable|string',
                 'description' => 'nullable|string',
                 'status' => 'nullable|string',
                 'priority' => 'nullable|string',
                 'project_id' => 'required|integer|exists:projects,id',
                 'due_date' => 'nullable|date|after:today',

             ];

    }
}
