<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'phone',
        'password',
        'role',
        'profile_picture',
        'date_of_employment',
        'dob',
        'national_id',
        'gender',
        'address',
    ];

    public function getByCompanyId(string $company_id): ?array
    {
        return $this->where('company_id', $company_id)->first();
    }

    public function getByRole(string $role): array
    {
        return $this->where('role', $role)->findAll();
    }

    public function getLastCompanyIdNumber(string $prefix): int
    {
        $result = $this->select("CAST(SUBSTRING(company_id, LENGTH('$prefix') + 1) AS UNSIGNED) AS num")
            ->where("company_id LIKE '$prefix%'")
            ->orderBy('num', 'DESC')
            ->first();

        return $result ? (int) $result['num'] : 0;
    }
}
