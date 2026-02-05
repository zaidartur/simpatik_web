<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelUser extends Model
{
    protected $casts = [
        'akses' => 'array',
    ];

    public function getRelatedLevelUserAttribute()
    {
        // Remove self ID if exists
        // $ids = array_filter($this->akses ?? [], fn($id) => $id != $this->id);

        $excluded = [1, 9]; // admin
        $ids = array_diff($this->akses, $excluded);

        return LevelUser::whereIn('id', $ids)->get(['id', 'nama']);
    }
}
