<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><?= $action === 'add' ? 'New LPO' : 'Edit LPO' ?></h1>
    </div>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside text-sm"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url($action === 'add' ? 'admin/lpos/create' : 'admin/lpos/update/' . $lpo['id']) ?>">
        <?= csrf_field() ?>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">LPO Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Supplier --</option>
                            <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= isset($lpo) && $lpo['supplier_id'] == $s['id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="job_card_id" class="block text-sm font-medium text-gray-700 mb-1">Link to Job Card (optional)</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="job_card_id" name="job_card_id">
                            <option value="">-- None --</option>
                            <?php foreach ($job_cards as $jc): ?>
                            <option value="<?= $jc['id'] ?>" <?= isset($lpo) && $lpo['job_card_id'] == $jc['id'] ? 'selected' : '' ?>><?= esc($jc['job_no'] ?? ('Job #' . $jc['id'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="lpo_date" class="block text-sm font-medium text-gray-700 mb-1">LPO Date <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="lpo_date" name="lpo_date" value="<?= isset($lpo) ? $lpo['lpo_date'] : date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label for="expected_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="expected_date" name="expected_date" value="<?= $lpo['expected_date'] ?? '' ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="notes" name="notes" rows="2"><?= esc($lpo['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Line Items</h2>
                <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5" id="addItemRow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="itemsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part/Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Ordered</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (isset($items) && !empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="px-4 py-3 relative">
                                    <input type="hidden" name="items[][inventory_id]" value="<?= $item['inventory_id'] ?>">
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-search" data-inventory-id="<?= $item['inventory_id'] ?>" value="<?= esc(($item['name'] ?? '') . ' (' . ($item['part_number'] ?? '') . ')') ?>" required>
                                </td>
                                <td class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 item-unit" value="<?= esc($item['unit'] ?? 'piece') ?>" readonly></td>
                                <td class="px-4 py-3"><input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-qty" name="items[][quantity_ordered]" value="<?= $item['quantity_ordered'] ?? 1 ?>" min="0.01" step="0.01" required></td>
                                <td class="px-4 py-3"><input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-price" name="items[][unit_price]" value="<?= $item['unit_price'] ?? 0 ?>" min="0" required></td>
                                <td class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 item-line-total" value="<?= (($item['quantity_ordered'] ?? 1) * ($item['unit_price'] ?? 0)) ?>" readonly></td>
                                <td class="px-4 py-3"><button type="button" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-lg text-xs font-medium transition-colors remove-row"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="px-4 py-3 relative">
                                    <input type="hidden" name="items[][inventory_id]" value="">
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-search" data-inventory-id="" placeholder="Search inventory..." required>
                                </td>
                                <td class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 item-unit" value="" readonly></td>
                                <td class="px-4 py-3"><input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-qty" name="items[][quantity_ordered]" value="1" min="0.01" step="0.01" required></td>
                                <td class="px-4 py-3"><input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-price" name="items[][unit_price]" value="0" min="0" required></td>
                                <td class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 item-line-total" value="0" readonly></td>
                                <td class="px-4 py-3"><button type="button" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-lg text-xs font-medium transition-colors remove-row"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Grand Total:</th>
                            <th class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700 font-bold" id="grandTotal" value="0" readonly></th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <?= $action === 'add' ? 'Create LPO' : 'Update LPO' ?>
            </button>
            <a href="<?= base_url('admin/lpos') ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
.search-inv-dropdown {
    position: absolute;
    z-index: 50;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    max-height: 12rem;
    overflow-y: auto;
    width: 100%;
}
.search-inv-dropdown .inv-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.875rem;
    border-bottom: 1px solid #f3f4f6;
}
.search-inv-dropdown .inv-item:hover {
    background-color: #eef2ff;
    color: #4338ca;
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
            <td class="px-4 py-3 relative">
                <input type="hidden" name="items[][inventory_id]" value="">
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-search" data-inventory-id="" placeholder="Search inventory...">
            </td>
            <td class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 item-unit" value="" readonly></td>
            <td class="px-4 py-3"><input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-qty" name="items[][quantity_ordered]" value="1" min="0.01" step="0.01" required></td>
            <td class="px-4 py-3"><input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none item-price" name="items[][unit_price]" value="0" min="0" required></td>
            <td class="px-4 py-3"><input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 item-line-total" value="0" readonly></td>
            <td class="px-4 py-3"><button type="button" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-lg text-xs font-medium transition-colors remove-row"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
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
                    dropdown.innerHTML = '<div class="inv-item text-gray-500">No matches</div>';
                } else {
                    filtered.forEach(function(part) {
                        var item = document.createElement('div');
                        item.className = 'inv-item';

                        var stockBadge = '';
                        if (part.is_stocked == 0) {
                            stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 ms-1">Catalog Only</span>';
                        } else {
                            var qty = parseFloat(part.quantity_in_hand || 0);
                            var reorder = parseFloat(part.reorder_level || 0);
                            if (qty <= 0) {
                                stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 ms-1">Out of Stock</span>';
                            } else if (qty <= reorder) {
                                stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 ms-1">Low Stock (' + qty + ')</span>';
                            } else {
                                stockBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 ms-1">In Stock (' + qty + ')</span>';
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
