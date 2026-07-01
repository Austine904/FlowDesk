<?php

namespace App\Models;

use CodeIgniter\Model;

class NextOfKinModel extends Model
{
    protected $table            = 'next_of_kin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_id',
        'kin_first_name',
        'kin_last_name',
        'relationship',
        'kin_phone_number',
    ];

    public function getByUserId(int $user_id): ?array
    {
        return $this->where('user_id', $user_id)->first();
    }
}
