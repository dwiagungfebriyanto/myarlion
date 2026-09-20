<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('add PO stock');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'po_type'             => 'required|in:stock,sample',
            'unique_id'           => 'nullable|string|unique:App\Models\PoStock,unique_id',
            'main_category'       => 'required|exists:App\Models\Main_Category,id',
            'supplier'            => 'required|exists:App\Models\Supplier,id',
            'product_id'          => 'required|array|min:1',
            'product_id.*'        => 'required|distinct|exists:App\Models\Product,id',
            'quantity'            => 'required|array',
            'quantity.*'          => 'required|numeric|min:1',
            'unit'                => 'required|array',
            'unit.*'              => 'required|exists:App\Models\Unit,id',
            'price'               => 'required|array',
            'price.*'             => 'required|numeric|min:0',
            'shipping_cost'       => 'required|numeric|min:0',
            'additional_expenses' => 'required|numeric|min:0',
            'total'               => 'required|numeric|min:0',
            'note'                => 'required|string|max:255',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $mainCategoryId = (int) $this->input('main_category');
            $supplierId     = (int) $this->input('supplier');

            if (
                $mainCategoryId > 0 &&
                $supplierId > 0 &&
                !Supplier::where('id', $supplierId)->where('main_category_id', $mainCategoryId)->exists()
            ) {
                $validator->errors()->add('supplier', 'Selected supplier does not belong to selected main category.');
            }

            $productIds = collect($this->input('product_id', []))
                ->filter(fn ($id) => !is_null($id) && $id !== '')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            if (empty($productIds)) {
                return;
            }

            $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $units      = (array) $this->input('unit', []);
            $quantities = (array) $this->input('quantity', []);
            $prices     = (array) $this->input('price', []);

            foreach ($productIds as $productId) {
                $product = $products->get($productId);

                if (!$product) {
                    continue;
                }

                if (
                    (int) $product->main_category_id !== $mainCategoryId ||
                    (int) $product->supplier_id !== $supplierId
                ) {
                    $validator->errors()->add(
                        'product_id',
                        "Product ID {$productId} does not match selected main category and supplier."
                    );
                }

                $hasUnit = false;
                $unit = $this->arrayValueByProductId($units, $productId, $hasUnit);
                if (!$hasUnit) {
                    $validator->errors()->add("unit.$productId", 'Unit is required for each selected product.');
                } elseif ((int) $unit !== (int) $product->unit_id) {
                    $validator->errors()->add("unit.$productId", "Unit for product ID {$productId} is invalid.");
                }

                $hasQty = false;
                $this->arrayValueByProductId($quantities, $productId, $hasQty);
                if (!$hasQty) {
                    $validator->errors()->add("quantity.$productId", 'Quantity is required for each selected product.');
                }

                $hasPrice = false;
                $this->arrayValueByProductId($prices, $productId, $hasPrice);
                if (!$hasPrice) {
                    $validator->errors()->add("price.$productId", 'Price is required for each selected product.');
                }
            }
        });
    }

    private function arrayValueByProductId(array $source, int $productId, bool &$exists): mixed
    {
        if (array_key_exists($productId, $source)) {
            $exists = true;
            return $source[$productId];
        }

        $stringKey = (string) $productId;
        if (array_key_exists($stringKey, $source)) {
            $exists = true;
            return $source[$stringKey];
        }

        $exists = false;
        return null;
    }
}
