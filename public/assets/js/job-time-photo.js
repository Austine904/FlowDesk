/**
 * Job Detail — Photo Upload & Time Tracking
 * Loaded on technician/job_detail.php
 */
(function () {
    'use strict';

    var BASE = typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '';
    var jobId = document.getElementById('jobDetailData')?.dataset?.jobId;

    if (!jobId) return;

    function csrfField() {
        var m = getCsrfMeta();
        var data = {};
        if (m && m.name && m.hash) {
            data[m.name] = m.hash;
        }
        return data;
    }

    // ======================================================================
    // Photo Upload
    // ======================================================================
    var uploadForm = document.getElementById('photoUploadForm');
    var uploadBtn = document.getElementById('photoUploadBtn');
    var uploadProgress = document.getElementById('photoUploadProgress');

    if (uploadForm && uploadBtn) {
        uploadBtn.addEventListener('click', function (e) {
            e.preventDefault();

            var fileInput = document.getElementById('photoFile');
            var typeInput = document.getElementById('photoType');
            var captionInput = document.getElementById('photoCaption');

            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                Swal.fire('Error', 'Please select a photo to upload.', 'error');
                return;
            }

            var formData = new FormData();
            formData.append('photo', fileInput.files[0]);
            formData.append('photo_type', typeInput ? typeInput.value : 'Progress');
            if (captionInput && captionInput.value.trim()) {
                formData.append('caption', captionInput.value.trim());
            }

            var csrf = getCsrfMeta();
            if (csrf && csrf.name && csrf.hash) {
                formData.append(csrf.name, csrf.hash);
            }

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            if (uploadProgress) uploadProgress.classList.remove('hidden');

            fetch(BASE + '/mechanic/jobs/' + jobId + '/photos', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) {
                return res.json().then(function (data) { data.statusCode = res.status; return data; });
            })
            .then(function (response) {
                if (response.status === 'success') {
                    var photo = response.photo;
                    var container = document.getElementById('photoGallery-' + photo.photo_type);
                    if (container) {
                        var col = document.createElement('div');
                        col.className = 'relative group';
                        col.innerHTML =
                            '<a href="' + BASE + '/' + photo.file_path + '" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow">' +
                                '<img src="' + BASE + '/' + photo.file_path + '" alt="' + (photo.caption || 'Job photo') + '" class="w-full h-32 object-cover">' +
                            '</a>' +
                            (photo.caption ? '<p class="mt-1 text-xs text-gray-500 truncate">' + photo.caption + '</p>' : '');
                        container.appendChild(col);

                        var emptyMsg = container.parentElement.querySelector('.gallery-empty');
                        if (emptyMsg) emptyMsg.remove();
                    }

                    if (fileInput) fileInput.value = '';
                    if (captionInput) captionInput.value = '';

                    Swal.fire('Uploaded', 'Photo uploaded successfully.', 'success');
                } else {
                    Swal.fire('Error', response.message || 'Upload failed.', 'error');
                }
            })
            .catch(function () {
                Swal.fire('Error', 'Upload failed. Please try again.', 'error');
            })
            .finally(function () {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
                if (uploadProgress) uploadProgress.classList.add('hidden');
            });
        });
    }

    // ======================================================================
    // Time Tracking
    // ======================================================================
    var clockBtn = document.getElementById('clockToggle');
    var clockState = document.getElementById('clockState');
    var logList = document.getElementById('timeLogList');

    function formatTime(isoStr) {
        if (!isoStr) return '--';
        var d = new Date(isoStr.replace(' ', 'T') + 'Z');
        if (isNaN(d.getTime())) return isoStr;
        return d.toLocaleString();
    }

    function formatDuration(minutes) {
        if (minutes == null) return '--';
        var h = Math.floor(minutes / 60);
        var m = minutes % 60;
        if (h > 0) return h + 'h ' + m + 'm';
        return m + 'm';
    }

    function refreshTimeLogs() {
        fetch(BASE + '/mechanic/jobs/' + jobId + '/time', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.json(); })
        .then(function (response) {
            if (response.status !== 'success') return;

            var logs = response.logs || [];
            var openLog = null;
            logs.forEach(function (l) {
                if (!l.clock_out) openLog = l;
            });

            if (clockState) {
                if (openLog) {
                    clockState.innerHTML =
                        '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700">' +
                            '<span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>' +
                            'Clocked in since ' + formatTime(openLog.clock_in) +
                        '</span>';
                } else {
                    clockState.innerHTML =
                        '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-600">' +
                            '<i class="fas fa-clock text-xs"></i> Not clocked in' +
                        '</span>';
                }
            }

            if (clockBtn) {
                if (openLog) {
                    clockBtn.innerHTML = '<i class="fas fa-stop-circle"></i> Clock Out';
                    clockBtn.dataset.action = 'clockout';
                    clockBtn.dataset.logId = openLog.id;
                    clockBtn.className = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors';
                } else {
                    clockBtn.innerHTML = '<i class="fas fa-play-circle"></i> Clock In';
                    clockBtn.dataset.action = 'clockin';
                    clockBtn.className = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors';
                }
                clockBtn.disabled = false;
            }

            if (logList) {
                if (logs.length === 0) {
                    logList.innerHTML = '<p class="text-sm text-gray-400 py-4 text-center">No time entries yet.</p>';
                } else {
                    var html = '';
                    logs.forEach(function (l) {
                        html +=
                            '<tr class="hover:bg-gray-50">' +
                                '<td class="px-4 py-3 text-sm text-gray-900">' + formatTime(l.clock_in) + '</td>' +
                                '<td class="px-4 py-3 text-sm text-gray-900">' + (l.clock_out ? formatTime(l.clock_out) : '<span class="text-emerald-600 font-medium">In progress</span>') + '</td>' +
                                '<td class="px-4 py-3 text-sm text-gray-900">' + (l.clock_out ? formatDuration(l.duration_minutes) : '--') + '</td>' +
                                '<td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">' + (l.notes || '') + '</td>' +
                            '</tr>';
                    });
                    logList.innerHTML = html;
                }
            }
        })
        .catch(function () {});
    }

    if (clockBtn) {
        clockBtn.addEventListener('click', function () {
            var action = clockBtn.dataset.action;
            var url, bodyData;

            if (action === 'clockin') {
                url = BASE + '/mechanic/jobs/' + jobId + '/time/start';
                bodyData = new URLSearchParams();
            } else if (action === 'clockout') {
                url = BASE + '/mechanic/time/' + clockBtn.dataset.logId + '/stop';
                bodyData = new URLSearchParams();
            } else {
                return;
            }

            var csrf = getCsrfMeta();
            if (csrf && csrf.name && csrf.hash) {
                bodyData.append(csrf.name, csrf.hash);
            }

            clockBtn.disabled = true;
            clockBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: bodyData.toString()
            })
            .then(function (res) {
                return res.json().then(function (data) { data.statusCode = res.status; return data; });
            })
            .then(function (response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success');
                    refreshTimeLogs();
                } else {
                    Swal.fire('Error', response.message || 'Request failed.', 'error');
                    clockBtn.disabled = false;
                }
            })
            .catch(function () {
                Swal.fire('Error', 'Request failed. Please try again.', 'error');
                clockBtn.disabled = false;
            });
        });
    }

    // Initial load
    refreshTimeLogs();
})();
