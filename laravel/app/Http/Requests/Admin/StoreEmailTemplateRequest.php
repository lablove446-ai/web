<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:100', 'unique:website_email_templates,key'],
            'name' => ['required', 'string', 'max:200'],
            'subject' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'variables' => ['nullable', 'array'],
            'is_enabled' => ['boolean'],
        ];
    }
}
