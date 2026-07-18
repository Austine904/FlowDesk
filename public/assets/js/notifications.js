(function() {
    if (!document.getElementById('notificationBadge')) return;

    function pollNotifications() {
        var csrf = getCsrfMeta();
        fetch(BASE_URL + 'admin/notifications', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf.hash }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var badge = document.getElementById('notificationBadge');
            var dropdown = document.querySelector('#notificationDropdown .max-h-64');
            if (!badge || !dropdown) return;
            if (data && data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
                dropdown.innerHTML = data.items.map(function(item) {
                    return '<div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 text-sm">' +
                           '<p class="text-gray-900">' + item.message + '</p>' +
                           '<p class="text-xs text-gray-400 mt-0.5">' + item.time + '</p></div>';
                }).join('');
            } else {
                badge.textContent = '0';
                badge.classList.add('hidden');
                dropdown.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-400">No new notifications</div>';
            }
        })
        .catch(function(e) { console.log('Notification poll failed:', e); });
    }

    pollNotifications();
    setInterval(pollNotifications, 60000);
})();
