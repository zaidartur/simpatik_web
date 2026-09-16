<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PimpinanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('pimpinan');
    }

    public function rules(): array
    {
        return [
            'jabatan'    => ['required', 'string', 'max:100'],
            'nama'       => ['required', 'string', 'max:100'],
            'nip'        => ['nullable', 'string', 'max:255'],
            'pangkat'    => ['nullable', 'string', 'max:100'],
            'role'       => ['required', 'integer'],
            'is_default' => ['required', 'string', 'in:yes,no'],
        ];
    }

    public function messages(): array
    {
        return [
            'jabatan.required'    => 'Jabatan wajib diisi.',
            'nama.required'       => 'Nama pimpinan wajib diisi.',
            'role.required'       => 'Role / Instansi pimpinan wajib dipilih.',
            'is_default.required' => 'Status default wajib dipilih.',
        ];
    }
}
