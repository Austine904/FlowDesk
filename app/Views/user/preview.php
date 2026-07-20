<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Preview — FlowDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center font-sans p-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 w-full max-w-lg p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Preview User Details</h1>
                <p class="text-sm text-gray-500">Review before saving</p>
            </div>
        </div>

        <!-- Company Information -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Company Information</h2>
                <button onclick="editStep(1)" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Edit</button>
            </div>
            <div class="flex items-center gap-4 mb-3">
                <img src="<?= session()->get('step1_data')['profile_picture'] ? base_url(session()->get('step1_data')['profile_picture']) : base_url('uploads/default.png') ?>"
                     alt="Profile" class="w-20 h-20 rounded-lg object-cover border border-gray-200">
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Role</dt><dd class="text-gray-900 font-medium"><?= session('step1_data.role') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Company ID</dt><dd class="text-gray-900 font-medium"><?= session('step1_data.company_id') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Date of Employment</dt><dd class="text-gray-900 font-medium"><?= session('step1_data.date_of_employment') ?></dd></div>
            </dl>
        </div>

        <hr class="border-gray-200 mb-6">

        <!-- Personal Information -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Personal Information</h2>
                <button onclick="editStep(2)" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Edit</button>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">First Name</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.first_name') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Last Name</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.last_name') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Date of Birth</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.dob') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">National ID</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.national_id') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Gender</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.gender') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Phone Number</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.phone_number') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Home Address</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.address') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-900 font-medium"><?= session('step2_data.email') ?></dd></div>
            </dl>
        </div>

        <hr class="border-gray-200 mb-6">

        <!-- Next of Kin -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Next of Kin</h2>
                <button onclick="editStep(3)" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">Edit</button>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">First Name</dt><dd class="text-gray-900 font-medium"><?= session('step3_data.kin_first_name') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Last Name</dt><dd class="text-gray-900 font-medium"><?= session('step3_data.kin_last_name') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Relationship</dt><dd class="text-gray-900 font-medium"><?= session('step3_data.relationship') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd class="text-gray-900 font-medium"><?= session('step3_data.kin_phone_number') ?></dd></div>
            </dl>
        </div>

        <div class="flex justify-between pt-4 border-t border-gray-200">
            <button type="button" onclick="goBackToStep3()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                Previous
            </button>
            <button type="button" onclick="finalSubmit()"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                Confirm &amp; Save
            </button>
        </div>
    </div>

    <script>
        function goBackToStep3() {
            window.location.href = "<?= base_url('user/add_step3') ?>";
        }

        function finalSubmit() {
            if (confirm('Are you sure you want to save this user?')) {
                window.location.href = "<?= base_url('user/saveUser') ?>";
            }
        }

        function editStep(step) {
            const urls = {
                1: "<?= base_url('user/add_step1') ?>",
                2: "<?= base_url('user/add_step2') ?>",
                3: "<?= base_url('user/add_step3') ?>"
            };
            window.location.href = urls[step];
        }
    </script>
</body>
</html>
