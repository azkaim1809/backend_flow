<?php

app/Http/Requests/SearchUserRequest.php;

class SearchUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'  => 'nullable|string|max:100',
            'email' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}