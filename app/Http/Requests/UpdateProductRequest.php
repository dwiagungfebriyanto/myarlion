<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_sku'      => 'required|unique:\App\Models\Product,product_sku',
            'category_id'      => 'required|exists:\App\Models\ProductCategory,id',
            'specification_id' => 'required|exists:\App\Models\ProductSpecification,id',
            'packaging_id'     => 'required|exists:\App\Models\ProductPackaging,id',
            'supplier_id'      => 'required|exists:\App\Models\Supplier,id',
            'brand'            => 'required|string',
            'qty'              => 'required|numeric|min:0',
            'note'             => 'required|string',
            'harga_rata_rata'  => 'required|numeric',
            'harga_tertinggi'  => 'required|numeric',
        ];
    }
}
