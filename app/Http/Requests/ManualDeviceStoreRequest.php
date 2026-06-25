<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualDeviceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'string', 'exists:brands,id'],
            'model_id' => ['required', 'string', 'max:255', 'unique:phone_lists,model_id'],
            'model_name' => ['required', 'string', 'max:255'],
            'model_type' => ['required', 'string', 'max:255'],
            'buy_date' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string', 'max:255'],
            'storage' => ['required', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:17', 'unique:phone_lists,imei'],
            'mac_address' => ['nullable', 'string', 'max:17', 'unique:phone_lists,mac_address'],
        ];
    }
}
