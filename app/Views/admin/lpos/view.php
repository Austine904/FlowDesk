<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> LPO: <?= esc($lpo['lpo_no']) ?></h3>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Print</button>
            <a href="<?= base_url('admin/lpos') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>LPO Details</strong>
            <span class="badge fs-6
                <?php
                $badgeMap = ['Draft'=>'bg-secondary','Sent'=>'bg-primary','Partially Received'=>'bg-warning text-dark','Received'=>'bg-success','Cancelled'=>'bg-dark'];
                echo $badgeMap[$lpo['status']] ?? 'bg-secondary';
                ?>"><?= esc($lpo['status']) ?></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Supplier:</strong> <?= esc($lpo['supplier_name'] ?? 'N/A') ?></div>
                <div class="col-md-4"><strong>Job Ref:</strong> <?= esc($lpo['job_no'] ?? 'N/A') ?></div>
                <div class="col-md-4"><strong>Raised By:</strong> <?= esc(($lpo['raised_by_first_name'] ?? '') . ' ' . ($lpo['raised_by_last_name'] ?? '')) ?></div>
                <div class="col-md-4 mt-2"><strong>LPO Date:</strong> <?= esc($lpo['lpo_date']) ?></div>
                <div class="col-md-4 mt-2"><strong>Expected Delivery:</strong> <?= esc($lpo['expected_date'] ?? 'Not set') ?></div>
                <div class="col-md-4 mt-2"><strong>Total Amount:</strong> <?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></div>
            </div>
            <?php if (!empty($lpo['notes'])): ?>
            <div class="row mt-3">
                <div class="col-12"><strong>Notes:</strong><br><?= nl2br(esc($lpo['notes'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Line Items</strong></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Part No.</th>
                        <th>Unit</th>
                        <th>Qty Ordered</th>
                        <th>Qty Received</th>
                        <th>Unit Price</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $idx = 1; ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= $idx++ ?></td>
                        <td><?= esc($item['name'] ?? 'N/A') ?></td>
                        <td><?= esc($item['part_number'] ?? '—') ?></td>
                        <td><?= esc($item['unit'] ?? 'piece') ?></td>
                        <td><?= number_format($item['quantity_ordered'], 2) ?></td>
                        <td><?= number_format($item['quantity_received'] ?? 0, 2) ?></td>
                        <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($item['unit_price'], 2) ?></td>
                        <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format(($item['quantity_ordered'] * $item['unit_price']), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="7" class="text-end">Grand Total:</td>
                        <td><?= org_setting('currency_symbol', 'KSh') ?> <?= number_format($lpo['total_amount'] ?? 0, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($lpo['status'] === 'Draft'): ?>
        <button class="btn btn-primary btn-status-update" data-new-status="Sent"><i class="bi bi-send"></i> Send LPO</button>
        <?php endif; ?>
        <?php if (in_array($lpo['status'], ['Sent', 'Partially Received'])): ?>
        <a href="<?= base_url('admin/lpos/receive/' . $lpo['id']) ?>" class="btn btn-success"><i class="bi bi-box-seam"></i> Receive Items</a>
        <?php endif; ?>
        <?php if ($lpo['status'] === 'Draft'): ?>
        <a href="<?= base_url('admin/lpos/edit/' . $lpo['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
        <button class="btn btn-outline-danger btn-status-update" data-new-status="Cancelled"><i class="bi bi-x-circle"></i> Cancel LPO</button>
        <?php endif; ?>
        <?php if ($lpo['status'] === 'Sent'): ?>
        <button class="btn btn-outline-danger btn-status-update" data-new-status="Cancelled"><i class="bi bi-x-circle"></i> Cancel LPO</button>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).on('click', '.btn-status-update', function() {
        var btn = $(this);
        var newStatus = btn.data('new-status');
        var id = <?= $lpo['id'] ?>;
        var csrf = getCsrfMeta();

        if (!confirm('Change LPO status to "' + newStatus + '"?')) return;

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '<?= base_url('admin/lpos/update_status/') ?>' + id,
            type: 'POST',
            data: { new_status: newStatus, [csrf.name]: csrf.hash },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.message || 'Failed to update status.');
                }
            },
            error: function() {
                alert('Error updating status.');
            },
            complete: function() {
                btn.prop('disabled', false).text(btn.data('original-text') || 'Update');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
