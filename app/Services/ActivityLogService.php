<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Record general activity log.
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): ActivityLog {
        $currentUser = $user ?: Auth::user();

        return ActivityLog::create([
            'user_uuid'   => $currentUser?->uuid,
            'user_id'     => $currentUser?->id,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model ? (string) ($model->uuid ?? $model->id) : null,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
