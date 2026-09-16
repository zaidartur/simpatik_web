<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PimpinanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('pimpinan');
    }

    public function rules(): array
    {
        return [
            'uid'        => ['required', 'numeric'],
            'jabatan'    => ['required', 'string', 'max:100'],
            'nama'       => ['required', 'string', 'max:100'],
            'nip'        => ['nullable', 'string', 'max:255'],
            'pangkat'    => ['nullable', 'string', 'max:100'],
            'role'       => ['required', 'string', 'max:50'],
            'is_default' => ['required', 'string', 'in:yes,no'],
        ];
    }

    public function messages(): array
    {
        return [
            'uid.required'        => 'ID pimpinan wajib disertakan.',
            'jabatan.required'    => 'Jabatan wajib diisi.',
            'nama.required'       => 'Nama pimpinan wajib diisi.',
            'role.required'       => 'Role / Instansi pimpinan wajib dipilih.',
            'is_default.required' => 'Status default wajib dipilih.',
        ];
    }
}
