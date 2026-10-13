/**
 * Dedicated JavaScript logic for Monthly Test Sessions module
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('Test Sessions Module Initialized');
});

function switchTestSessionMonth(mKey) {
    const panels = document.querySelectorAll('.test-session-month-panel');
    panels.forEach(p => p.style.display = 'none');

    const target = document.getElementById('testSessionPanel-' + mKey);
    if (target) {
        target.style.display = 'block';
    }
}

function openTestResultModal(mKey) {
    if (typeof window.testSessionsData === 'undefined' || !window.testSessionsData[mKey]) return;

    const session = window.testSessionsData[mKey];

    document.getElementById('modalTestTitle').innerText = session.title;
    document.getElementById('modalTestMonth').innerText = session.month_title;
    document.getElementById('modalTotalObtained').innerText = session.total_obtained_marks + ' / ' + session.total_max_marks;
    document.getElementById('modalPercentage').innerText = session.percentage + '%';
    document.getElementById('modalGrade').innerText = session.grade || '—';
    document.getElementById('modalDownloadBtn').href = '/student/test-sessions/' + mKey + '/download';

    let tableHtml = '<table class="ts-table"><thead><tr><th>Subject</th><th>Timing</th><th style="text-align:center;">Max</th><th style="text-align:center;">Obtained</th></tr></thead><tbody>';
    
    (session.schedule_json || []).forEach(row => {
        if (row.is_off) {
            tableHtml += '<tr class="ts-row-off"><td><b>' + row.date + ' (' + row.day + ')</b></td><td colspan="3"><span class="ts-off-badge">Weekend Off</span></td></tr>';
        } else {
            const obt = session.status === 'Evaluated' ? (row.obtained_marks + ' / ' + row.max_marks) : 'Pending';
            tableHtml += '<tr class="ts-row-exam"><td><b>' + row.subject + '</b><br><small style="color:#64748b;">' + row.date + '</small></td><td>' + row.time + '</td><td style="text-align:center;">' + row.max_marks + '</td><td style="text-align:center;"><strong style="color:#047857;">' + obt + '</strong></td></tr>';
        }
    });
    tableHtml += '</tbody></table>';

    document.getElementById('modalTestContent').innerHTML = tableHtml;
    const modal = document.getElementById('testResultModal');
    if (modal) modal.style.display = 'grid';
}

function closeTestResultModal() {
    const modal = document.getElementById('testResultModal');
    if (modal) modal.style.display = 'none';
}
