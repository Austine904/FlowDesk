<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — FlowDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center font-sans p-4">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 w-full max-w-md p-8">

        <div class="flex justify-center mb-6">
            <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-center text-gray-900 mb-1">Forgot Password?</h1>
        <p class="text-sm text-center text-gray-500 mb-8">Enter your Company ID and we'll send you a reset link</p>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('forgot-password/send') ?>">
            <?= csrf_field() ?>

            <div class="space-y-4">
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company ID</label>
                    <input type="text" name="company_id" id="company_id" placeholder="Enter your Company ID"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           required autofocus>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2.5 text-sm font-medium transition-colors mt-6">
                Send Reset Link
            </button>

            <p class="text-center mt-4">
                <a href="<?= base_url('login') ?>" class="text-sm text-indigo-600 hover:text-indigo-700">Back to Login</a>
            </p>
        </form>
    </div>

</body>
</html>
