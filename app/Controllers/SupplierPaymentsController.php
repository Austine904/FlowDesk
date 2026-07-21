<?php

namespace App\Controllers;

use App\Models\SupplierPaymentModel;
use App\Models\LpoModel;
use App\Models\LpoItemModel;
use App\Models\SupplierModel;
use CodeIgniter\API\ResponseTrait;

class SupplierPaymentsController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $model = new SupplierPaymentModel();

        $pendingPayments = $model->getPendingApprovals();
        $pendingCount = count($pendingPayments);
        $pendingAmount = array_sum(array_column($pendingPayments, 'amount'));

        $totalPaidThisMonth = $model->select('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'Paid')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->get()
            ->getRowArray();

        $totalPaidAllTime = $model->select('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'Paid')
            ->get()
            ->getRowArray();

        return view('admin/supplier_payments/index', [
            'pageTitle' => 'Supplier Payments',
            'pendingCount' => $pendingCount,
            'pendingAmount' => $pendingAmount,
            'totalPaidThisMonth' => (float) ($totalPaidThisMonth['total'] ?? 0),
            'totalPaidAllTime' => (float) ($totalPaidAllTime['total'] ?? 0),
        ]);
    }

    public function load()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $model = new SupplierPaymentModel();

        $draw   = $this->request->getPost('draw');
        $start  = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $builder = $model->builder();
        $builder->select('
            supplier_payments.*,
            suppliers.name AS supplier_name,
            lpos.lpo_no,
            raised.first_name AS raised_by_first_name,
            raised.last_name AS raised_by_last_name,
            approved.first_name AS approved_by_first_name,
            approved.last_name AS approved_by_last_name
        ')
        ->join('suppliers', 'suppliers.id = supplier_payments.supplier_id', 'left')
        ->join('lpos', 'lpos.id = supplier_payments.lpo_id', 'left')
        ->join('users AS raised', 'raised.id = supplier_payments.raised_by', 'left')
        ->join('users AS approved', 'approved.id = supplier_payments.approved_by', 'left');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('supplier_payments.payment_ref', $search)
                ->orLike('suppliers.name', $search)
                ->orLike('lpos.lpo_no', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('supplier_payments.created_at', 'DESC');
        $data = $builder->limit($length, $start)->get()->getResultArray();

        foreach ($data as &$row) {
            $row['raised_by_name'] = trim(($row['raised_by_first_name'] ?? '') . ' ' . ($row['raised_by_last_name'] ?? ''));
            $row['approved_by_name'] = trim(($row['approved_by_first_name'] ?? '') . ' ' . ($row['approved_by_last_name'] ?? ''));
        }

        return $this->respond([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    public function raise($lpo_id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $lpoModel = new LpoModel();
        $lpoItemModel = new LpoItemModel();
        $supplierModel = new SupplierModel();
        $supplierPaymentModel = new SupplierPaymentModel();

        $lpo = $lpoModel->getWithDetails((int) $lpo_id);
        if (!$lpo) {
            return redirect()->to('/admin/lpos')->with('error', 'LPO not found.');
        }

        if ($lpo['status'] !== 'Received') {
            return redirect()->to('/admin/lpos/view/' . $lpo_id)
                ->with('error', 'Payment can only be raised for received LPOs. Current status: ' . $lpo['status']);
        }

        $existingPending = $supplierPaymentModel->where('lpo_id', $lpo_id)
            ->whereIn('status', ['Pending Approval', 'Approved'])
            ->countAllResults();

        if ($existingPending > 0) {
            return redirect()->to('/admin/lpos/view/' . $lpo_id)
                ->with('error', 'A payment is already pending or approved for this LPO.');
        }

        $items = $lpoItemModel->getByLpo((int) $lpo_id);
        $supplier = $supplierModel->find($lpo['supplier_id']);
        $alreadyPaid = $supplierPaymentModel->getTotalPaidForLpo((int) $lpo_id);
        $balanceDue = $lpo['total_amount'] - $alreadyPaid;

        return view('admin/supplier_payments/raise', compact('lpo', 'items', 'supplier', 'alreadyPaid', 'balanceDue'));
    }

    public function store()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'lpo_id'         => 'required|integer',
            'amount'         => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[Cash,M-Pesa,Bank Transfer,Cheque,Other]',
            'notes'          => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lpo_id = (int) $this->request->getPost('lpo_id');
        $amount = (float) $this->request->getPost('amount');

        $lpoModel = new LpoModel();
        $lpo = $lpoModel->find($lpo_id);
        if (!$lpo) {
            return redirect()->to('/admin/lpos')->with('error', 'LPO not found.');
        }

        if ($lpo['status'] !== 'Received') {
            return redirect()->to('/admin/lpos/view/' . $lpo_id)
                ->with('error', 'Payment can only be raised for received LPOs. Current status: ' . $lpo['status']);
        }

        $supplierPaymentModel = new SupplierPaymentModel();
        $existingPending = $supplierPaymentModel->where('lpo_id', $lpo_id)
            ->whereIn('status', ['Pending Approval', 'Approved'])
            ->countAllResults();

        if ($existingPending > 0) {
            return redirect()->to('/admin/lpos/view/' . $lpo_id)
                ->with('error', 'A payment is already pending or approved for this LPO.');
        }

        $alreadyPaid = $supplierPaymentModel->getTotalPaidForLpo($lpo_id);
        $balanceDue = $lpo['total_amount'] - $alreadyPaid;

        if ($amount > $balanceDue) {
            return redirect()->back()->withInput()
                ->with('error', 'Amount exceeds balance due of ' . org_setting('currency_symbol', 'KSh') . ' ' . number_format($balanceDue, 2) . '.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $id = $supplierPaymentModel->insert([
            'payment_ref'    => $supplierPaymentModel->generatePaymentRef(),
            'lpo_id'         => $lpo_id,
            'supplier_id'    => $lpo['supplier_id'],
            'amount'         => $amount,
            'payment_method' => $this->request->getPost('payment_method'),
            'notes'          => $this->request->getPost('notes'),
            'status'         => 'Pending Approval',
            'raised_by'      => (int) session()->get('user_id'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Transaction failed.');
        }

        $payment = $supplierPaymentModel->find($id);
        $lpoRecord = $lpoModel->find($lpo_id);
        log_activity('supplier_payment_raised', 'supplier_payment', $id,
            'Payment of ' . org_setting('currency_symbol', 'KSh') . ' ' . number_format($amount, 2) .
            ' raised for LPO ' . ($lpoRecord['lpo_no'] ?? ''));

        return redirect()->to('/admin/supplier_payments')
            ->with('success', 'Supplier payment raised and submitted for approval.');
    }

    public function approve($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $model = new SupplierPaymentModel();
        $payment = $model->find((int) $id);

        if (!$payment) {
            return $this->failNotFound('Payment not found.');
        }

        if ($payment['status'] !== 'Pending Approval') {
            return $this->fail('Only pending payments can be approved.', 403);
        }

        $model->update($id, [
            'status'       => 'Approved',
            'approved_by'  => (int) session()->get('user_id'),
            'approved_at'  => date('Y-m-d H:i:s'),
        ]);

        log_activity('supplier_payment_approved', 'supplier_payment', $id,
            'Payment ' . $payment['payment_ref'] . ' approved');

        return $this->respond(['status' => 'success', 'message' => 'Payment approved successfully.']);
    }

    public function reject($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $rejectionReason = $this->request->getPost('rejection_reason');
        if (empty($rejectionReason)) {
            return $this->fail('Rejection reason is required.', 400);
        }

        $model = new SupplierPaymentModel();
        $payment = $model->find((int) $id);

        if (!$payment) {
            return $this->failNotFound('Payment not found.');
        }

        if ($payment['status'] !== 'Pending Approval') {
            return $this->fail('Only pending payments can be rejected.', 403);
        }

        $model->update($id, [
            'status'            => 'Rejected',
            'rejection_reason'  => $rejectionReason,
        ]);

        log_activity('supplier_payment_rejected', 'supplier_payment', $id,
            'Payment ' . $payment['payment_ref'] . ' rejected: ' . $rejectionReason);

        return $this->respond(['status' => 'success', 'message' => 'Payment rejected.']);
    }

    public function markPaid($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $paymentDate = $this->request->getPost('payment_date');
        $referenceNo = $this->request->getPost('reference_no');

        if (empty($paymentDate)) {
            return $this->fail('Payment date is required.', 400);
        }

        $model = new SupplierPaymentModel();
        $payment = $model->find((int) $id);

        if (!$payment) {
            return $this->failNotFound('Payment not found.');
        }

        if ($payment['status'] !== 'Approved') {
            return $this->fail('Payment must be approved before marking as paid.', 403);
        }

        $model->update($id, [
            'status'       => 'Paid',
            'payment_date' => $paymentDate,
            'reference_no' => $referenceNo ?: null,
        ]);

        log_activity('supplier_payment_paid', 'supplier_payment', $id,
            'Payment ' . $payment['payment_ref'] . ' marked as paid via ' . $payment['payment_method']);

        return $this->respond(['status' => 'success', 'message' => 'Payment marked as paid.']);
    }

    public function view($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $model = new SupplierPaymentModel();
        $lpoModel = new LpoModel();
        $lpoItemModel = new LpoItemModel();

        $payment = $model->getWithDetails((int) $id);
        if (empty($payment)) {
            return redirect()->to('/admin/supplier_payments')->with('error', 'Payment not found.');
        }

        $lpo = $lpoModel->getWithDetails($payment['lpo_id']);
        $items = $lpoItemModel->getByLpo($payment['lpo_id']);

        return view('admin/supplier_payments/view', compact('payment', 'lpo', 'items'));
    }
}
