<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-box-seam me-2"></i> Receive Items — <?= esc($lpo['lpo_no']) ?></h3>
        <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><strong>LPO Details</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Supplier:</strong> <?= esc($lpo['supplier_name'] ?? 'N/A') ?></div>
                <div class="col-md-4"><strong>Date:</strong> <?= esc($lpo['lpo_date']) ?></div>
                <div class="col-md-4">
                    <strong>Status:</strong>
                    <span class="badge
                        <?php
                        $badgeMap = ['Draft'=>'bg-secondary','Sent'=>'bg-primary','Partially Received'=>'bg-warning text-dark','Received'=>'bg-success','Cancelled'=>'bg-dark'];
                        echo $badgeMap[$lpo['status']] ?? 'bg-secondary';
                        ?>"><?= esc($lpo['status']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= base_url('admin/lpos/process_receive/' . $lpo['id']) ?>">
        <?= csrf_field() ?>
        <div class="card">
            <div class="card-header"><strong>Items to Receive</strong></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty Ordered</th>
                            <th>Qty Received So Far</th>
                            <th>Qty Receiving Now</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <?php
                            $remaining = $item['quantity_ordered'] - ($item['quantity_received'] ?? 0);
                        ?>
                        <tr>
                            <td><?= esc($item['name'] ?? 'N/A') ?> (<?= esc($item['part_number'] ?? '—') ?>)</td>
                            <td><?= number_format($item['quantity_ordered'], 2) ?></td>
                            <td><?= number_format($item['quantity_received'] ?? 0, 2) ?></td>
                            <td>
                                <?php if ($remaining > 0): ?>
                                <input type="number" class="form-control" name="receive_qty[<?= $item['id'] ?>]" min="0" max="<?= $remaining ?>" step="0.01" value="0" style="max-width: 150px;">
                                <small class="text-muted">Max: <?= number_format($remaining, 2) ?></small>
                                <?php else: ?>
                                <span class="text-success">Fully Received</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Receive Items</button>
            <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
