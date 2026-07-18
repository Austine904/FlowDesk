<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Receive Items — <?= esc($lpo['lpo_no']) ?></h1>
        <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['supplier_name'] ?? 'N/A') ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Date</span>
                    <span class="text-sm text-gray-900"><?= esc($lpo['lpo_date']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Status</span>
                    <?php
                    $badgeMap = ['Draft'=>'bg-gray-100 text-gray-700','Sent'=>'bg-blue-100 text-blue-700','Partially Received'=>'bg-amber-100 text-amber-700','Received'=>'bg-emerald-100 text-emerald-700','Cancelled'=>'bg-red-100 text-red-700'];
                    $badgeCls = $badgeMap[$lpo['status']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeCls ?>"><?= esc($lpo['status']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= base_url('admin/lpos/process_receive/' . $lpo['id']) ?>">
        <?= csrf_field() ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Items to Receive</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Ordered</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Received So Far</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Receiving Now</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                        <?php
                            $remaining = $item['quantity_ordered'] - ($item['quantity_received'] ?? 0);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= esc($item['name'] ?? 'N/A') ?> (<?= esc($item['part_number'] ?? '—') ?>)</td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($item['quantity_ordered'], 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?= number_format($item['quantity_received'] ?? 0, 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <?php if ($remaining > 0): ?>
                                <div class="flex flex-col gap-1">
                                    <input type="number" class="w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="receive_qty[<?= $item['id'] ?>]" min="0" max="<?= $remaining ?>" step="0.01" value="0">
                                    <span class="text-xs text-gray-500">Max: <?= number_format($remaining, 2) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Fully Received</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Receive Items
            </button>
            <a href="<?= base_url('admin/lpos/view/' . $lpo['id']) ?>" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancel
            </a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
