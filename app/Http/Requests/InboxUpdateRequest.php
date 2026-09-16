<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InboxUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('edit surat masuk');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uid'               => 'required|string',
            'berkas'            => 'required|string|max:255',
            'tgl_terima'        => 'required|date',
            'tgl_surat'         => 'required|date',
            'darikepada'        => 'required|string|max:255',
            'wilayah'           => 'required|string|max:100',
            'perihal'           => 'required|string|max:255',
            'isi'               => 'required|string',
            'klasifikasi_kode'  => 'required',
            'no_surat'          => 'required|string|max:100',
            'tempat_berkas'     => 'required|string|max:100',
            'perkembangan'      => 'required|string|max:100',
            'sifat_surat'       => 'nullable|string|max:100',
            'tindakan'          => 'nullable|string|max:255',
            'tgl_balas'         => 'nullable|date',
            'lampiran'          => 'nullable|string|max:50',
            'is_scan'           => 'nullable|mimes:pdf,jpeg,jpg,png|max:10240',
        ];
    }
}
