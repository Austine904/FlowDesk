<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Last User ID — FlowDesk</title>
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 w-full max-w-md p-8 text-center">
        <h1 class="text-lg font-bold text-gray-900 mb-4">Get Last User ID</h1>
        <p class="text-sm text-gray-500 mb-4">Last User ID: <span id="lastIdDisplay" class="font-medium text-gray-900">Not Fetched</span></p>
        <button id="fetchLastId"
                class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">
            Get Last User ID
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#fetchLastId').click(function() {
                $.ajax({
                    url: "<?= base_url('user/getLastId') ?>",
                    type: 'GET',
                    success: function(response) {
                        $('#lastIdDisplay').text(response.id);
                    },
                    error: function() {
                        alert('Failed to fetch last user ID.');
                    }
                });
            });
        });
    </script>
</body>
</html>
