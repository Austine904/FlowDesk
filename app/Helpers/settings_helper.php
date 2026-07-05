<?php

use App\Models\OrgSettingsModel;

function org_setting(string $key, $default = null)
{
    static $settings = null;
    if ($settings === null) {
        $model = new OrgSettingsModel();
        $settings = $model->getSettings();
    }
    return $settings[$key] ?? $default;
}
