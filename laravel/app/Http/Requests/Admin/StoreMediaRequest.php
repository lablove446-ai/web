<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240'],
            'alt_text' => ['nullable', 'string', 'max:300'],
            'caption' => ['nullable', 'string', 'max:300'],
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credit' => ['nullable', 'string', 'max:200'],
            'copyright' => ['nullable', 'string', 'max:200'],
            'source' => ['nullable', 'string', 'max:200'],
            'visibility' => ['nullable', 'string', 'in:public,private'],
        ];
    }
}
