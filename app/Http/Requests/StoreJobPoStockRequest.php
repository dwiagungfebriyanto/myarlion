<?php

namespace App\Http\Requests;

use App\Models\PoStockProduct;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobPoStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $poDetail = PoStockProduct::where([
            'po_stock_id' => request()->po_stock,
            'product_id' => request()->product,
        ])->firstOrFail();

        return [
            'po_stock' => 'required|exists:App\Models\PoStock,id',
            'product'  => 'required|exists:App\Models\Product,id',
            'quantity' => "required|numeric|max:$poDetail->remaining_qty",
            'amount'   => 'required|numeric',
            'note'     => 'nullable|string',
        ];
    }
}
