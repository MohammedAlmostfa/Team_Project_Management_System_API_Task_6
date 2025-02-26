<?php

namespace App\Http\Requests\Task;

use App\Rules\CheckUser;
use Illuminate\Foundation\Http\FormRequest;


use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class TaskFormRequestCreat extends FormRequest
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
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
               'title' => 'required|string',
               'description' => 'nullable|string',
               'status' => 'nullable|string',
               'priority' => 'required|string',
               'due_date' => 'required|date|after:today',
               'project_id' => 'required|integer|exists:projects,id',
               'user_id' => ['required', 'integer', new CheckUser($this->request->get('project_id'), $this->method())],
           ];


    }
}
