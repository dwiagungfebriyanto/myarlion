<?php

namespace App\Http\Requests;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreInventoryOutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('add inventory out')) {
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
        $inventoryStock = InventoryStock::findOrFail($request->inventory_stock);

        $quantity = ($inventoryStock->unit_id === 1)
                    ? 'weight'
                    : 'amount' ;

        return [
            'inventory_stock'       => 'required|exists:\App\Models\InventoryStock,id',
            'status'                => 'required|string|max:255',
            'datetime'              => 'required|date',
            'quantity'              => 'required|numeric|max:' .$inventoryStock->$quantity,
            'destination_warehouse' => 'required|exists:App\Models\Warehouse,id',
            'notes'                 => 'string|nullable',
        ];
    }
}
