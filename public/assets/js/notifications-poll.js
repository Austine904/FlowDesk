/**
 * Notifications polling — loaded from technician/layout.php
 * Checks unread count every 30s and updates badge elements.
 */
(function () {
    var POLL_INTERVAL = 30000;

    function updateBadges(count) {
        var elements = [
            document.getElementById('tabNotifBadge'),
            document.getElementById('sidebarNotifBadge'),
            document.getElementById('topbarNotifBadge')
        ];

        elements.forEach(function (el) {
            if (!el) return;
            if (count > 0) {
                el.textContent = count > 99 ? '99+' : count;
                el.classList.remove('hidden');
                // sidebar badge is a dot, not a count
                if (el.id === 'sidebarNotifBadge') {
                    el.textContent = '';
                }
            } else {
                el.classList.add('hidden');
            }
        });
    }

    function poll() {
        fetch(BASE_URL + '/mechanic/notifications/unread_count', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (typeof data.count === 'number') {
                updateBadges(data.count);
            }
        })
        .catch(function () {});
    }

    if (typeof BASE_URL === 'undefined') return;

    // Initial fetch
    poll();

    // Poll every 30s
    setInterval(poll, POLL_INTERVAL);
})();
