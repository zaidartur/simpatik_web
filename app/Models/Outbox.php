<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Outbox extends Model
{
    use HasUuids;

    public function uniqueIds()
    {
        // return parent::uniqueIds();
        return ['uuid'];
    }

    /**
     * Get the klasifikasi associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function klasifikasi(): HasOne
    {
        return $this->hasOne(Klasifikasi::class, 'id', 'id_klasifikasi');
    }

    /**
     * Get the media associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function media(): HasOne
    {
        return $this->hasOne(MediaSurat::class, 'id', 'id_media');
    }

    /**
     * Get the berkas associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function berkas(): HasOne
    {
        return $this->hasOne(TempatBerkas::class, 'id', 'tempat_berkas');
    }

    /**
     * Get the sifat associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sifat(): HasOne
    {
        return $this->hasOne(SifatSurat::class, 'id', 'sifat_surat');
    }

    /**
     * Get the perkembangan associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function perkembangan(): HasOne
    {
        return $this->hasOne(Perkembangan::class, 'id', 'id_perkembangan');
    }

    /**
     * Get the level associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function level(): HasOne
    {
        return $this->hasOne(LevelUser::class, 'id', 'level_surat');
    }

    /**
     * Get the creator associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'uuid', 'created_by');
    }

    /**
     * Get the spd associated with the Outbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function spd(): HasOne
    {
        return $this->hasOne(Spd::class, 'id', 'id_spd');
    }

    /**
     * Get the pengolah associated with the Outbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pengolah(): HasOne
    {
        return $this->hasOne(DataUnit::class, 'id', 'id_unit');
    }
}
