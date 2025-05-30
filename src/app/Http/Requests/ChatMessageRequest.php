<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'body'  => 'required|string|max:400',
            'image' => 'nullable|file|mimes:jpeg,png',
        ];
    }

}
