<?php

namespace App\Models;

use CodeIgniter\Model;

class PettyCashModel extends Model
{
    protected $table            = 'petty_cash';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'transaction_date',
        'type',
        'category',
        'description',
        'amount',
        'reference_no',
        'recorded_by',
    ];

    public function getWithDetails($limit = null)
    {
        $builder = $this->builder();
        $builder->select('petty_cash.*, users.first_name, users.last_name, CONCAT(users.first_name, " ", users.last_name) as recorded_by_name');
        $builder->join('users', 'users.id = petty_cash.recorded_by', 'left');
        $builder->orderBy('petty_cash.transaction_date DESC, petty_cash.id DESC');
        if ($limit !== null) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    public function getSummary()
    {
        $totalIncome  = $this->where('type', 'Income')->selectSum('amount', 'total')->get()->getRowArray()['total'] ?? 0;
        $totalExpenses = $this->where('type', 'Expense')->selectSum('amount', 'total')->get()->getRowArray()['total'] ?? 0;
        return [
            'total_income'    => (float) $totalIncome,
            'total_expenses'  => (float) $totalExpenses,
            'current_balance'  => (float) $totalIncome - (float) $totalExpenses,
        ];
    }

    public function getSummaryByPeriod($start_date, $end_date)
    {
        $totalIncome = $this->builder()
            ->selectSum('amount', 'total')
            ->where('type', 'Income')
            ->where('transaction_date >=', $start_date)
            ->where('transaction_date <=', $end_date)
            ->get()->getRowArray()['total'] ?? 0;

        $totalExpenses = $this->builder()
            ->selectSum('amount', 'total')
            ->where('type', 'Expense')
            ->where('transaction_date >=', $start_date)
            ->where('transaction_date <=', $end_date)
            ->get()->getRowArray()['total'] ?? 0;

        return [
            'total_income'    => (float) $totalIncome,
            'total_expenses'  => (float) $totalExpenses,
            'current_balance'  => (float) $totalIncome - (float) $totalExpenses,
        ];
    }

    public function getByCategory($type = null)
    {
        $builder = $this->select('category, type, SUM(amount) as total');
        if ($type !== null) {
            $builder->where('type', $type);
        }
        $builder->groupBy('category, type');
        $builder->orderBy('total', 'DESC');
        return $builder->findAll();
    }

    public function getRunningBalance()
    {
        $results = $this->select('petty_cash.*, users.first_name, users.last_name, CONCAT(users.first_name, " ", users.last_name) as recorded_by_name')
            ->join('users', 'users.id = petty_cash.recorded_by', 'left')
            ->orderBy('petty_cash.transaction_date ASC, petty_cash.id ASC')
            ->findAll();

        $runningBalance = 0;
        foreach ($results as &$row) {
            if ($row['type'] === 'Income') {
                $runningBalance += (float) $row['amount'];
            } else {
                $runningBalance -= (float) $row['amount'];
            }
            $row['running_balance'] = $runningBalance;
        }

        return $results;
    }
}
