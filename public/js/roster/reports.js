$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    var selectedType = null;
    var reportData = [];
    var reportColumns = [];
    var sortKey = null;
    var sortAsc = true;

    function esc(str) {
        if (!str && str !== 0) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str)));
        return div.innerHTML;
    }

    // Default date range: last 30 days
    var today = new Date();
    var thirtyAgo = new Date();
    thirtyAgo.setDate(today.getDate() - 30);
    $('#dateTo').val(today.toISOString().split('T')[0]);
    $('#dateFrom').val(thirtyAgo.toISOString().split('T')[0]);

    // Card selection
    $('.report-card').on('click', function () {
        var type = $(this).data('type');
        if (selectedType === type) return;
        selectedType = type;

        $('.report-card').removeClass('active');
        $(this).addClass('active');

        // Show filter section
        $('#filterSection').addClass('visible');

        // Show/hide type-specific filters
        $('.filter-extra').removeClass('visible');
        $('.filter-extra[data-for="' + type + '"]').addClass('visible');

        // Reset results
        hideResults();
    });

    function hideResults() {
        $('#reportSummary').removeClass('visible');
        $('#reportTableWrap').removeClass('visible');
        $('#emptyState').removeClass('visible');
        $('#loadingOverlay').removeClass('visible');
        $('#truncatedNotice').removeClass('visible');
        $('#btnExportCSV').hide();
    }

    // Generate report
    $('#btnGenerate').on('click', function () {
        if (!selectedType) return;

        var params = {
            report_type: selectedType,
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val()
        };

        if (selectedType === 'training') {
            params.status = $('#filterTrainingStatus').val();
        } else if (selectedType === 'mar') {
            params.code = $('#filterMARCode').val();
        } else if (selectedType === 'shifts') {
            params.shift_type = $('#filterShiftType').val();
            params.status = $('#filterShiftStatus').val();
        } else if (selectedType === 'feedback') {
            params.feedback_type = $('#filterFeedbackType').val();
            params.status = $('#filterFeedbackStatus').val();
        }

        hideResults();
        $('#loadingOverlay').addClass('visible');

        $.ajax({
            url: baseUrl + '/roster/reports/generate',
            method: 'GET',
            data: params,
            dataType: 'json',
            success: function (resp) {
                $('#loadingOverlay').removeClass('visible');
                if (resp.status && resp.report) {
                    reportData = resp.report.data || [];
                    reportColumns = resp.report.columns || [];
                    sortKey = null;
                    sortAsc = true;
                    renderSummary(resp.report.summary);
                    if (reportData.length > 0) {
                        renderTable();
                        $('#reportTableWrap').addClass('visible');
                        $('#btnExportCSV').show();
                        if (resp.report.summary.total > 500) {
                            $('#totalRecords').text(resp.report.summary.total);
                            $('#truncatedNotice').addClass('visible');
                        }
                    } else {
                        $('#emptyState').addClass('visible');
                    }
                }
            },
            error: function (xhr) {
                $('#loadingOverlay').removeClass('visible');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;
                    var msg = 'Validation error';
                    if (errors) {
                        var first = Object.values(errors)[0];
                        msg = Array.isArray(first) ? first[0] : first;
                    }
                    alert(msg);
                } else {
                    alert('Failed to generate report. Please try again.');
                }
            }
        });
    });

    function renderSummary(summary) {
        var count = summary.total || 0;
        $('#recordCount').html('<span>' + esc(count) + '</span> records found');

        var badges = '';
        if (selectedType === 'incidents') {
            badges = '<span class="summary-badge">Total: <span class="sb-val">' + esc(summary.total) + '</span></span>';
        } else if (selectedType === 'training') {
            badges = '<span class="summary-badge">Total: <span class="sb-val">' + esc(summary.total) + '</span></span>' +
                '<span class="summary-badge">Completed: <span class="sb-val">' + esc(summary.completed) + '</span></span>' +
                '<span class="summary-badge">Pending: <span class="sb-val">' + esc(summary.pending) + '</span></span>' +
                '<span class="summary-badge">Overdue: <span class="sb-val">' + esc(summary.overdue) + '</span></span>' +
                '<span class="summary-badge">Compliance: <span class="sb-val">' + esc(summary.compliance_rate) + '%</span></span>';
        } else if (selectedType === 'mar') {
            badges = '<span class="summary-badge">Total: <span class="sb-val">' + esc(summary.total) + '</span></span>' +
                '<span class="summary-badge">Administered: <span class="sb-val">' + esc(summary.administered) + '</span></span>' +
                '<span class="summary-badge">Refused: <span class="sb-val">' + esc(summary.refused) + '</span></span>' +
                '<span class="summary-badge">Spoilt: <span class="sb-val">' + esc(summary.spoilt) + '</span></span>' +
                '<span class="summary-badge">Compliance: <span class="sb-val">' + esc(summary.compliance_rate) + '%</span></span>';
        } else if (selectedType === 'shifts') {
            badges = '<span class="summary-badge">Total: <span class="sb-val">' + esc(summary.total) + '</span></span>' +
                '<span class="summary-badge">Filled: <span class="sb-val">' + esc(summary.filled) + '</span></span>' +
                '<span class="summary-badge">Unfilled: <span class="sb-val">' + esc(summary.unfilled) + '</span></span>' +
                '<span class="summary-badge">Fill Rate: <span class="sb-val">' + esc(summary.fill_rate) + '%</span></span>';
        } else if (selectedType === 'feedback') {
            badges = '<span class="summary-badge">Total: <span class="sb-val">' + esc(summary.total) + '</span></span>' +
                '<span class="summary-badge">Avg Rating: <span class="sb-val">' + esc(summary.avg_rating) + '/5</span></span>' +
                '<span class="summary-badge">New: <span class="sb-val">' + esc(summary['new']) + '</span></span>' +
                '<span class="summary-badge">Resolved: <span class="sb-val">' + esc(summary.resolved) + '</span></span>';
        }
        $('#summaryBadges').html(badges);
        $('#reportSummary').addClass('visible');
    }

    function renderTable() {
        var thead = '<tr>';
        for (var i = 0; i < reportColumns.length; i++) {
            var col = reportColumns[i];
            var icon = '';
            if (sortKey === col.key) {
                icon = sortAsc ? ' <i class="fa fa-sort-asc sort-icon"></i>' : ' <i class="fa fa-sort-desc sort-icon"></i>';
            } else {
                icon = ' <i class="fa fa-sort sort-icon"></i>';
            }
            thead += '<th data-key="' + esc(col.key) + '">' + esc(col.label) + icon + '</th>';
        }
        thead += '</tr>';
        $('#reportThead').html(thead);

        var tbody = '';
        var limit = Math.min(reportData.length, 500);
        for (var j = 0; j < limit; j++) {
            var row = reportData[j];
            tbody += '<tr>';
            for (var k = 0; k < reportColumns.length; k++) {
                var key = reportColumns[k].key;
                var val = row[key];
                if (key === 'rating' && val) {
                    var stars = '';
                    for (var s = 0; s < 5; s++) {
                        stars += s < val ? '<i class="fa fa-star" style="color:#f5a623"></i>' : '<i class="fa fa-star-o" style="color:#ddd"></i>';
                    }
                    tbody += '<td>' + stars + '</td>';
                } else {
                    tbody += '<td title="' + esc(val) + '">' + esc(val) + '</td>';
                }
            }
            tbody += '</tr>';
        }
        $('#reportTbody').html(tbody);
    }

    // Column sorting
    $(document).on('click', '#reportThead th', function () {
        var key = $(this).data('key');
        if (sortKey === key) {
            sortAsc = !sortAsc;
        } else {
            sortKey = key;
            sortAsc = true;
        }
        reportData.sort(function (a, b) {
            var va = a[key] || '';
            var vb = b[key] || '';
            if (typeof va === 'number' && typeof vb === 'number') {
                return sortAsc ? va - vb : vb - va;
            }
            va = String(va).toLowerCase();
            vb = String(vb).toLowerCase();
            if (va < vb) return sortAsc ? -1 : 1;
            if (va > vb) return sortAsc ? 1 : -1;
            return 0;
        });
        renderTable();
    });

    // CSV export
    $('#btnExportCSV').on('click', function () {
        if (!reportData.length || !reportColumns.length) return;

        var csv = reportColumns.map(function (c) { return '"' + c.label.replace(/"/g, '""') + '"'; }).join(',') + '\n';
        for (var i = 0; i < reportData.length; i++) {
            var row = reportData[i];
            var line = reportColumns.map(function (c) {
                var v = row[c.key];
                if (v === null || v === undefined) v = '';
                return '"' + String(v).replace(/"/g, '""') + '"';
            }).join(',');
            csv += line + '\n';
        }

        var dateStr = new Date().toISOString().split('T')[0];
        var filename = selectedType + '_report_' + dateStr + '.csv';
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
});
