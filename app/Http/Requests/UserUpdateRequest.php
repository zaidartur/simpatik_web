<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('aplikasi');
    }

    public function rules(): array
    {
        return [
            'uid'       => ['required'],
            'nama'      => ['required', 'string', 'max:100'],
            'email'     => ['nullable', 'email', 'max:255'],
            'level'     => ['required', 'string'],
            'blokir'    => ['nullable', 'in:Y,N'],
        ];
    }

    public function messages(): array
    {
        return [
            'uid.required'  => 'User ID wajib disertakan.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.email'   => 'Format email tidak valid.',
        ];
    }
}
