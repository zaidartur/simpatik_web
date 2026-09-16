<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('aplikasi');
    }

    public function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:100'],
            'email'     => ['nullable', 'email', 'max:255'],
            'level'     => ['required', 'string'],
            'username'  => ['required', 'string', 'unique:users,username', 'max:50'],
            'password'  => ['required', 'string', 'min:8', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'     => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'email.email'       => 'Format email tidak valid.',
        ];
    }
}
