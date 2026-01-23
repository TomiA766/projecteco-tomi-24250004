<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can manage categories
        $user = $this->user();
        return $user && $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;
        
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-&]+$/',
                'unique:categories,name,' . $categoryId
            ],
            'description' => 'nullable|string|max:1000',
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.min' => 'Nama kategori minimal 2 karakter.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.regex' => 'Nama kategori hanya boleh mengandung huruf, angka, spasi, dan tanda hubung.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diizinkan: JPEG, PNG, JPG, GIF, WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.dimensions' => 'Ukuran gambar harus antara 100x100px hingga 2000x2000px.'
        ];
    }
}