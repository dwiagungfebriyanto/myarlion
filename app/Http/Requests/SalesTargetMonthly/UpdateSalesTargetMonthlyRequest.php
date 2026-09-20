<?php

namespace App\Http\Requests\SalesTargetMonthly;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesTargetMonthlyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit sales target');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'month'     => 'required|date_format:Y-m',
            'marketing' => 'nullable|exists:users,id',
            'target'    => 'required|numeric|min:0',
        ];
    }
}
