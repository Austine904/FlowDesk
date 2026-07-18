(function() {
    var searchInput = document.getElementById('globalSearch');
    var searchDropdown = document.getElementById('searchResults');
    if (!searchInput) return;

    var debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var query = searchInput.value.trim();
        if (query.length < 3) { searchDropdown.classList.add('hidden'); return; }
        debounceTimer = setTimeout(function() {
            var csrf = getCsrfMeta();
            fetch(BASE_URL + 'admin/search?q=' + encodeURIComponent(query), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf.hash }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data || data.length === 0) {
                    searchDropdown.innerHTML = '<div class="p-3 text-sm text-gray-400">No results found</div>';
                } else {
                    searchDropdown.innerHTML = data.map(function(item) {
                        return '<a href="' + BASE_URL + item.url + '" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-sm">' +
                               '<span class="text-gray-400">' + item.icon + '</span>' +
                               '<div><p class="text-gray-900">' + item.label + '</p><p class="text-xs text-gray-500">' + item.subtext + '</p></div></a>';
                    }).join('');
                }
                searchDropdown.classList.remove('hidden');
            })
            .catch(function(e) { console.log('Search failed:', e); });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target)) searchDropdown.classList.add('hidden');
    });
})();
