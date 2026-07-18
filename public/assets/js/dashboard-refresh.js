(function() {
    if (!document.getElementById('revenueChart')) return;

    function refreshDashboard() {
        var csrf = getCsrfMeta();
        fetch(BASE_URL + 'admin/dashboard', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf.hash }
        })
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            ['stats-grid', 'charts-row', 'recent-activity', 'alerts-section'].forEach(function(id) {
                var oldEl = document.getElementById(id);
                var newEl = doc.getElementById(id);
                if (oldEl && newEl) oldEl.outerHTML = newEl.outerHTML;
            });
        })
        .catch(function(e) { console.log('Dashboard refresh failed:', e); });
    }

    setInterval(refreshDashboard, 30000);
})();
