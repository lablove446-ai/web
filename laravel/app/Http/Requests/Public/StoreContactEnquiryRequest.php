<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:200'],
            'category' => ['required', 'string', 'in:general,membership,partnerships,welfare'],
            'message' => ['required', 'string', 'max:3000'],
            'website' => ['nullable', 'string', 'max:200'], // honeypot
        ];
    }

    public function isHoneypotTriggered(): bool
    {
        return filled($this->input('website'));
    }
}
