<?php

namespace App\Rules;

use App\Models\PoStockProduct;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidQtyIn implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $poStockId = request('po_stock');
        $productId = substr($attribute, 9);
        
        $poStockProduct = PoStockProduct::where([
            'po_stock_id' => $poStockId,
            'product_id'  => $productId
        ])->first();

        
        if ($value > $poStockProduct->remaining_qty) {
            $fail('The quantity must be less than or equal to the PO remaining quantity.');
        }
    }
}
