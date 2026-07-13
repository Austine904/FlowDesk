<?php
$sublet = $sublet ?? [];
$safe = function($value) {
    return esc($value) ?? 'N/A';
};
$getStatusBadgeClass = function($status) {
    switch ($status) {
        case 'Pending': return 'bg-amber-100 text-amber-700';
        case 'In Progress': return 'bg-blue-100 text-blue-700';
        case 'Completed': return 'bg-emerald-100 text-emerald-700';
        case 'Invoiced': return 'bg-blue-100 text-blue-700';
        case 'Paid': return 'bg-emerald-100 text-emerald-700';
        case 'Cancelled': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};
?>
<dl class="grid grid-cols-1 gap-3">
    <div><dt class="text-xs font-medium text-gray-500">Sublet ID</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['id']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Job No</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['job_no']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Vehicle Reg</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['registration_number']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Provider</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['provider_name']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Cost</dt><dd class="text-sm text-gray-900">KES <?= number_format($sublet['cost'] ?? 0, 2) ?></dd></div>
    <div>
        <dt class="text-xs font-medium text-gray-500">Status</dt>
        <dd class="text-sm"><span class="text-xs font-medium px-2.5 py-0.5 rounded-full <?= $getStatusBadgeClass($sublet['status'] ?? '') ?>"><?= $safe($sublet['status']) ?></span></dd>
    </div>
    <div><dt class="text-xs font-medium text-gray-500">Date Sent</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['date_sent']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Date Returned</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['date_returned']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Description</dt><dd class="text-sm text-gray-900"><?= nl2br($safe($sublet['description'])) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Notes</dt><dd class="text-sm text-gray-900"><?= nl2br($safe($sublet['notes'])) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Created At</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['created_at']) ?></dd></div>
    <div><dt class="text-xs font-medium text-gray-500">Last Updated</dt><dd class="text-sm text-gray-900"><?= $safe($sublet['updated_at']) ?></dd></div>
</dl>
