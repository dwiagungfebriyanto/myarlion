<?php

namespace App\Http\Requests;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('job statement edit stock');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $inventoryStock = InventoryStock::find(request()->inventory_stock_id);
        $jobStatement = $this->route('job_statement');
        $availableQuantity = $inventoryStock?->unitValue() ?? 0;

        if ($inventoryStock && $jobStatement && (int) $jobStatement->inventory_stock_id === (int) $inventoryStock->id) {
            $availableQuantity += (float) $jobStatement->quantity;
        }

        $quantityMax = $inventoryStock ? "max:$availableQuantity" : "";

        return [
            'job_id'             => 'required|exists:App\Models\Job,id',
            'supplier'           => 'nullable|exists:App\Models\Supplier,id',
            'inventory_stock_id' => 'required|exists:App\Models\InventoryStock,id',
            'quantity'           => "required|numeric|$quantityMax",
            'price'              => 'required|numeric',
            'total'              => 'required|numeric',
        ];
    }
}
