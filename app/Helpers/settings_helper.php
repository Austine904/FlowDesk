<?php

use App\Models\OrgSettingsModel;
use App\Models\ActivityLogModel;

function org_setting(string $key, $default = null)
{
    static $settings = null;
    if ($settings === null) {
        $model = new OrgSettingsModel();
        $settings = $model->getSettings();
    }
    return $settings[$key] ?? $default;
}

function log_activity(string $action, string $entity_type, ?int $entity_id, string $description): void
{
    $user_id = session()->get('user_id');
    if (!$user_id) {
        return;
    }
    $model = new ActivityLogModel();
    $model->log($user_id, $action, $entity_type, $entity_id, $description);
}
