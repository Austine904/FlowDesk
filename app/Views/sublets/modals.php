<div id="actionModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="actionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h5 class="text-lg font-semibold text-gray-900" id="actionModalLabel"></h5>
            <button type="button" onclick="closeModal('actionModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6">
            <div id="modalContent" class="flex justify-center items-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        </div>
    </div>
</div>

<div id="subletDetailsModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="subletDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h5 class="text-lg font-semibold text-gray-900">
                <i class="bi bi-info-circle me-2"></i> Sublet Details
            </h5>
            <button type="button" onclick="closeModal('subletDetailsModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6" id="subletDetailsBody">
            <div class="flex justify-center items-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button type="button" onclick="closeModal('subletDetailsModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Close</button>
        </div>
    </div>
</div>

<div id="confirmDeleteModal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
<div id="confirmDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h5 class="text-lg font-semibold text-gray-900"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion</h5>
            <button type="button" onclick="closeModal('confirmDeleteModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6 text-center text-sm text-gray-600">
            Are you sure you want to delete the selected sublet(s)? This action cannot be undone.
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-center gap-2">
            <button type="button" onclick="closeModal('confirmDeleteModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</button>
            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<script>
if (typeof window.closeModal !== 'function') {
    window.closeModal = function(name) {
        document.getElementById(name).classList.add('hidden');
        var backdrop = document.getElementById(name + '-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };
}

if (typeof window.openModal !== 'function') {
    window.openModal = function(url, title) {
        var label = document.getElementById('actionModalLabel');
        if (label && title) label.textContent = title;
        var content = document.getElementById('modalContent');
        if (content) {
            content.innerHTML = '<div class="flex justify-center items-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';
        }
        document.getElementById('actionModal').classList.remove('hidden');
        document.getElementById('actionModal-backdrop').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Network response was not ok ' + response.statusText);
            return response.text();
        })
        .then(function(data) {
            if (content) content.innerHTML = data;
        })
        .catch(function(error) {
            if (label) label.textContent = 'Error';
            if (content) content.innerHTML = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Error loading content: ' + error.message + '. Please try again.</div>';
            console.error('Error loading modal content:', error);
        });
    };
}
</script>
