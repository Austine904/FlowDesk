<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCardPhotoModel extends Model
{
    protected $table            = 'job_card_photos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'job_card_id',
        'file_path',
        'file_name',
    ];

    public function getByJobCard(int $job_card_id): array
    {
        return $this->where('job_card_id', $job_card_id)->findAll();
    }
}
