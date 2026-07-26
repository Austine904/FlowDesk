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
        'uploaded_by',
        'photo_type',
        'caption',
        'created_at',
    ];

    public function getByJobCard(int $job_card_id): array
    {
        return $this->where('job_card_id', $job_card_id)->findAll();
    }

    public function getByJobCardAndType(int $jobCardId, ?string $type = null): array
    {
        $this->where('job_card_id', $jobCardId);
        if ($type !== null) {
            $this->where('photo_type', $type);
        }
        return $this->findAll();
    }
}
