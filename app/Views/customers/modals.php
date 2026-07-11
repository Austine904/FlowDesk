<!-- Customer Details Modal -->
<div id="customerDetailsModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('customerDetailsModal')"></div>
<div id="customerDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="customerDetailsModalLabel">
                <i class="bi bi-person-vcard mr-2"></i> Customer Details
            </h5>
            <button type="button" onclick="closeModal('customerDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-3 text-center">
                    <img id="customer-profile-picture" src="https://placehold.co/100x100/cccccc/333333?text=CS" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover" alt="Customer Photo">
                    <h5 class="text-base font-semibold text-gray-900 customer-name-heading" id="customer-fullname-modal"></h5>
                    <div class="mt-3 space-y-1 text-sm text-gray-600">
                        <p><i class="bi bi-phone"></i> <span id="customer-phone-modal"></span></p>
                        <p><i class="bi bi-envelope"></i> <span id="customer-email-modal"></span></p>
                        <p><i class="bi bi-house"></i> <span id="customer-address-modal"></span></p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <span class="text-xs font-medium text-gray-500">Member Since:</span>
                        <span class="text-sm text-gray-900 block" id="customer-created-at"></span>
                    </div>
                </div>

                <div class="md:col-span-9">
                    <div class="flex border-b border-gray-200 mb-4">
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px text-indigo-600 border-indigo-600" onclick="switchTab('customer-overview', this)" type="button">Overview</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300" onclick="switchTab('customer-vehicles', this)" type="button">Vehicles Owned</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300" onclick="switchTab('customer-jobs', this)" type="button">Job History</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300" onclick="switchTab('customer-invoices', this)" type="button">Invoices</button>
                        <button class="tab-btn px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300" onclick="switchTab('customer-communication', this)" type="button">Comms</button>
                    </div>

                    <div>
                        <div id="customer-overview" class="tab-panel block">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Customer Contact & Basic Info</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-2"><span class="text-xs font-medium text-gray-500">Full Name:</span> <span class="text-sm text-gray-900" id="overview_fullname"></span></div>
                                    <div class="mb-2"><span class="text-xs font-medium text-gray-500">Phone:</span> <span class="text-sm text-gray-900" id="overview_phone"></span></div>
                                    <div class="mb-2"><span class="text-xs font-medium text-gray-500">Email:</span> <span class="text-sm text-gray-900" id="overview_email"></span></div>
                                </div>
                                <div>
                                    <div class="mb-2"><span class="text-xs font-medium text-gray-500">Address:</span> <span class="text-sm text-gray-900" id="overview_address"></span></div>
                                    <div class="mb-2"><span class="text-xs font-medium text-gray-500">Account ID:</span> <span class="text-sm text-gray-900" id="overview_id"></span></div>
                                </div>
                            </div>
                        </div>

                        <div id="customer-vehicles" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Registered Vehicles</h6>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Reg No.</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Make</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Model</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Year</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">VIN</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Mileage</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Problem</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer-vehicles-list">
                                        <tr>
                                            <td colspan="7" class="px-3 py-2 text-sm text-gray-500 text-center">Loading vehicles...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p id="no-vehicles-message" class="text-sm text-gray-500 text-center mt-2 hidden">No vehicles registered for this customer.</p>
                        </div>

                        <div id="customer-jobs" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Job History</h6>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Job No.</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Vehicle Reg No.</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Date In</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Status</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Problem</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer-jobs-list">
                                        <tr>
                                            <td colspan="5" class="px-3 py-2 text-sm text-gray-500 text-center">No job history available for this customer.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="customer-invoices" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Invoices & Payments</h6>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Invoice No.</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Date</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Amount</th>
                                            <th class="bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider px-3 py-2 text-left">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer-invoices-list">
                                        <tr>
                                            <td colspan="4" class="px-3 py-2 text-sm text-gray-500 text-center">No invoices available for this customer.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="customer-communication" class="tab-panel hidden">
                            <h6 class="text-sm font-semibold text-gray-500 mb-3">Communication Log</h6>
                            <div class="divide-y divide-gray-200 rounded-lg border border-gray-200">
                                <div class="px-4 py-3 flex items-start gap-3">
                                    <i class="bi bi-chat-dots-fill text-indigo-600 mt-1"></i>
                                    <div>
                                        <small class="text-gray-500">2025-06-01 10:30 AM (Receptionist)</small>
                                        <p class="text-sm text-gray-700">Call received regarding job status of ABC 123. Informed customer of parts delay.</p>
                                    </div>
                                </div>
                                <div class="px-4 py-3 flex items-start gap-3">
                                    <i class="bi bi-envelope-fill text-emerald-600 mt-1"></i>
                                    <div>
                                        <small class="text-gray-500">2025-05-28 09:00 AM (System)</small>
                                        <p class="text-sm text-gray-700">Automated email sent: Job Card #XYZ-456 created.</p>
                                    </div>
                                </div>
                                <div id="customer-communication-list">
                                    <div class="text-center text-gray-500 py-3 text-sm">No further communication entries.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('customerDetailsModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <i class="bi bi-x-circle"></i> Close
            </button>
            <button type="button" onclick="openModal('<?= base_url('admin/customers/edit/') ?>' + document.getElementById('overview_id').innerText, 'Edit Customer Details')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit Customer
            </button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmDeleteModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeModal('confirmDeleteModal')"></div>
<div id="confirmDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-900" id="confirmDeleteModalLabel"><i class="bi bi-exclamation-triangle mr-2"></i> Confirm Deletion</h5>
            <button type="button" onclick="closeModal('confirmDeleteModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 text-center">
            <p class="text-sm text-gray-600">Are you sure you want to delete the selected customer(s)? This action cannot be undone.</p>
        </div>
        <div class="flex items-center justify-center gap-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="closeModal('confirmDeleteModal')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            <button type="button" id="confirmDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Delete</button>
        </div>
    </div>
</div>

<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.add('hidden'); p.classList.remove('block'); });
    document.querySelectorAll('.tab-btn').forEach(function(b) {
        b.classList.remove('text-indigo-600', 'border-indigo-600');
        b.classList.add('text-gray-500', 'border-transparent');
    });
    var panel = document.getElementById(tabId);
    panel.classList.remove('hidden');
    panel.classList.add('block');
    btn.classList.add('text-indigo-600', 'border-indigo-600');
    btn.classList.remove('text-gray-500', 'border-transparent');
}

if (typeof window.openModal !== 'function') {
    window.openModal = function(url, title) {
        var modal = document.getElementById('actionModal');
        var backdrop = document.getElementById('actionModal-backdrop');
        var modalTitle = document.getElementById('actionModalLabel');
        var modalContent = document.getElementById('modalContent');

        modalTitle.textContent = title;
        modalContent.innerHTML = '<div class="text-center py-5"><div class="inline-block w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin" role="status"><span class="sr-only">Loading...</span></div></div>';

        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Network response was not ok ' + response.statusText);
            return response.text();
        })
        .then(function(data) {
            modalContent.innerHTML = data;
        })
        .catch(function(error) {
            modalTitle.textContent = 'Error';
            modalContent.innerHTML = '<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm" role="alert">Error loading content: ' + error.message + '. Please try again.</div>';
            console.error('Error loading modal content:', error);
        });
    };
}

if (typeof window.closeModal !== 'function') {
    window.closeModal = function(id) {
        document.getElementById(id).classList.add('hidden');
        var backdrop = document.getElementById(id + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };
}
</script>


