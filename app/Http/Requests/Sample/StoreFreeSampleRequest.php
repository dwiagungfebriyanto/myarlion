<?php

namespace App\Http\Requests\Sample;

use App\Models\InventoryStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFreeSampleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('add free sample');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $inventoryStock = InventoryStock::find($this->input('inventory_stock_id'));
        
        return [
            'inventory_stock_id' => 'required|exists:inventory_stocks,id',
            'recipient_type' => ['required', Rule::in(['inquiry', 'customer'])],
            'inquiry' => [
                Rule::excludeIf($this->input('recipient_type') !== 'inquiry'),
                Rule::requiredIf($this->input('recipient_type') === 'inquiry'),
                Rule::exists('inquiries', 'id')->where(function ($query) {
                    $query->whereNull('job_id');
                }),
            ],
            'customer' => [
                Rule::excludeIf($this->input('recipient_type') !== 'customer'),
                Rule::requiredIf($this->input('recipient_type') === 'customer'),
                Rule::exists('customers', 'id'),
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
                function (string $attribute, mixed $value, \Closure $fail) use ($inventoryStock) {
                    if (is_null($inventoryStock)) {
                        return;
                    }

                    if ((float) $value > (float) $inventoryStock->unitValue()) {
                        $fail('The selected quantity is greater than available inventory.');
                    }
                },
            ],
            'date'             => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'inventory_stock_id.required' => 'Inventory wajib dipilih.',
            'inventory_stock_id.exists' => 'Inventory yang dipilih tidak tersedia atau sudah berubah.',
            'recipient_type.required' => 'Recipient type wajib dipilih.',
            'recipient_type.in' => 'Recipient type yang dipilih tidak valid.',
            'inquiry.required' => 'Inquiry wajib dipilih untuk recipient by inquiry.',
            'inquiry.exists' => 'Inquiry tidak valid atau inquiry tersebut sudah terhubung ke job.',
            'customer.required' => 'Customer wajib dipilih untuk recipient by customer.',
            'customer.exists' => 'Customer yang dipilih tidak valid.',
            'quantity.required' => 'Quantity wajib diisi.',
            'quantity.numeric' => 'Quantity harus berupa angka.',
            'quantity.gt' => 'Quantity harus lebih besar dari 0.',
            'date.required' => 'Date wajib diisi.',
            'date.date' => 'Date tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'inventory_stock_id' => 'inventory',
            'recipient_type' => 'recipient type',
            'inquiry' => 'inquiry',
            'customer' => 'customer',
            'quantity' => 'quantity',
            'date' => 'date',
        ];
    }
}
