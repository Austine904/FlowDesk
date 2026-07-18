<div class="space-y-6">
    <div class="flex items-center gap-2 mb-4">
        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-semibold" id="stepIndicator">1</span>
        <div class="h-1.5 flex-1 rounded-full bg-gray-200">
            <div class="h-1.5 rounded-full bg-indigo-600 transition-all" id="progressBar" style="width: 33%"></div>
        </div>
        <span class="text-xs text-gray-400" id="stepLabel">Step 1 of 3</span>
    </div>

    <form id="addUserForm" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <!-- Step 1: Account -->
        <div id="step1" class="step-panel">
            <h5 class="text-base font-semibold text-gray-900 mb-4">Account Information</h5>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                    <input type="file" name="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="mechanic">Mechanic</option>
                        <option value="receptionist">Receptionist</option>
                    </select>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="6" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none pr-10">
                        <button type="button" id="passwordToggle" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company ID</label>
                    <input type="text" name="company_id" id="company_id" readonly class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Auto-generated based on role.</p>
                </div>
                <div>
                    <label for="date_of_employment" class="block text-sm font-medium text-gray-700 mb-1">Date of Employment</label>
                    <input type="text" name="date_of_employment" id="date_of_employment" readonly value="<?= date('Y-m-d') ?>" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" id="nextStep1" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Next Step</button>
            </div>
        </div>

        <!-- Step 2: Personal -->
        <div id="step2" class="step-panel hidden">
            <h5 class="text-base font-semibold text-gray-900 mb-4">Personal Information</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="dob" id="dob" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">National ID</label>
                    <input type="text" name="national_id" id="national_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <div class="flex gap-4 mt-1">
                        <label class="inline-flex items-center"><input type="radio" name="gender" value="Male" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> <span class="ml-2 text-sm text-gray-700">Male</span></label>
                        <label class="inline-flex items-center"><input type="radio" name="gender" value="Female" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> <span class="ml-2 text-sm text-gray-700">Female</span></label>
                        <label class="inline-flex items-center"><input type="radio" name="gender" value="Other" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> <span class="ml-2 text-sm text-gray-700">Other</span></label>
                    </div>
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" required pattern="[0-9]{10,15}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                    <input type="text" name="address" id="address" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-between mt-6">
                <button type="button" id="prevStep2" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Previous</button>
                <button type="button" id="nextStep2" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Next Step</button>
            </div>
        </div>

        <!-- Step 3: Next of Kin -->
        <div id="step3" class="step-panel hidden">
            <h5 class="text-base font-semibold text-gray-900 mb-4">Next of Kin</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kin_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="kin_first_name" id="kin_first_name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="kin_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="kin_last_name" id="kin_last_name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                    <input type="text" name="relationship" id="relationship" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="kin_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="kin_phone_number" id="kin_phone_number" required pattern="[0-9]{10,15}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-between mt-6">
                <button type="button" id="prevStep3" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Previous</button>
                <button type="submit" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4 hidden animate-spin" id="submitSpinner" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span id="submitText">Save User</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
(function() {
    var currentStep = 1;
    var form = document.getElementById('addUserForm');
    var progressBar = document.getElementById('progressBar');
    var stepIndicator = document.getElementById('stepIndicator');
    var stepLabel = document.getElementById('stepLabel');

    function showStep(step) {
        document.querySelectorAll('.step-panel').forEach(function(el) { el.classList.add('hidden'); });
        var panel = document.getElementById('step' + step);
        if (panel) panel.classList.remove('hidden');
        currentStep = step;
        progressBar.style.width = (step / 3 * 100) + '%';
        stepIndicator.textContent = step;
        stepLabel.textContent = 'Step ' + step + ' of 3';
    }

    function validateStep(stepId) {
        var panel = document.getElementById(stepId);
        var inputs = panel.querySelectorAll('[required]');
        var valid = true;
        inputs.forEach(function(input) {
            var isRadio = input.type === 'radio';
            if (isRadio) {
                var name = input.name;
                var checked = panel.querySelector('input[name="' + name + '"]:checked');
                if (!checked) valid = false;
                return;
            }
            if (!input.value || !input.checkValidity()) {
                input.classList.add('border-red-400', 'focus:ring-red-500');
                valid = false;
            } else {
                input.classList.remove('border-red-400', 'focus:ring-red-500');
            }
        });
        return valid;
    }

    function clearErrors(stepId) {
        document.querySelectorAll('#' + stepId + ' input, #' + stepId + ' select').forEach(function(el) {
            el.classList.remove('border-red-400', 'focus:ring-red-500');
        });
    }

    // Password toggle
    document.getElementById('passwordToggle').addEventListener('click', function() {
        var pw = document.getElementById('password');
        var type = pw.getAttribute('type') === 'password' ? 'text' : 'password';
        pw.setAttribute('type', type);
    });

    // Role → company ID generation
    document.getElementById('role').addEventListener('change', async function() {
        var role = this.value;
        if (!role) return;
        var companyIdInput = document.getElementById('company_id');
        companyIdInput.value = 'Generating...';
        var year = new Date().getFullYear().toString().slice(-2);
        var prefixes = { admin: 'ADM', mechanic: 'MECH', receptionist: 'RP' };
        try {
            var resp = await fetch(BASE_URL + '/user/getLastId?role=' + role, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            var data = await resp.json();
            var lastId = parseInt(data.result) || 0;
            var nextId = String(lastId + 1).padStart(3, '0');
            companyIdInput.value = (prefixes[role] || '') + year + nextId;
        } catch (e) {
            companyIdInput.value = 'Error generating ID';
        }
    });

    // Navigation
    document.getElementById('nextStep1').addEventListener('click', function() {
        if (validateStep('step1')) { clearErrors('step1'); showStep(2); }
    });
    document.getElementById('nextStep2').addEventListener('click', function() {
        if (validateStep('step2')) { clearErrors('step2'); showStep(3); }
    });
    document.getElementById('prevStep2').addEventListener('click', function() { clearErrors('step2'); showStep(1); });
    document.getElementById('prevStep3').addEventListener('click', function() { clearErrors('step3'); showStep(2); });

    // Submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!validateStep('step3')) return;

        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitSpinner').classList.remove('hidden');
        document.getElementById('submitText').textContent = 'Saving...';

        var formData = new FormData(form);
        try {
            var resp = await fetch(BASE_URL + '/user/final_submit', { method: 'POST', body: formData });
            var result = await resp.json();
            if (resp.ok && result.success) {
                Swal.fire('Success', 'User added successfully!', 'success').then(function() {
                    window.hideModal('actionModal');
                    if (window.userTable) window.userTable.ajax.reload();
                });
            } else {
                Swal.fire('Error', result.message || 'Failed to save user.', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Server error. Please try again.', 'error');
        } finally {
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitSpinner').classList.add('hidden');
            document.getElementById('submitText').textContent = 'Save User';
        }
    });

    showStep(1);
})();
</script>