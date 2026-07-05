<?php

namespace App\Models;

use CodeIgniter\Model;

class OrgSettingsModel extends Model
{
    protected $table            = 'org_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'org_name',
        'org_logo',
        'org_address',
        'org_phone',
        'org_email',
        'org_website',
        'currency',
        'currency_symbol',
        'vat_rate',
        'default_labor_rate',
        'invoice_prefix',
        'invoice_due_days',
        'fin_year_start_month',
    ];

    public function getSettings(): array
    {
        $settings = $this->find(1);
        return $settings ?: [];
    }

    public function updateSettings(array $data): bool
    {
        return $this->update(1, $data);
    }
}
