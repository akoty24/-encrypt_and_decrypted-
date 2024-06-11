<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EncryptFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_path' => 'required|string',
            // 'custom_path' => 'nullable|string',
            // 'custom_file_name' => 'required|string',
            'file_extension' => 'required|string',
        ];
    }
}
