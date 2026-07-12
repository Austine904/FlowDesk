<form action="<?= base_url('admin/customers/store') ?>" method="POST">
<?= csrf_field() ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" required>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
</div>
<div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
</div>
<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-primary">Save Customer</button>
</div>
</form>
