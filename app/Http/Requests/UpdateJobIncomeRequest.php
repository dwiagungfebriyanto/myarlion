<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobIncomeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit income job');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $requiredConvertion = (request()->jobIncome->job->currency_id != 3) ? 'required' : 'nullable' ;

        return [
            'datetime'     => 'required|date',
            'bank_account' => 'required|exists:App\Models\BankAccount,id',
            'nominal'      => 'required|numeric',
            'convertion'   => "$requiredConvertion|numeric",
        ];
    }
}
