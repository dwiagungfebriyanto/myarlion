<?php

namespace App\Http\Requests;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateInventoryOutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('edit inventory out')) {
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
        $originalStock = InventoryStock::findOrFail($request->inventory_stock);
        $inventoryOut  = $this->route('inventory_out');

        $quantity = ($originalStock->unit_id === 1)
                    ? 'weight'
                    : 'amount' ;

        $maxQuantity = $originalStock->$quantity + $inventoryOut->$quantity;

        return [
            'inventory_stock'       => 'required|exists:\App\Models\InventoryStock,id',
            'status'                => 'required|string|max:255',
            'datetime'              => 'required|date',
            'quantity'              => 'required|numeric|max:' .$maxQuantity,
            'destination_warehouse' => 'required|exists:App\Models\Warehouse,id',
            'notes'                 => 'string|nullable',
        ];
    }
}
