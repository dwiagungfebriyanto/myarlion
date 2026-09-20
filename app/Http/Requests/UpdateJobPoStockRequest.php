<?php

namespace App\Http\Requests;

use App\Models\PoStockProduct;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPoStockRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jobPoStock = request()->jobPoStock;

        $poDetail = PoStockProduct::where([
            'po_stock_id' => $jobPoStock->po_stock_id,
            'product_id'  => $jobPoStock->product_id,
        ])->firstOrFail();

        $maxQty = $poDetail->remaining_qty + $jobPoStock->qty;

        return [
            'quantity' => "required|numeric|max:$maxQty",
            'amount'   => 'required|numeric',
            'note'     => 'nullable|string',
        ];
    }
}
