<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Step 2 — FlowDesk</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New User</h1>
                <p class="text-sm text-gray-500">Step 2 of 3 — Personal Information</p>
            </div>
        </div>

        <form id="step2Form" method="POST" action="<?= base_url('user/add_step2') ?>">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" id="first_name" name="first_name" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">National ID Number</label>
                    <input type="text" id="national_id" name="national_id" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-2">Gender</span>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="Male" required
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"> Male
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="Female"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"> Female
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="Other"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"> Other
                        </label>
                    </div>
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                    <input type="text" id="address" name="address" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" onclick="loadStep1()"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Previous
                </button>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
                    Next Step
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadStep1() {
            window.location.href = "<?= base_url('user/add_step1') ?>";
        }

        $('#step2Form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('user/add_step2') ?>",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        window.location.href = "<?= base_url('user/add_step3') ?>";
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Failed to submit Step 2.');
                }
            });
        });
    </script>
</body>
</html>
