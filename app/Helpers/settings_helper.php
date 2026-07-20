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

function number_to_words(float $amount, string $currency = 'Kenya Shillings'): string
{
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
             'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
             'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $intPart = (int) floor($amount);
    $decPart = (int) round(($amount - $intPart) * 100);

    $convertHundreds = function(int $n) use ($ones, $tens): string {
        $result = '';
        if ($n >= 100) {
            $result .= $ones[(int)($n / 100)] . ' Hundred ';
            $n %= 100;
        }
        if ($n >= 20) {
            $result .= $tens[(int)($n / 10)] . ' ';
            $n %= 10;
        }
        if ($n > 0) {
            $result .= $ones[$n] . ' ';
        }
        return $result;
    };

    if ($intPart === 0) {
        $words = 'Zero';
    } else {
        $words = '';
        if ($intPart >= 1000000) {
            $words .= $convertHundreds((int)($intPart / 1000000)) . 'Million ';
            $intPart %= 1000000;
        }
        if ($intPart >= 1000) {
            $words .= $convertHundreds((int)($intPart / 1000)) . 'Thousand ';
            $intPart %= 1000;
        }
        if ($intPart > 0) {
            $words .= $convertHundreds($intPart);
        }
    }

    $result = $currency . ' ' . trim($words);
    if ($decPart > 0) {
        $result .= ' and ' . $decPart . '/100';
    }
    return strtoupper(trim($result)) . ' ONLY';
}
