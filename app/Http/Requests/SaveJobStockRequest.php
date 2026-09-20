<?php

namespace App\Http\Requests;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;

class SaveJobStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('job statement add stock');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $selectedStock = InventoryStock::find(request()->inventory_stock_id);
        $maxQuantity = $selectedStock?->unitValue();
        $maxQuantityRule = !is_null($maxQuantity) ? "max:$maxQuantity" : "";

        return [
            'supplier'           => 'nullable|exists:App\Models\Supplier,id',
            'inventory_stock_id' => 'required|exists:App\Models\InventoryStock,id',
            'quantity'           => "required|numeric|$maxQuantityRule",
            'price'              => 'required|numeric',
            'total'              => 'required|numeric',
        ];
    }
}
