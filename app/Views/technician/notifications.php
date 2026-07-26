<?php
$helperPath = APPPATH . 'Helpers/activity_helper.php';
if (file_exists($helperPath)) {
    helper('activity');
}
?>
<?= $this->extend('technician/layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900"><i class="fas fa-bell mr-2"></i> Notifications</h3>
        <?php $unreadCount = array_reduce($notifications ?? [], fn($c, $n) => $c + (empty($n['is_read']) ? 1 : 0), 0); ?>
        <?php if ($unreadCount > 0): ?>
        <button id="markAllReadBtn" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <i class="fas fa-check-double"></i> Mark all read
        </button>
        <?php endif; ?>
    </div>
    <div class="divide-y divide-gray-100" id="notificationList">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $n): ?>
            <div class="px-6 py-4 flex items-start gap-4 notification-item <?= empty($n['is_read']) ? 'bg-indigo-50/50 border-l-4 border-indigo-500' : 'bg-white border-l-4 border-transparent' ?>" data-id="<?= $n['id'] ?>" data-related-type="<?= esc($n['related_type'] ?? '') ?>" data-related-id="<?= esc($n['related_id'] ?? '') ?>">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-900"><?= esc($n['title']) ?></span>
                        <?php if (empty($n['is_read'])): ?>
                        <span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0"></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-gray-600"><?= esc($n['message']) ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= function_exists('timeAgo') ? timeAgo($n['created_at']) : esc($n['created_at']) ?></p>
                </div>
                <?php if (empty($n['is_read'])): ?>
                <button class="mark-read-btn flex-shrink-0 text-gray-400 hover:text-indigo-600 transition-colors p-1" title="Mark as read">
                    <i class="fas fa-circle text-xs"></i>
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="px-6 py-10 text-center">
                <i class="fas fa-bell-slash text-gray-300 text-3xl mb-3"></i>
                <p class="text-sm text-gray-400">No notifications yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark individual notification as read
    document.querySelectorAll('.mark-read-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var item = this.closest('.notification-item');
            var id = item.dataset.id;
            var csrf = getCsrfMeta();
            var formData = new URLSearchParams();
            if (csrf && csrf.name && csrf.hash) {
                formData.append(csrf.name, csrf.hash);
            }

            fetch(BASE_URL + '/mechanic/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
            .then(function(res) { return res.json(); })
            .then(function(response) {
                if (response.status === 'success') {
                    item.classList.remove('bg-indigo-50/50', 'border-l-4', 'border-indigo-500');
                    item.classList.add('bg-white', 'border-l-4', 'border-transparent');
                    btn.remove();
                }
            })
            .catch(function() {});
        });
    });

    // Click notification item — mark read and redirect
    document.querySelectorAll('.notification-item').forEach(function(item) {
        item.addEventListener('click', function() {
            var id = this.dataset.id;
            var relatedType = this.dataset.relatedType;
            var relatedId = this.dataset.relatedId;

            var csrf = getCsrfMeta();
            var formData = new URLSearchParams();
            if (csrf && csrf.name && csrf.hash) {
                formData.append(csrf.name, csrf.hash);
            }

            fetch(BASE_URL + '/mechanic/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
            .then(function() {
                if (relatedType === 'job_card' && relatedId) {
                    window.location.href = BASE_URL + '/mechanic/jobs/' + relatedId;
                }
            })
            .catch(function() {});
        });
    });

    // Mark all read
    var markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            var csrf = getCsrfMeta();
            var formData = new URLSearchParams();
            if (csrf && csrf.name && csrf.hash) {
                formData.append(csrf.name, csrf.hash);
            }

            fetch(BASE_URL + '/mechanic/notifications/mark_all_read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
            .then(function(res) { return res.json(); })
            .then(function(response) {
                if (response.status === 'success') {
                    document.querySelectorAll('.notification-item').forEach(function(item) {
                        item.classList.remove('bg-indigo-50/50', 'border-l-4', 'border-indigo-500');
                        item.classList.add('bg-white', 'border-l-4', 'border-transparent');
                    });
                    document.querySelectorAll('.mark-read-btn').forEach(function(b) { b.remove(); });
                    document.querySelectorAll('.notification-item .w-2\\.h-2').forEach(function(dot) { dot.remove(); });
                    markAllBtn.remove();
                }
            })
            .catch(function() {});
        });
    }
});
</script>
<?= $this->endSection() ?>
