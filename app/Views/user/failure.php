<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Creation Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger text-center">
            <i class="bi bi-exclamation-triangle fs-3"></i>
            <h4 class="alert-heading mt-2">Something went wrong.</h4>
            <p class="mb-0">User could not be created.</p>
        </div>
        <div class="text-center">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-primary">Back to Users</a>
        </div>
    </div>
</body>
</html>
