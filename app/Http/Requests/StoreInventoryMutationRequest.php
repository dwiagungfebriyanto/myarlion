<?php

namespace App\Http\Requests;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreInventoryMutationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('add inventory mutation')) {
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
        $originalStock = InventoryStock::findOrFail((int)$request->stock);

        $unit = ($originalStock->unit_id === 1) ? 'weight' : 'amount' ;

        return [
            'sku'                   => 'required|exists:App\Models\Product,id',
            'stock'                 => 'required|exists:App\Models\InventoryStock,id',
            'quantity'              => 'required|numeric|max:' .$originalStock->$unit,
            'warehouse_destination' => 'required|exists:App\Models\Warehouse,id',
            'new_sku'               => 'required|exists:App\Models\Product,id',
            'new_quantity'          => 'required|numeric',
            'price'                 => 'required|numeric',
        ];
    }
}
