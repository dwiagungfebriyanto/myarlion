<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

    class UpdateInventoryInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('edit inventory in')) {
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
    public function rules(Request $request): array
    {
        $inventoryIn    = $this->route('inventory_in');
        $stockReduction = $inventoryIn->inventoryOutsSum() + $inventoryIn->inventoryLostsSum();

        return [
            'status'        => 'required|string|max:255',
            'datetime'      => 'required|date',
            'warehouse'     => 'required|exists:\App\Models\Warehouse,id',
            'quantity'      => 'required|numeric|min:' .$stockReduction,
            'notes'         => 'string|nullable',
            'purchase_cost' => 'required|numeric|min:100',
        ];
    }
}
