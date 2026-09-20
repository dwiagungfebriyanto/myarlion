<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveJobCommissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'release_date' => 'required|date',
            'marketing'    => 'required|exists:App\Models\User,id',
            'percentage'   => 'required|numeric|min:1|max:100',
            'nominal'      => 'required|min:1',
            'note'         => 'nullable|string',
        ];
    }
}
