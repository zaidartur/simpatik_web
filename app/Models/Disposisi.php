<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_uuid', 'uuid');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_uuid', 'uuid');
    }
}
