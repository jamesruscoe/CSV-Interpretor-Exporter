<?php

namespace App\Modules\Uploads\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Please select a CSV file to upload',
            'csv_file.mimes' => 'The file must be a CSV or TXT file',
            'csv_file.max' => 'The file size must not exceed 2MB'
        ];
    }
}
