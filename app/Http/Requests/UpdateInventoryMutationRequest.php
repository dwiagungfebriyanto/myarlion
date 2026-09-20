<?php

namespace App\Http\Requests;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateInventoryMutationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('edit inventory mutation')) {
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
        $inventoryMutation = $this->route('inventory_mutation');
        $originalStock = InventoryStock::findOrFail((int) $request->stock);
        $unitField = $originalStock->unit_id === 1 ? 'weight' : 'amount';
        $maxQuantity = (float) $originalStock->$unitField;

        if ((int) $inventoryMutation->original_stock_id === (int) $originalStock->id) {
            $maxQuantity += (float) $inventoryMutation->qty_mutation;
        }

        return [
            'sku'          => 'required|exists:App\Models\Product,id',
            'stock'        => 'required|exists:App\Models\InventoryStock,id',
            'quantity'     => 'required|numeric|max:' .$maxQuantity,
            'warehouse_destination' => 'nullable|exists:App\Models\Warehouse,id',
            'new_sku'      => 'required|exists:App\Models\Product,id',
            'new_quantity' => 'required|numeric',
            'price'        => 'required|numeric',
        ];
    }
}
