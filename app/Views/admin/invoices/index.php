<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-receipt me-2"></i> Invoices</h3>
        <div>
            <a href="<?= base_url('admin/invoices/mark_overdue') ?>" class="btn btn-outline-warning me-2" onclick="return confirm('Mark overdue invoices?')">
                <i class="bi bi-clock-history"></i> Mark Overdue
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table id="invoicesTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Job No</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><?= esc($inv['invoice_no']) ?></td>
                                <td><?= esc($inv['customer_name'] ?? 'N/A') ?></td>
                                <td><?= esc($inv['job_no'] ?? 'N/A') ?></td>
                                <td><?= esc($inv['invoice_date']) ?></td>
                                <td><?= esc($inv['due_date']) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['grand_total'], 2) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['amount_paid'], 2) ?></td>
                                <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($inv['balance_due'], 2) ?></td>
                                <td>
                                    <?php
                                        $badgeMap = [
                                            'Draft' => 'bg-secondary',
                                            'Sent' => 'bg-primary',
                                            'Partially Paid' => 'bg-warning text-dark',
                                            'Paid' => 'bg-success',
                                            'Overdue' => 'bg-danger',
                                            'Cancelled' => 'bg-dark',
                                        ];
                                        $badgeClass = $badgeMap[$inv['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc($inv['status']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/invoices/view/' . $inv['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No invoices found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#invoicesTable').DataTable({
        order: [[3, 'desc']],
        pageLength: 25,
    });
});
</script>
<?= $this->endSection() ?>
