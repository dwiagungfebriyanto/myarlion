<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('add customer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:\App\Models\Customer,name',
            'address'     => 'nullable|string',
            'country'     => 'nullable|exists:\App\Models\Country,id',
            'fax'         => 'nullable|string|max:15',
            'telp'        => 'nullable|string|max:15|regex:/^[\d\s\-\(\)]+$/',
            'email'       => 'nullable|email:rfc,dns|max:255',
            'contact'     => 'nullable|string|max:255',
            'tax'         => 'nullable|in:tax,nontax',
            'no_rekening' => 'nullable|string',
            'note'        => 'nullable|string',
        ];
    }
}
