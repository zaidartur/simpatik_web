<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Inbox extends Model
{
    use HasUuids;

    public function uniqueIds()
    {
        // return parent::uniqueIds();
        return ['uuid'];
    }

    /**
     * Get all of the disposisi for the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disposisi(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'uid_surat', 'uuid');
    }

    /**
     * Get the mydisposisi associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mydisposisi(): HasOne
    {
        return $this->hasOne(Disposisi::class, 'uid_surat', 'uuid')->where('disposisis.pengirim_uuid', Auth::user()->uuid);
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
     * Get the posisi associated with the Inbox
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function posisi(): HasOne
    {
        return $this->hasOne(User::class, 'uuid', 'posisi_surat');
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
}

