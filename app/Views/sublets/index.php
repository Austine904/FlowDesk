<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $pageTitle = 'Sublets'; ?>

<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-900">Sublets List</h4>
            <div class="flex gap-2">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2" onclick="openModal('addSubletModal')">
                    <i class="bi bi-plus-circle"></i> Add Sublet
                </button>
            </div>
        </div>
        <div class="p-6">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-4"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/sublets/bulkAction') ?>" id="bulkActionForm">
                <?= csrf_field() ?>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full divide-y divide-gray-200" id="subletTable" style="width:100%">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><input type="checkbox" id="select_all"></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Sent</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Returned</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
                <div id="pagination-links-container"></div>
            </form>
        </div>
    </div>
</div>

<!-- Add Sublet Modal -->
<div id="addSubletModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('addSubletModal')"></div>
<div id="addSubletModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Add New Sublet</h5>
            <button type="button" onclick="closeModal('addSubletModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addSubletForm">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="">
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Card <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" name="job_card_id" required>
                            <option value="">Select Job Card</option>
                            <?php if (isset($job_cards)): ?>
                                <?php foreach ($job_cards as $jc): ?>
                                    <option value="<?= esc($jc['id']) ?>"><?= esc($jc['job_no'] ?? 'N/A') ?> (<?= esc($jc['registration_number'] ?? 'N/A') ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sublet Provider <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" name="sublet_provider_id" required>
                            <option value="">Select Provider</option>
                            <?php if (isset($sublet_providers)): ?>
                                <?php foreach ($sublet_providers as $p): ?>
                                    <option value="<?= esc($p['id']) ?>"><?= esc($p['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="description" rows="3" required></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="cost" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Invoiced">Invoiced</option>
                            <option value="Paid">Paid</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Sent <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="date_sent" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Returned</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="date_returned">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" name="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Add Sublet</button>
                <button type="button" onclick="closeModal('addSubletModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Sublet Modal -->
<div id="editSubletModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('editSubletModal')"></div>
<div id="editSubletModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900">Edit Sublet</h5>
            <button type="button" onclick="closeModal('editSubletModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editSubletForm">
            <?= csrf_field() ?>
            <input type="hidden" id="edit_sublet_id" name="id" value="">
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Card <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="edit_sublet_job_card_id" name="job_card_id" required>
                            <option value="">Select Job Card</option>
                            <?php if (isset($job_cards)): ?>
                                <?php foreach ($job_cards as $jc): ?>
                                    <option value="<?= esc($jc['id']) ?>"><?= esc($jc['job_no'] ?? 'N/A') ?> (<?= esc($jc['registration_number'] ?? 'N/A') ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sublet Provider <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="edit_sublet_provider_id" name="sublet_provider_id" required>
                            <option value="">Select Provider</option>
                            <?php if (isset($sublet_providers)): ?>
                                <?php foreach ($sublet_providers as $p): ?>
                                    <option value="<?= esc($p['id']) ?>"><?= esc($p['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_sublet_description" name="description" rows="3" required></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_sublet_cost" name="cost" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none bg-white" id="edit_sublet_status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Invoiced">Invoiced</option>
                            <option value="Paid">Paid</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Sent <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_sublet_date_sent" name="date_sent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Returned</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_sublet_date_returned" name="date_returned">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none" id="edit_sublet_notes" name="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2"><i class="bi bi-save"></i> Update Sublet</button>
                <button type="button" onclick="closeModal('editSubletModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include('modals.php'); ?>
<?= $this->endSection() ?>
