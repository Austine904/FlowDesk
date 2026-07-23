<?php

namespace App\Controllers;

use App\Models\OutgoingPaymentModel;
use App\Models\LpoModel;
use App\Models\LpoItemModel;
use App\Models\SubletModel;
use App\Models\SupplierModel;
use App\Models\UserModel;

class OutgoingPaymentsController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;

    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $model = new OutgoingPaymentModel();
        $db = \Config\Database::connect();

        $totalPaidMonth = (float) $db->table('outgoing_payments')
            ->select('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'Paid')
            ->where('MONTH(payment_date)', date('m'))
            ->where('YEAR(payment_date)', date('Y'))
            ->get()->getRowArray()['total'] ?? 0;

        $pendingCount = $model->where('status', 'Pending Approval')->countAllResults();

        $pendingAmount = (float) $db->table('outgoing_payments')
            ->select('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'Pending Approval')
            ->get()->getRowArray()['total'] ?? 0;

        $totalPaidAll = (float) $db->table('outgoing_payments')
            ->select('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'Paid')
            ->get()->getRowArray()['total'] ?? 0;

        $breakdownRows = $db->table('outgoing_payments')
            ->select('payment_type, COUNT(*) AS cnt, COALESCE(SUM(amount), 0) AS amt')
            ->groupBy('payment_type')
            ->get()->getResultArray();

        $breakdown = [];
        foreach ($breakdownRows as $r) {
            $breakdown[$r['payment_type']] = ['count' => (int) $r['cnt'], 'amount' => (float) $r['amt']];
        }

        return view('admin/outgoing_payments/index', compact(
            'totalPaidMonth', 'pendingCount', 'pendingAmount', 'totalPaidAll', 'breakdown'
        ));
    }

    public function load()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failUnauthorized('Unauthorized.');
        }

        $model = new OutgoingPaymentModel();
        $draw = $this->request->getPost('draw');
        $start = (int) ($this->request->getPost('start') ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 10);
        $search = $this->request->getPost('search')['value'] ?? '';
        $typeFilter = $this->request->getGet('type') ?? $this->request->getPost('type');
        $statusFilter = $this->request->getGet('status') ?? $this->request->getPost('status');

        $builder = $model->builder();
        $builder->select('
            outgoing_payments.*,
            suppliers.name AS supplier_name
        ')
        ->join('suppliers', 'suppliers.id = outgoing_payments.supplier_id', 'left');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('outgoing_payments.payment_ref', $search)
                ->orLike('suppliers.name', $search)
                ->groupEnd();
        }
        if (!empty($typeFilter)) {
            $builder->where('outgoing_payments.payment_type', $typeFilter);
        }
        if (!empty($statusFilter)) {
            $builder->where('outgoing_payments.status', $statusFilter);
        }

        $filteredRecords = $builder->countAllResults(false);
        $builder->orderBy('outgoing_payments.created_at', 'DESC');
        $rows = $builder->limit($length, $start)->get()->getResultArray();

        $model2 = new OutgoingPaymentModel();
        foreach ($rows as &$row) {
            $row = $model2->enrichWithNames($row);
            $row = $model2->enrichWithSourceRef($row);
        }

        return $this->respond([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows,
        ]);
    }

    public function raise()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('admin/outgoing_payments/raise_select');
    }

    public function raiseForm($type)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $type = strtolower($type);
        $model = new OutgoingPaymentModel();

        if ($type === 'lpo') {
            $sourceId = (int) ($this->request->getGet('source_id') ?? 0);
            if (!$sourceId) {
                return redirect()->to('/admin/lpos')->with('error', 'Select an LPO first.');
            }

            $gate = $model->validateGate('LPO', 'lpos', $sourceId);
            if (!$gate['valid']) {
                return redirect()->to('/admin/lpos/view/' . $sourceId)->with('error', $gate['message']);
            }

            $lpoModel = new LpoModel();
            $lpoItemModel = new LpoItemModel();
            $lpo = $lpoModel->getWithDetails($sourceId);
            $items = $lpoItemModel->getByLpo($sourceId);
            $alreadyPaid = $model->getTotalPaidForSource('lpos', $sourceId);
            $balanceDue = ($lpo['total_amount'] ?? 0) - $alreadyPaid;

            return view('admin/outgoing_payments/raise_lpo', compact('lpo', 'items', 'alreadyPaid', 'balanceDue'));
        }

        if ($type === 'sublet') {
            $sourceId = (int) ($this->request->getGet('source_id') ?? 0);
            if (!$sourceId) {
                return redirect()->to('/admin/sublets')->with('error', 'Select a sublet first.');
            }

            $gate = $model->validateGate('Sublet', 'sublets', $sourceId);
            if (!$gate['valid']) {
                return redirect()->to('/admin/sublets')->with('error', $gate['message']);
            }

            $subletModel = new SubletModel();
            $subletArr = $subletModel->getWithDetails($sourceId);
            $sublet = $subletArr ? $subletArr[0] : null;
            if (!$sublet) {
                return redirect()->to('/admin/sublets')->with('error', 'Sublet not found.');
            }

            $alreadyPaid = $model->getTotalPaidForSource('sublets', $sourceId);
            $balanceDue = ($sublet['cost'] ?? 0) - $alreadyPaid;

            return view('admin/outgoing_payments/raise_sublet', compact('sublet', 'alreadyPaid', 'balanceDue'));
        }

        if ($type === 'adhoc' || $type === 'ad-hoc') {
            if ($this->session->get('role') !== 'admin') {
                return redirect()->to('/admin/outgoing_payments')->with('error', 'Only admins can raise ad-hoc payments.');
            }

            $supplierModel = new SupplierModel();
            $userModel = new UserModel();
            $suppliers = $supplierModel->findAll();
            $staff = $userModel->whereIn('role', ['mechanic', 'receptionist', 'admin'])->findAll();

            return view('admin/outgoing_payments/raise_adhoc', compact('suppliers', 'staff'));
        }

        if (in_array($type, ['expense', 'staff_reimbursement'])) {
            return redirect()->to('/admin/outgoing_payments/raise')->with('info', 'Coming soon.');
        }

        return redirect()->to('/admin/outgoing_payments')->with('error', 'Invalid payment type.');
    }

    public function store()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $paymentType = $this->request->getPost('payment_type');
        $amount = (float) ($this->request->getPost('amount') ?? 0);
        $paymentMethod = $this->request->getPost('payment_method');

        $rules = [
            'payment_type' => 'required|in_list[LPO,Sublet,Expense,Staff Reimbursement,Ad-hoc]',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new OutgoingPaymentModel();
        $db = \Config\Database::connect();

        $sourceType = null;
        $sourceId = null;
        $supplierId = null;
        $payeeId = null;
        $payeeType = 'Supplier';
        $gateType = $paymentType;

        if ($paymentType === 'LPO') {
            $sourceId = (int) ($this->request->getPost('source_id') ?? 0);
            $sourceType = 'lpos';
            $gate = $model->validateGate('LPO', $sourceType, $sourceId);
            if (!$gate['valid']) {
                return redirect()->back()->with('error', $gate['message']);
            }
            $lpo = $db->table('lpos')->where('id', $sourceId)->get()->getRowArray();
            $supplierId = $lpo['supplier_id'] ?? null;
        } elseif ($paymentType === 'Sublet') {
            $sourceId = (int) ($this->request->getPost('source_id') ?? 0);
            $sourceType = 'sublets';
            $gate = $model->validateGate('Sublet', $sourceType, $sourceId);
            if (!$gate['valid']) {
                return redirect()->back()->with('error', $gate['message']);
            }
            $sublet = $db->table('sublets')->where('id', $sourceId)->get()->getRowArray();
            $supplierId = $sublet['sublet_provider_id'] ?? null;
        } elseif ($paymentType === 'Ad-hoc') {
            if ($this->session->get('role') !== 'admin') {
                return redirect()->back()->with('error', 'Only admins can raise ad-hoc payments.');
            }
            $payeeType = $this->request->getPost('payee_type');
            if ($payeeType === 'Supplier') {
                $supplierId = (int) ($this->request->getPost('supplier_id') ?? 0);
            } elseif ($payeeType === 'Staff') {
                $payeeId = (int) ($this->request->getPost('payee_id') ?? 0);
            }
            $sourceType = null;
            $sourceId = null;
        }

        $ref = $model->generatePaymentRef();
        $data = [
            'payment_ref' => $ref,
            'payment_type' => $paymentType,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'supplier_id' => $supplierId,
            'payee_id' => $payeeId,
            'payee_type' => $payeeType,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference_no' => $this->request->getPost('reference_no'),
            'notes' => $this->request->getPost('notes'),
            'status' => 'Pending Approval',
            'raised_by' => $this->session->get('user_id'),
        ];

        $model->insert($data);
        $id = $model->getInsertID();

        log_activity('outgoing_payment_raised', 'outgoing_payment', (int) $id,
            "Payment {$ref} raised — {$paymentType}");

        return redirect()->to('/admin/outgoing_payments')->with('success', "Payment {$ref} submitted for approval.");
    }

    public function approve($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $model = new OutgoingPaymentModel();
        $payment = $model->find($id);
        if (!$payment || $payment['status'] !== 'Pending Approval') {
            return $this->respond(['status' => 'error', 'message' => 'Payment not found or cannot be approved.'], 400);
        }

        $model->update($id, [
            'status' => 'Approved',
            'approved_by' => $this->session->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        log_activity('outgoing_payment_approved', 'outgoing_payment', (int) $id,
            "Payment {$payment['payment_ref']} approved");
        return $this->respond(['status' => 'success', 'message' => 'Payment approved.']);
    }

    public function reject($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $reason = $this->request->getPost('rejection_reason');
        if (empty($reason) || strlen($reason) < 10) {
            return $this->respond(['status' => 'error', 'message' => 'Rejection reason must be at least 10 characters.'], 400);
        }

        $model = new OutgoingPaymentModel();
        $payment = $model->find($id);
        if (!$payment || $payment['status'] !== 'Pending Approval') {
            return $this->respond(['status' => 'error', 'message' => 'Payment not found or cannot be rejected.'], 400);
        }

        $model->update($id, [
            'status' => 'Rejected',
            'rejection_reason' => $reason,
        ]);

        log_activity('outgoing_payment_rejected', 'outgoing_payment', (int) $id,
            "Payment {$payment['payment_ref']} rejected: {$reason}");
        return $this->respond(['status' => 'success', 'message' => 'Payment rejected.']);
    }

    public function markPaid($id)
    {
        if ($this->session->get('role') !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $paymentDate = $this->request->getPost('payment_date');
        if (empty($paymentDate)) {
            return $this->respond(['status' => 'error', 'message' => 'Payment date is required.'], 400);
        }

        $model = new OutgoingPaymentModel();
        $payment = $model->find($id);
        if (!$payment || $payment['status'] !== 'Approved') {
            return $this->respond(['status' => 'error', 'message' => 'Payment not found or cannot be marked as paid.'], 400);
        }

        $model->update($id, [
            'status' => 'Paid',
            'payment_date' => $paymentDate,
            'reference_no' => $this->request->getPost('reference_no'),
        ]);

        log_activity('outgoing_payment_paid', 'outgoing_payment', (int) $id,
            "Payment {$payment['payment_ref']} marked as paid");
        return $this->respond(['status' => 'success', 'message' => 'Payment marked as paid.']);
    }

    public function view($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $model = new OutgoingPaymentModel();
        $payment = $model->getWithDetails((int) $id);

        if (empty($payment)) {
            return redirect()->to('/admin/outgoing_payments')->with('error', 'Payment not found.');
        }

        $sourceDoc = null;
        $sourceItems = [];

        if ($payment['source_type'] === 'lpos' && !empty($payment['source_id'])) {
            $lpoModel = new LpoModel();
            $lpoItemModel = new LpoItemModel();
            $sourceDoc = $lpoModel->getWithDetails($payment['source_id']);
            $sourceItems = $lpoItemModel->getByLpo($payment['source_id']);
        } elseif ($payment['source_type'] === 'sublets' && !empty($payment['source_id'])) {
            $subletModel = new SubletModel();
            $sourceArr = $subletModel->getWithDetails($payment['source_id']);
            $sourceDoc = $sourceArr ? $sourceArr[0] : null;
        }

        return view('admin/outgoing_payments/view', compact('payment', 'sourceDoc', 'sourceItems'));
    }
}
