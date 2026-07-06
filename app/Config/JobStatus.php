<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JobStatus extends BaseConfig
{
    public array $transitions = [
        'Awaiting Assignment' => [
            'admin'        => ['Awaiting Diagnosis', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => [],
        ],
        'Awaiting Diagnosis' => [
            'admin'        => ['Diagnosis Complete', 'On Hold', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => ['Diagnosis Complete'],
        ],
        'Diagnosis Complete' => [
            'admin'        => ['Approved', 'Quote Sent', 'Rework', 'On Hold', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => [],
        ],
        'Quote Sent' => [
            'admin'        => ['Approved', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => [],
        ],
        'Approved' => [
            'admin'        => ['In Progress', 'On Hold', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => ['In Progress'],
        ],
        'In Progress' => [
            'admin'        => ['Quality Check', 'Awaiting Parts', 'On Hold', 'Cancelled', 'Rework'],
            'receptionist' => [],
            'mechanic'     => ['Quality Check', 'Awaiting Parts'],
        ],
        'Awaiting Parts' => [
            'admin'        => ['In Progress', 'On Hold', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => ['In Progress'],
        ],
        'Quality Check' => [
            'admin'        => ['Ready for Invoice', 'Rework', 'On Hold'],
            'receptionist' => [],
            'mechanic'     => ['Rework'],
        ],
        'Ready for Invoice' => [
            'admin'        => ['Paid', 'On Hold'],
            'receptionist' => ['Paid'],
            'mechanic'     => [],
        ],
        'Paid' => [
            'admin'        => ['Completed'],
            'receptionist' => [],
            'mechanic'     => [],
        ],
        'On Hold' => [
            'admin'        => ['Awaiting Diagnosis', 'In Progress', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => [],
        ],
        'Rework' => [
            'admin'        => ['In Progress', 'Cancelled'],
            'receptionist' => [],
            'mechanic'     => ['In Progress'],
        ],
        'Completed' => [
            'admin'        => [],
            'receptionist' => [],
            'mechanic'     => [],
        ],
        'Cancelled' => [
            'admin'        => [],
            'receptionist' => [],
            'mechanic'     => [],
        ],
    ];

    public function getValidTransitions(string $currentStatus, string $role): array
    {
        return $this->transitions[$currentStatus][$role] ?? [];
    }
}
