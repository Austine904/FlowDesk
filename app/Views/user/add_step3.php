<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Step 3 — FlowDesk</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New User</h1>
                <p class="text-sm text-gray-500">Step 3 of 3 — Next of Kin</p>
            </div>
        </div>

        <form id="step3Form" method="POST">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div>
                    <label for="kin_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" id="kin_first_name" name="kin_first_name" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="kin_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" id="kin_last_name" name="kin_last_name" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                    <input type="text" id="relationship" name="relationship" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="kin_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="kin_phone_number" name="kin_phone_number" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="goBackToStep2()"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Previous
                </button>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function goBackToStep2() {
            window.location.href = "<?= base_url('user/add_step2') ?>";
        }

        $('#step3Form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('user/add_step3') ?>",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        window.location.href = "<?= base_url('user/preview') ?>";
                    } else {
                        alert('Something went wrong!');
                    }
                }
            });
        });
    </script>
</body>
</html>
