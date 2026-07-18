<form action="<?= base_url('admin/customers/store') ?>" method="POST" class="space-y-5">
<?= csrf_field() ?>

<div class="flex items-center gap-3 pb-2 border-b border-gray-100">
    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
    </div>
    <div>
        <h4 class="text-sm font-semibold text-gray-900">Customer Information</h4>
        <p class="text-xs text-gray-400">Add the customer's contact details</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="space-y-1.5">
        <label for="name" class="block text-xs font-medium text-gray-600">Full Name <span class="text-red-500">*</span></label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <input type="text" name="name" id="name" required placeholder="John Doe"
                   class="w-full rounded-lg border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 hover:bg-white hover:border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
        </div>
    </div>
    <div class="space-y-1.5">
        <label for="phone" class="block text-xs font-medium text-gray-600">Phone <span class="text-red-500">*</span></label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>
            <input type="text" name="phone" id="phone" required placeholder="0712 345 678"
                   class="w-full rounded-lg border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 hover:bg-white hover:border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
        </div>
        <p class="text-xs text-gray-400 pl-1">e.g. 0712 345 678</p>
    </div>
</div>

<div class="space-y-1.5">
    <label for="email" class="block text-xs font-medium text-gray-600">Email <span class="text-gray-400 font-normal">(optional)</span></label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <input type="email" name="email" id="email" placeholder="john@example.com"
               class="w-full rounded-lg border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 hover:bg-white hover:border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
    </div>
</div>

<div class="space-y-1.5">
    <label for="address" class="block text-xs font-medium text-gray-600">Address <span class="text-gray-400 font-normal">(optional)</span></label>
    <div class="relative">
        <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <textarea name="address" id="address" rows="2" placeholder="123 Main Street, Nairobi"
                  class="w-full rounded-lg border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 hover:bg-white hover:border-gray-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors resize-none"></textarea>
    </div>
</div>

<div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
    <button type="button" onclick="closeModal('actionModal')"
            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors">
        Cancel
    </button>
    <button type="submit"
            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors inline-flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Save Customer
    </button>
</div>
</form>
