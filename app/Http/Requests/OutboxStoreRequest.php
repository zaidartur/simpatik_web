<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutboxStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('input surat keluar');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'berkas'            => 'required|string|max:255',
            'tgl_naik'          => 'required|date',
            'tgl_surat'         => 'required|date',
            'tgl_diteruskan'    => 'required|date',
            'darikepada'        => 'required|string|max:255',
            'wilayah'           => 'required|string|max:100',
            'perihal'           => 'nullable|string|max:255',
            'isi'               => 'required|string',
            'klasifikasi_kode'  => 'required',
            'tempat_berkas'     => 'required|string|max:100',
            'perkembangan'      => 'required|string|max:100',
            'sifat_surat'       => 'nullable|string|max:100',
            'no_surat'          => 'nullable|string|max:100',
            'kode_up'           => 'nullable|string|max:50',
            'nama_up'           => 'nullable|string|max:150',
            'ttd'               => 'nullable|string|max:100',
            'lampiran'          => 'nullable|string|max:50',
            'is_scan'           => 'nullable|mimes:pdf,jpeg,jpg,png|max:10240',
            'keterangan'        => 'nullable|string|max:300',
            'sppd'              => 'nullable|string|max:50',
            'nama'              => 'nullable|string|max:100',
            'jabatan'           => 'nullable|string|max:100',
            'tujuan'            => 'nullable|string|max:255',
            'kendaraan'         => 'nullable|string|max:100',
            'berangkat'         => 'nullable|date',
        ];
    }
}
