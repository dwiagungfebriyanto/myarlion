<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\InventoryStock;

class StoreReturnStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('add inventory stock');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $inventoryStock = InventoryStock::findOrFail($this->inventory_stock_id);
        $unitId = $inventoryStock->product->unit_id;

        $qtyRules = $unitId == 1
            ? 'required|numeric|min:0.001'
            : 'required|integer|min:1';

        return [
            'warehouse_id'       => 'required|exists:warehouses,id',
            'inventory_stock_id' => 'required|exists:inventory_stocks,id',
            'qty'                => $qtyRules,
            'total'              => 'required|numeric|min:0',
            'unit_id'            => 'required|exists:units,id'
        ];
    }

}
