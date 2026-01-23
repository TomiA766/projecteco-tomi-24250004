<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can manage products
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
        $productId = $this->route('product')?->id;
        
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-&()\/]+$/',
                'unique:products,name,' . $productId
            ],
            'price' => [
                'required',
                'numeric',
                'min:1000',
                'max:999999999',
                'regex:/^[0-9]+(\.[0-9]{1,2})?$/'
            ],
            'discount_price' => [
                'nullable',
                'numeric',
                'min:1000',
                'max:999999999',
                'regex:/^[0-9]+(\.[0-9]{1,2})?$/',
                'lt:price'
            ],
            'discount_price' => [
                'nullable',
                'numeric',
                'min:1000',
                'max:999999999',
                'regex:/^[0-9]+(\.[0-9]{1,2})?$/',
                'lt:price'
            ],
            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:999999'
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id'
            ],
            'description' => 'nullable|string|max:2000',
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                'dimensions:min_width=200,min_height=200,max_width=2000,max_height=2000'
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
            'name.required' => 'Nama produk wajib diisi.',
            'name.min' => 'Nama produk minimal 3 karakter.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'name.regex' => 'Nama produk hanya boleh mengandung huruf, angka, spasi, dan karakter khusus yang valid.',
            'name.unique' => 'Nama produk sudah digunakan.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga minimal Rp 1.000.',
            'price.max' => 'Harga maksimal Rp 999.999.999.',
            'price.regex' => 'Format harga tidak valid. Gunakan titik sebagai pemisah desimal.',
            'discount_price.numeric' => 'Harga diskon harus berupa angka.',
            'discount_price.min' => 'Harga diskon minimal Rp 1.000.',
            'discount_price.max' => 'Harga diskon maksimal Rp 999.999.999.',
            'discount_price.regex' => 'Format harga diskon tidak valid. Gunakan titik sebagai pemisah desimal.',
            'discount_price.lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok tidak boleh kurang dari 0.',
            'stock.max' => 'Stok maksimal 999.999.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diizinkan: JPEG, PNG, JPG, GIF, WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.dimensions' => 'Ukuran gambar harus antara 200x200px hingga 2000x2000px.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama Produk',
            'price' => 'Harga',
            'discount_price' => 'Harga Diskon',
            'stock' => 'Stok',
            'category_id' => 'Kategori',
            'description' => 'Deskripsi',
            'image' => 'Gambar Produk'
        ];
    }
}