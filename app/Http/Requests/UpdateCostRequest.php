<?php

namespace App\Http\Requests;

use App\Models\OutcomeCheque;
use App\Models\PoStock;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->can('add cost') || auth()->user()->can('edit cost')) {
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
    public function rules(): array
    {
        $maxAmount = (request('code_type') === 'po_stock')
                        ? "|max:" .$this->getPoStockRemainingBill(request('code'), request('cost'))
                        : "";

        return [
            "date"           => "required|date",
            "bank_account"   => "required|exists:App\Models\BankAccount,id",
            "category"       => "required|exists:App\Models\OutcomeType,id",
            "code"           => "required",
            "po_stock"       => "exists:App\Models\PoStock,id|nullable",
            "amount"         => "required|numeric" .$maxAmount,
            "recipient_type" => "required|string",
            "recipient"      => "required|numeric",
            "note"           => "required|string",
            "receipt_file_1" => "mimes:jpg,png,pdf|max:2048|nullable",
            "receipt_file_2" => "mimes:jpg,png,pdf|max:2048|nullable",
        ];
    }

    private function getPoStockRemainingBill($poStockID, $exceptCost) {
        $paidAmount = OutcomeCheque::where('code', $poStockID)
                                    ->where('code_type', 'po_stock')
                                    ->where('id', '!=', $exceptCost->id)
                                    ->sum('amount');

        $poStock = PoStock::findOrFail($poStockID);

        $remainingBill = $poStock->total - $paidAmount;

        return $remainingBill;
    }
}
