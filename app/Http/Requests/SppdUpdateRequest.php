<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SppdUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('spd');
    }

    public function rules(): array
    {
        return [
            'uid'           => ['required'],
            'nosppd'        => ['required', 'string', 'max:100'],
            'nama'          => ['required', 'string', 'max:255'],
            'jabatan'       => ['required', 'string', 'max:255'],
            'tujuan'        => ['required', 'string', 'max:255'],
            'kendaraan'     => ['required', 'string', 'max:255'],
            'tgl_surat'     => ['required', 'date'],
            'tgl_berangkat' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'uid.required'           => 'ID SPPD wajib disertakan.',
            'nosppd.required'        => 'Nomor SPPD wajib diisi.',
            'nama.required'          => 'Nama pegawai wajib diisi.',
            'jabatan.required'       => 'Jabatan wajib diisi.',
            'tujuan.required'        => 'Tujuan perjalanan dinas wajib diisi.',
            'kendaraan.required'     => 'Kendaraan dinas wajib diisi.',
            'tgl_surat.required'     => 'Tanggal surat wajib diisi.',
            'tgl_berangkat.required' => 'Tanggal keberangkatan wajib diisi.',
        ];
    }
}
