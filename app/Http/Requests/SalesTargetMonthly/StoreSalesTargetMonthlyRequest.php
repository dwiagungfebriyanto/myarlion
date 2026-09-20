<?php

namespace App\Http\Requests\SalesTargetMonthly;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesTargetMonthlyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('add sales target');
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
            'marketing' => 'required|exists:users,id',
            'target'    => 'required|numeric|min:0',
        ];
    }
}
