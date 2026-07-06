<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3 class="mb-4"><i class="bi bi-file-earmark-text me-2"></i> <?= $action === 'add' ? 'New LPO' : 'Edit LPO' ?></h3>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url($action === 'add' ? 'admin/lpos/create' : 'admin/lpos/update/' . $lpo['id']) ?>">
        <?= csrf_field() ?>

        <div class="card mb-4">
            <div class="card-header"><strong>LPO Details</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Supplier --</option>
                            <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= isset($lpo) && $lpo['supplier_id'] == $s['id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="job_card_id" class="form-label">Link to Job Card (optional)</label>
                        <select class="form-select" id="job_card_id" name="job_card_id">
                            <option value="">-- None --</option>
                            <?php foreach ($job_cards as $jc): ?>
                            <option value="<?= $jc['id'] ?>" <?= isset($lpo) && $lpo['job_card_id'] == $jc['id'] ? 'selected' : '' ?>><?= esc($jc['job_no'] ?? ('Job #' . $jc['id'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="lpo_date" class="form-label">LPO Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="lpo_date" name="lpo_date" value="<?= isset($lpo) ? $lpo['lpo_date'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="expected_date" class="form-label">Expected Delivery Date</label>
                        <input type="date" class="form-control" id="expected_date" name="expected_date" value="<?= $lpo['expected_date'] ?? '' ?>">
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"><?= esc($lpo['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Line Items</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow"><i class="bi bi-plus-lg"></i> Add Item</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Part/Item</th>
                            <th>Unit</th>
                            <th>Qty Ordered</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($items) && !empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="items[][inventory_id]" value="<?= $item['inventory_id'] ?>">
                                    <input type="text" class="form-control item-search" data-inventory-id="<?= $item['inventory_id'] ?>" value="<?= esc(($item['name'] ?? '') . ' (' . ($item['part_number'] ?? '') . ')') ?>" required>
                                </td>
                                <td><input type="text" class="form-control item-unit" value="<?= esc($item['unit'] ?? 'piece') ?>" readonly></td>
                                <td><input type="number" class="form-control item-qty" name="items[][quantity_ordered]" value="<?= $item['quantity_ordered'] ?? 1 ?>" min="0.01" step="0.01" required></td>
                                <td><input type="number" step="0.01" class="form-control item-price" name="items[][unit_price]" value="<?= $item['unit_price'] ?? 0 ?>" min="0" required></td>
                                <td><input type="text" class="form-control item-line-total" value="<?= (($item['quantity_ordered'] ?? 1) * ($item['unit_price'] ?? 0)) ?>" readonly></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="items[][inventory_id]" value="">
                                    <input type="text" class="form-control item-search" data-inventory-id="" placeholder="Search inventory..." required>
                                </td>
                                <td><input type="text" class="form-control item-unit" value="" readonly></td>
                                <td><input type="number" class="form-control item-qty" name="items[][quantity_ordered]" value="1" min="0.01" step="0.01" required></td>
                                <td><input type="number" step="0.01" class="form-control item-price" name="items[][unit_price]" value="0" min="0" required></td>
                                <td><input type="text" class="form-control item-line-total" value="0" readonly></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Grand Total:</th>
                            <th><input type="text" class="form-control" id="grandTotal" value="0" readonly></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> <?= $action === 'add' ? 'Create LPO' : 'Update LPO' ?></button>
            <a href="<?= base_url('admin/lpos') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.search-inv-dropdown {
    position: absolute;
    z-index: 1000;
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    width: 100%;
}
.search-inv-dropdown .inv-item {
    padding: 6px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f2f5;
}
.search-inv-dropdown .inv-item:hover {
    background-color: #e9ecef;
}
.search-inv-dropdown .inv-item:last-child {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var inventoryData = <?= json_encode($inventory) ?>;

    function recalcRow(tr) {
        var qty = parseFloat(tr.querySelector('.item-qty').value) || 0;
        var price = parseFloat(tr.querySelector('.item-price').value) || 0;
        tr.querySelector('.item-line-total').value = (qty * price).toFixed(2);
        recalcGrandTotal();
    }

    function recalcGrandTotal() {
        var total = 0;
        document.querySelectorAll('.item-line-total').forEach(function(el) {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('grandTotal').value = total.toFixed(2);
    }

    document.getElementById('addItemRow').addEventListener('click', function() {
        var tbody = document.querySelector('#itemsTable tbody');
        var row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="hidden" name="items[][inventory_id]" value="">
                <input type="text" class="form-control item-search" data-inventory-id="" placeholder="Search inventory...">
            </td>
            <td><input type="text" class="form-control item-unit" value="" readonly></td>
            <td><input type="number" class="form-control item-qty" name="items[][quantity_ordered]" value="1" min="0.01" step="0.01" required></td>
            <td><input type="number" step="0.01" class="form-control item-price" name="items[][unit_price]" value="0" min="0" required></td>
            <td><input type="text" class="form-control item-line-total" value="0" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(row);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            var btn = e.target.classList.contains('remove-row') ? e.target : e.target.closest('.remove-row');
            btn.closest('tr').remove();
            recalcGrandTotal();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
            recalcRow(e.target.closest('tr'));
        }
    });

    var searchTimeout;
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-search')) {
            clearTimeout(searchTimeout);
            var input = e.target;
            var query = input.value.trim();

            var existing = input.closest('td').querySelector('.search-inv-dropdown');
            if (existing) existing.remove();

            if (query.length < 1) return;

            searchTimeout = setTimeout(function() {
                var td = input.closest('td');
                td.style.position = 'relative';
                var dropdown = document.createElement('div');
                dropdown.className = 'search-inv-dropdown';

                var filtered = inventoryData.filter(function(p) {
                    return (p.name && p.name.toLowerCase().indexOf(query.toLowerCase()) !== -1) ||
                           (p.part_number && p.part_number.toLowerCase().indexOf(query.toLowerCase()) !== -1);
                });

                if (filtered.length === 0) {
                    dropdown.innerHTML = '<div class="inv-item text-muted">No matches</div>';
                } else {
                    filtered.forEach(function(part) {
                        var item = document.createElement('div');
                        item.className = 'inv-item';

                        var stockBadge = '';
                        if (part.is_stocked == 0) {
                            stockBadge = '<span class="badge bg-secondary ms-1">Catalog Only</span>';
                        } else {
                            var qty = parseFloat(part.quantity_in_hand || 0);
                            var reorder = parseFloat(part.reorder_level || 0);
                            if (qty <= 0) {
                                stockBadge = '<span class="badge bg-danger ms-1">Out of Stock</span>';
                            } else if (qty <= reorder) {
                                stockBadge = '<span class="badge bg-warning text-dark ms-1">Low Stock (' + qty + ')</span>';
                            } else {
                                stockBadge = '<span class="badge bg-success ms-1">In Stock (' + qty + ')</span>';
                            }
                        }

                        item.innerHTML = part.name + ' (' + (part.part_number || 'N/A') + ') ' + stockBadge;
                        item.dataset.id = part.id;
                        item.dataset.name = part.name;
                        item.dataset.partNumber = part.part_number || '';
                        item.dataset.unitPrice = part.unit_price || 0;
                        item.dataset.unit = part.unit || 'piece';

                        item.addEventListener('click', function() {
                            var tr = input.closest('tr');
                            tr.querySelector('.item-search').value = this.dataset.name + ' (' + this.dataset.partNumber + ')';
                            tr.querySelector('.item-search').dataset.inventoryId = this.dataset.id;
                            tr.querySelector('input[name$="[inventory_id]"]').value = this.dataset.id;
                            tr.querySelector('.item-unit').value = this.dataset.unit;
                            tr.querySelector('.item-price').value = parseFloat(this.dataset.unitPrice).toFixed(2);
                            recalcRow(tr);
                            dropdown.remove();
                        });

                        dropdown.appendChild(item);
                    });
                }
                td.appendChild(dropdown);
            }, 300);
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('item-search')) {
            document.querySelectorAll('.search-inv-dropdown').forEach(function(el) { el.remove(); });
        }
    });
});
</script>
<?= $this->endSection() ?>
