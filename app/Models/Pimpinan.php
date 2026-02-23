<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pimpinan extends Model
{
    /**
     * Get the leveluser associated with the Pimpinan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function leveluser(): HasOne
    {
        return $this->hasOne(LevelUser::class, 'id', 'level');
    }
}
