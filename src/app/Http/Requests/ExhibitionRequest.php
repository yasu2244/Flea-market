<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可ロジックが必要ならここで制御
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string'],
            'brand'        => ['nullable', 'string', 'max:255'],
            'description'  => ['required', 'string', 'max:255'],
            'image'        => ['required', 'image', 'mimes:jpeg,png'],
            'categories'   => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'status_id'    => ['required', 'exists:statuses,id'],
            'price'        => ['required', 'numeric', 'min:0'],
        ];
    }
}
