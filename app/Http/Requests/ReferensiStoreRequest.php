<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReferensiStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->can('referensi') || $this->user()->hasRole('administrator'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->route('type');

        return match ($type) {
            'klasifikasi' => [
                'klas3'     => 'required|string|max:50',
                'masalah3'  => 'required|string|max:255',
                'series'    => 'nullable|string|max:255',
                'r_aktif'   => 'required|integer|min:0',
                'r_inaktif' => 'required|integer|min:0',
                'ket_jra'   => 'nullable|string|max:255',
                'nilai_guna'=> 'nullable|string|max:255',
            ],
            'sifat-surat' => [
                'nama_sifat' => 'required|string|max:100',
            ],
            'tempat-berkas' => [
                'nama' => 'required|string|max:50',
            ],
            'perkembangan' => [
                'nama' => 'required|string|max:50',
            ],
            'media-surat' => [
                'nama' => 'required|string|max:100',
            ],
            default => [
                'nama' => 'required|string|max:255',
            ],
        };
    }
}
