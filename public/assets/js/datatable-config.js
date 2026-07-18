var FlowDesk = FlowDesk || {};

FlowDesk.dtDefaults = {
    pageLength: 25,
    dom: '<"top flex justify-between flex-wrap gap-2 mb-4"<"length-wrap"l><"search-wrap"f>>rt<"bottom flex justify-between flex-wrap gap-2 mt-4"<"info-wrap"i><"pagination-wrap"p>>',
    language: {
        search: "",
        searchPlaceholder: "Search...",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "No entries found",
        infoFiltered: "(filtered from _MAX_ total entries)"
    }
};

FlowDesk.serverSideTable = function(selector, customOptions) {
    var defaults = {
        processing: true,
        serverSide: true,
        pageLength: FlowDesk.dtDefaults.pageLength,
        dom: FlowDesk.dtDefaults.dom,
        language: FlowDesk.dtDefaults.language,
        ajax: {
            type: 'POST',
            data: function(d) {
                var csrf = getCsrfMeta();
                if (csrf.name) d[csrf.name] = csrf.hash;
            }
        }
    };
    var options = $.extend(true, {}, defaults, customOptions);
    var table = $(selector).DataTable(options);
    table.reloadSafe = function(callback) {
        table.ajax.reload(function() {
            if (typeof callback === 'function') callback();
        }, false);
    };
    return table;
};

FlowDesk.clientSideTable = function(selector, customOptions) {
    var defaults = {
        pageLength: FlowDesk.dtDefaults.pageLength,
        dom: FlowDesk.dtDefaults.dom,
        language: FlowDesk.dtDefaults.language
    };
    var options = $.extend(true, {}, defaults, customOptions);
    return $(selector).DataTable(options);
};

FlowDesk.handleAjaxError = function(xhr, context) {
    var msg = 'An error occurred.';
    try {
        var r = JSON.parse(xhr.responseText);
        msg = r.message || r.messages?.error || msg;
    } catch (e) {}
    if (typeof Swal !== 'undefined') {
        Swal.fire('Error!', msg, 'error');
    } else {
        alert(msg);
    }
};

FlowDesk.reloadOnSuccess = function(table, response) {
    if (response.status === 'success') {
        if (typeof Swal !== 'undefined') {
            Swal.fire('Success!', response.message || 'Operation completed successfully.', 'success');
        }
        if (table && typeof table.ajax === 'function') {
            table.ajax.reload();
        } else if (table && table.reloadSafe) {
            table.reloadSafe();
        }
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error!', response.message || 'Operation failed.', 'error');
        } else {
            alert(response.message || 'Operation failed.');
        }
    }
};
