<?php

namespace App\Http\Requests;

use App\Rules\ValidQtyIn;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('add inventory in')) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'po_stock'      => 'required|exists:\App\Models\PoStock,id',
            'status'        => 'required|string|max:255',
            'datetime'      => 'required|date',
            'warehouse'     => 'required|exists:\App\Models\Warehouse,id',
            'quantity.*'    => ['required', 'numeric', 'min:0', new ValidQtyIn],
            'unit.*'        => 'required|exists:\App\Models\Unit,id',
            'notes'         => 'string|nullable',
        ];
    }
}
