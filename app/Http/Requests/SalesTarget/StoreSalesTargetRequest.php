<?php

namespace App\Http\Requests\SalesTarget;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesTargetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marketing' => 'required|exists:users,id',
            'target'    => 'nullable|numeric|min:0',
            'year'      => 'required|date_format:Y',
        ];
    }
}
