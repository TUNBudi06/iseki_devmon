<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhoneUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['required', 'string', 'max:255', 'unique:phone_lists,model_id,'.$id],
            'model_name' => ['required', 'string', 'max:255'],
            'model_type' => ['required', 'string', 'max:255'],
            'buy_date' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string', 'max:255'],
            'storage' => ['required', 'string', 'max:255'],
            'departemen' => ['required', 'string', 'exists:departemens,id'],
            'imei' => ['nullable', 'string', 'max:17', 'unique:phone_lists,imei,'.$id],
            'mac_address' => ['nullable', 'string', 'max:17', 'unique:phone_lists,mac_address,'.$id],
            'thumbnail' => ['nullable', 'string'],
            'list_photos' => ['nullable', 'array'],
            'list_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
