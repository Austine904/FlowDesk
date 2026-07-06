<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    @media print {
        body { background: #fff !important; }
        .sidebar, #sidebar, .no-print, .modal, .modal-backdrop { display: none !important; }
        .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .print-break-inside { page-break-inside: avoid; }
    }
    .invoice-header { border-bottom: 2px solid #007bff; padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
    .invoice-title { font-size: 2rem; font-weight: 700; color: #007bff; }
    .totals-table td { padding: 0.5rem 0.75rem; }
    .totals-table .grand-total { font-size: 1.3rem; font-weight: 700; color: #007bff; }
    .balance-due { font-size: 1.2rem; font-weight: 700; color: #dc3545; }
</style>

<div class="container mt-4 mb-5">
    <div class="no-print d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('admin/invoices') ?>" class="btn btn-outline-secondary">&larr; Back to Invoices</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Print Invoice</button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success no-print"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger no-print"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors = session()->getFlashdata('errors'))): ?>
        <div class="alert alert-danger no-print">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <div class="invoice-header d-flex justify-content-between align-items-start">
                <div>
                    <?php $logo = org_setting('org_logo'); ?>
                    <?php if ($logo): ?>
                        <img src="<?= base_url($logo) ?>" alt="Logo" style="max-height: 60px; margin-bottom: 0.5rem;"><br>
                    <?php endif; ?>
                    <strong><?= esc(org_setting('org_name', 'FlowDesk Organization')) ?></strong><br>
                    <small><?= nl2br(esc(org_setting('org_address', ''))) ?><br>
                    <?= esc(org_setting('org_phone', '')) ?> | <?= esc(org_setting('org_email', '')) ?></small>
                </div>
                <div class="text-end">
                    <div class="invoice-title">INVOICE</div>
                    <div><strong>Invoice No:</strong> <?= esc($invoice['invoice_no']) ?></div>
                    <div><strong>Date:</strong> <?= esc($invoice['invoice_date']) ?></div>
                    <div><strong>Due Date:</strong> <?= esc($invoice['due_date']) ?></div>
                    <div class="mt-1">
                        <?php
                            $badgeMap = [
                                'Draft' => 'bg-secondary', 'Sent' => 'bg-primary',
                                'Partially Paid' => 'bg-warning text-dark', 'Paid' => 'bg-success',
                                'Overdue' => 'bg-danger', 'Cancelled' => 'bg-dark',
                            ];
                            $bc = $badgeMap[$invoice['status']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $bc ?> fs-6"><?= esc($invoice['status']) ?></span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Bill To:</h6>
                    <strong><?= esc($invoice['customer_name'] ?? 'N/A') ?></strong><br>
                    <small><?= esc($invoice['customer_phone'] ?? '') ?></small>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-muted">Reference:</h6>
                    <strong>Job No:</strong> <?= esc($invoice['job_no'] ?? 'N/A') ?><br>
                </div>
            </div>

            <h6 class="text-muted mb-2">Parts</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Part Name</th><th>Part No</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <?php if (!empty($parts)): ?>
                        <?php foreach ($parts as $part): ?>
                            <tr>
                                <td><?= esc($part['name'] ?? 'N/A') ?></td>
                                <td><?= esc($part['part_number'] ?? 'N/A') ?></td>
                                <td><?= (int) ($part['quantity_required'] ?? 0) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($part['unit_price_at_estimate'] ?? 0, 2) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format(($part['quantity_required'] ?? 0) * ($part['unit_price_at_estimate'] ?? 0), 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-muted text-center">No parts recorded.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h6 class="text-muted mb-2">Labor Tasks</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Task</th><th>Hours</th><th>Rate/hr</th><th>Cost</th></tr>
                </thead>
                <tbody>
                    <?php if (!empty($tasks)): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= esc($task['task_name'] ?? 'N/A') ?></td>
                                <td><?= number_format($task['estimated_hours'] ?? 0, 2) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($task['rate_per_hour'] ?? 0, 2) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($task['labor_cost'] ?? 0, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-muted text-center">No labor tasks recorded.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h6 class="text-muted mb-2">Sublets</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Description</th><th>Supplier</th><th>Cost</th></tr>
                </thead>
                <tbody>
                    <?php if (!empty($sublets)): ?>
                        <?php foreach ($sublets as $sublet): ?>
                            <tr>
                                <td><?= esc($sublet['description'] ?? 'N/A') ?></td>
                                <td><?= esc($sublet['provider_name'] ?? 'N/A') ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($sublet['cost'] ?? 0, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-muted text-center">No sublets recorded.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-sm totals-table">
                        <tr><td>Parts Total</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['parts_total'], 2) ?></td></tr>
                        <tr><td>Labor Total</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['labor_total'], 2) ?></td></tr>
                        <tr><td>Sublets Total</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['sublet_total'], 2) ?></td></tr>
                        <tr><td>Subtotal</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['subtotal'], 2) ?></td></tr>
                        <tr><td>VAT (<?= number_format($invoice['vat_rate'], 2) ?>%)</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['vat_amount'], 2) ?></td></tr>
                        <tr><td>Discount</td><td class="text-end">- <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['discount'], 2) ?></td></tr>
                        <tr class="grand-total"><td>GRAND TOTAL</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['grand_total'], 2) ?></td></tr>
                        <tr><td>Amount Paid</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['amount_paid'], 2) ?></td></tr>
                        <tr class="balance-due"><td>BALANCE DUE</td><td class="text-end"><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($invoice['balance_due'], 2) ?></td></tr>
                    </table>
                </div>
            </div>

            <?php if (!empty($invoice['notes'])): ?>
                <div class="mt-3">
                    <h6 class="text-muted">Notes:</h6>
                    <p><?= nl2br(esc($invoice['notes'])) ?></p>
                </div>
            <?php endif; ?>

            <hr class="mt-4">
            <h5 class="mb-3">Payment History</h5>
            <?php if (!empty($payments)): ?>
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th><th>Received By</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= esc($payment['payment_date']) ?></td>
                                <td><?= esc($payment['payment_method']) ?></td>
                                <td><?= esc($payment['reference_no'] ?? '-') ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($payment['amount'], 2) ?></td>
                                <td><?= esc($payment['received_by_name'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No payments recorded yet.</p>
            <?php endif; ?>

            <?php if ($invoice['balance_due'] > 0 && $invoice['status'] !== 'Cancelled'): ?>
                <div class="no-print card mt-4">
                    <div class="card-header"><h5 class="mb-0">Record Payment</h5></div>
                    <div class="card-body">
                        <form method="POST" action="<?= base_url('admin/invoices/record_payment/' . $invoice['id']) ?>">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Amount <?= org_setting('currency_symbol', 'KSh') ?></label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="">-- Select --</option>
                                        <option value="Cash">Cash</option>
                                        <option value="M-Pesa">M-Pesa</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Insurance">Insurance</option>
                                        <option value="Credit">Credit</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Reference No</label>
                                    <input type="text" name="reference_no" class="form-control" placeholder="e.g. M-Pesa transaction ID">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes (optional)</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success"><i class="bi bi-cash"></i> Record Payment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
