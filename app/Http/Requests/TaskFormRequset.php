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
    {

        return [
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'title'=>'nullable|string',
        ];


    }



}
