<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardAggregateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Token akan divalidasi di controller
    }

    public function rules(): array
    {
        return [
            'year' => ['nullable','integer','between:2020,' . date('Y')],
            'month' => ['nullable','integer','between:1,12'],
            'marketing' => ['nullable'],
            'website' => ['nullable','integer'],
            'modules' => ['nullable','string'],
            'force_refresh' => ['nullable','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.between' => 'Month harus antara 1-12',
            'year.between' => 'Year di luar rentang yang diizinkan.',
        ];
    }
}
