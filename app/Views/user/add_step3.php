<!DOCTYPE html>
<html>
<head>
    <title>Add New User - Step 3: Next of Kin Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5>Add New User - Step 3: Next of Kin Information</h5>
        </div>
        <div class="card-body">
            <form id="step3Form" method="POST">
                <?= csrf_field() ?>
                <!-- First Name -->
                <div class="mb-3">
                    <label for="kin_first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="kin_first_name" name="kin_first_name" required>
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="kin_last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="kin_last_name" name="kin_last_name" required>
                </div>

                <!-- Relationship -->
                <div class="mb-3">
                    <label for="relationship" class="form-label">Relationship</label>
                    <input type="text" class="form-control" id="relationship" name="relationship" required>
                </div>

                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="kin_phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="kin_phone_number" name="kin_phone_number" required>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="button" onclick="goBackToStep2()" class="btn btn-secondary">Previous</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function goBackToStep2() {
        window.location.href = "<?= base_url('user/add_step2') ?>";
    }

    // AJAX Submission for Step 3
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
                alert('Something went wrooong!');
            }
        },
     
    });
});

</script>

</body>
</html>
