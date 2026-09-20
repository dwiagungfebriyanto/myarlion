<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
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
            'date'                => 'required|date',
            'name'                => 'required|string',
            'country_code'        => 'required|exists:\App\Models\Country,country_code',
            'city_id'             => 'required|exists:\App\Models\City,id',
            'product_category_id' => 'required|exists:\App\Models\ProductCategory,id',
            'channel_id'          => 'required|exists:\App\Models\Channel,id',
            'user_id'             => 'required|exists:\App\Models\User,id',
            'phone'               => 'required|string',
            'email'               => 'required|email',
            'note'                => 'required|string',
        ];
    }
}
