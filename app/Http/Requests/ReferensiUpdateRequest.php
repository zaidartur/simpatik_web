<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReferensiUpdateRequest extends FormRequest
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
                'id'        => 'required|integer',
                'klas3'     => 'required|string|max:50',
                'masalah3'  => 'required|string|max:255',
                'series'    => 'nullable|string|max:255',
                'r_aktif'   => 'required|integer|min:0',
                'r_inaktif' => 'required|integer|min:0',
                'ket_jra'   => 'nullable|string|max:255',
                'nilai_guna'=> 'nullable|string|max:255',
            ],
            'sifat-surat' => [
                'id'         => 'required|integer',
                'nama_sifat' => 'required|string|max:100',
            ],
            'tempat-berkas' => [
                'id'   => 'required|integer',
                'nama' => 'required|string|max:50',
            ],
            'perkembangan' => [
                'id'   => 'required|integer',
                'nama' => 'required|string|max:50',
            ],
            'media-surat' => [
                'id'   => 'required|integer',
                'nama' => 'required|string|max:100',
            ],
            default => [
                'id'   => 'required|integer',
                'nama' => 'required|string|max:255',
            ],
        };
    }
}
