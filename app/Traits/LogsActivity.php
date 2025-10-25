<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity.
     */
    public static function logActivity(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $user = Auth::guard('business_admin')->user();
        
        if (!$user || !$user->business) {
            return;
        }

        ActivityLog::create([
            'business_id' => $user->business->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log model creation.
     */
    public static function logCreated($model, string $description = null): void
    {
        $action = strtolower(class_basename($model)) . '_created';
        $description = $description ?? 'Created ' . class_basename($model);
        
        self::logActivity(
            $action,
            $description,
            get_class($model),
            $model->id,
            null,
            $model->toArray()
        );
    }

    /**
     * Log model update.
     */
    public static function logUpdated($model, array $oldValues, string $description = null): void
    {
        $action = strtolower(class_basename($model)) . '_updated';
        $description = $description ?? 'Updated ' . class_basename($model);
        
        self::logActivity(
            $action,
            $description,
            get_class($model),
            $model->id,
            $oldValues,
            $model->getChanges()
        );
    }

    /**
     * Log model deletion.
     */
    public static function logDeleted($model, string $description = null): void
    {
        $action = strtolower(class_basename($model)) . '_deleted';
        $description = $description ?? 'Deleted ' . class_basename($model);
        
        self::logActivity(
            $action,
            $description,
            get_class($model),
            $model->id,
            $model->toArray(),
            null
        );
    }
}
