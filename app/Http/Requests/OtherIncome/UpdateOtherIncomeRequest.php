<?php

namespace App\Http\Requests\OtherIncome;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateOtherIncomeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit other income');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $tableRecipient = Str::plural(request()->input('recipient_type')); // "suppliers"/"vendors"

        return [
            'date'          => 'required|date',
            'bank_account'  => 'required|exists:App\Models\BankAccount,id',
            'category'      => 'required|exists:App\Models\OtherIncomeCategory,id',
            'cost_category' => 'required|exists:App\Models\OutcomeType,id',
            'code'          => 'required|string',
            'code_type'     => 'required|string',
            'amount'        => 'required|numeric',
            'description'   => 'required|string',
            'recipient'     => "required|string|exists:$tableRecipient,id",
            'recipient_type'=> 'required|string|in:supplier,vendor',
        ];
    }
}
