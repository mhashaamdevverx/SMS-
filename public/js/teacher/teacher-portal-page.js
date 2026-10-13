/**
 * AKM Teacher Portal Page Functions
 * Handles Leave Approval/Rejection Modals, Student Result Filtering, Live Clock,
 * and Test Session Marks Entry & Schedule View Modals.
 */

// 1. Leave Detail Modal Handler
function openLeaveModalFromBtn(btn) {
    if (!btn) return;
    const modal = document.getElementById('stdLeaveDetailModal') || document.getElementById('viewLeaveDetailModal');
    if (!modal) return;

    const name = btn.dataset.name || btn.getAttribute('data-name') || 'N/A';
    const stdId = btn.dataset.stdid || btn.getAttribute('data-stdid') || '';
    const classSec = btn.dataset.classsec || btn.getAttribute('data-classsec') || 'N/A';
    const date = btn.dataset.date || btn.getAttribute('data-date') || 'N/A';
    const type = btn.dataset.type || btn.getAttribute('data-type') || 'N/A';
    const reason = btn.dataset.reason || btn.getAttribute('data-reason') || 'No reason provided.';
    const fine = btn.dataset.fine || btn.getAttribute('data-fine') || 'Free Leave';
    const isFine = (btn.dataset.isfine || btn.getAttribute('data-isfine')) === '1';
    const status = btn.dataset.status || btn.getAttribute('data-status') || 'Pending';
    const approvedBy = btn.dataset.approvedby || btn.getAttribute('data-approvedby') || 'Pending Review';
    const canAction = (btn.dataset.canaction || btn.getAttribute('data-canaction')) === '1';
    const approveUrl = btn.dataset.approveurl || btn.getAttribute('data-approveurl') || '';
    const rejectUrl = btn.dataset.rejecturl || btn.getAttribute('data-rejecturl') || '';

    const subTitle = document.getElementById('modalStdSubTitle');
    const stdName = document.getElementById('modalStdName');
    const stdClassSec = document.getElementById('modalStdClassSec');
    const leaveDate = document.getElementById('modalLeaveDate');
    const leaveType = document.getElementById('modalLeaveType');
    const leaveReason = document.getElementById('modalLeaveReason');
    const leaveApprovedBy = document.getElementById('modalApprovedBy');

    if (subTitle) subTitle.textContent = `Roll #${stdId} • ${classSec}`;
    if (stdName) stdName.textContent = name;
    if (stdClassSec) stdClassSec.textContent = classSec;
    if (leaveDate) leaveDate.textContent = date;
    if (leaveType) leaveType.textContent = type;
    if (leaveReason) leaveReason.textContent = reason;
    if (leaveApprovedBy) leaveApprovedBy.textContent = approvedBy;

    const fineEl = document.getElementById('modalFineStatus');
    if (fineEl) {
        fineEl.textContent = fine;
        fineEl.style.color = isFine ? '#dc2626' : '#16a34a';
    }

    const badgeEl = document.getElementById('modalStatusBadge');
    if (badgeEl) {
        badgeEl.textContent = status;
        if (status.toLowerCase() === 'approved') {
            badgeEl.style.background = '#dcfce7';
            badgeEl.style.color = '#15803d';
        } else if (status.toLowerCase() === 'rejected') {
            badgeEl.style.background = '#fee2e2';
            badgeEl.style.color = '#b91c1c';
        } else {
            badgeEl.style.background = '#fef3c7';
            badgeEl.style.color = '#b45309';
        }
    }

    const actionContainer = document.getElementById('modalActionButtons');
    if (actionContainer) {
        actionContainer.innerHTML = '';
        if (canAction && status.toLowerCase() === 'pending' && approveUrl && rejectUrl) {
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '';

            actionContainer.innerHTML = `
                <form action="${approveUrl}" method="POST" style="margin:0;">
                    <input type="hidden" name="_token" value="${token}">
                    <button type="submit" onclick="return confirm('Approve leave for ${name}?')" style="padding:9px 18px;border-radius:10px;background:#10b981;color:#ffffff;font-size:13px;font-weight:800;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(16,185,129,0.3);">
                        <i class="bi bi-check-circle-fill"></i> Approve Leave
                    </button>
                </form>
                <form action="${rejectUrl}" method="POST" style="margin:0;">
                    <input type="hidden" name="_token" value="${token}">
                    <button type="submit" onclick="return confirm('Reject leave for ${name}?')" style="padding:9px 18px;border-radius:10px;background:#ef4444;color:#ffffff;font-size:13px;font-weight:800;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(239,68,68,0.3);">
                        <i class="bi bi-x-circle-fill"></i> Reject Leave
                    </button>
                </form>
            `;
        }
    }

    modal.style.display = 'grid';
}

function closeLeaveModal() {
    const modal = document.getElementById('stdLeaveDetailModal') || document.getElementById('viewLeaveDetailModal');
    if (modal) modal.style.display = 'none';
}

function closeLeaveDetailModal() {
    closeLeaveModal();
}

// 2. Student Results Modal Logic
let currentModalStudentResults = [];

function openStudentResultModal(data) {
    const nameEl = document.getElementById('modalStdName');
    const subEl = document.getElementById('modalStdSub');
    const avEl = document.getElementById('modalStdAvatar');
    const grEl = document.getElementById('modalGradeBadge');
    const dlEl = document.getElementById('modalDownloadBtn');
    const modal = document.getElementById('viewStudentResultModal');

    if (nameEl) nameEl.innerText = data.name;
    if (subEl) subEl.innerText = 'Class: ' + data.class + ' (' + (data.section || 'A') + ') • Roll #' + data.roll_no;
    if (avEl) avEl.innerText = data.name.charAt(0).toUpperCase();
    if (grEl) grEl.innerText = data.overall_grade + ' (' + data.overall_pct + '%)';
    if (dlEl) dlEl.href = data.download_url;

    currentModalStudentResults = data.results || [];
    filterModalTerm('all');

    if (modal) modal.style.display = 'grid';
}

function filterModalTerm(term) {
    document.querySelectorAll('.modal-term-btn').forEach(btn => {
        if (btn.innerText.trim().toLowerCase() === term.toLowerCase() || (term === 'all' && btn.innerText.trim().toLowerCase() === 'all terms')) {
            btn.style.background = '#4F46E5';
            btn.style.color = '#fff';
            btn.style.borderColor = '#4F46E5';
        } else {
            btn.style.background = '#fff';
            btn.style.color = '#475569';
            btn.style.borderColor = '#cbd5e1';
        }
    });

    renderModalResults(term);
}

function renderModalResults(termFilter) {
    const tbody = document.getElementById('modalResultsBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    const filtered = currentModalStudentResults.filter(r => {
        if (termFilter === 'all') return true;
        return (r.term || '').toLowerCase() === termFilter.toLowerCase();
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align:center;padding:24px;color:#64748b;">No results found for this term.</td>
            </tr>
        `;
        return;
    }

    filtered.forEach(r => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid #f1f5f9';
        tr.innerHTML = `
            <td style="padding:12px 14px;font-weight:700;color:#0f172a;">${r.subject || 'N/A'}</td>
            <td style="padding:12px 14px;color:#475569;">${r.term || 'N/A'}</td>
            <td style="padding:12px 14px;font-weight:700;color:#0f172a;">${r.total_marks || 100}</td>
            <td style="padding:12px 14px;font-weight:800;color:#4F46E5;">${r.obtained_marks || 0}</td>
            <td style="padding:12px 14px;font-weight:700;color:#0f172a;">${r.percentage || 0}%</td>
            <td style="padding:12px 14px;"><span style="background:#ecfdf5;color:#047857;padding:3px 10px;border-radius:6px;font-weight:800;font-size:12px;">${r.grade || 'A'}</span></td>
            <td style="padding:12px 14px;color:#64748b;font-size:12px;">${r.remarks || 'Passed'}</td>
        `;
        tbody.appendChild(tr);
    });
}

function closeStudentResultModal() {
    const modal = document.getElementById('viewStudentResultModal');
    if (modal) modal.style.display = 'none';
}

// 3. Live Digital Clock
function updateLiveClock() {
    const clockEl = document.getElementById('liveDigitalClock');
    if (clockEl) {
        const now = new Date();
        clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}

// 4. Test Session Modals
function openTestSessionViewModal(data) {
    const nameEl = document.getElementById('tModalStdName');
    const subEl = document.getElementById('tModalSubTitle');
    const avEl = document.getElementById('tModalAvatar');
    const grEl = document.getElementById('tModalGradeBadge');
    const tbody = document.getElementById('tModalTableBody');
    const modal = document.getElementById('viewTestSessionModal');

    if (nameEl) nameEl.innerText = data.name;
    if (subEl) subEl.innerText = 'Class: ' + data.class + ' (' + data.section + ') • Roll #' + data.roll_no + ' • ' + data.month_title;
    if (avEl) avEl.innerText = data.name.charAt(0).toUpperCase();
    if (grEl) grEl.innerText = 'Grade ' + data.grade + ' (' + data.percentage + '%)';

    if (tbody) {
        tbody.innerHTML = '';
        const sched = data.schedule || [];
        sched.forEach(row => {
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            if (row.is_off) {
                tr.style.background = '#f8fafc';
                tr.innerHTML = `
                    <td style="padding:10px 12px;color:#64748b;"><b>${row.date}</b><small style="display:block;">${row.day}</small></td>
                    <td style="padding:10px 12px;" colspan="3"><span style="background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:6px;font-size:11.5px;font-weight:700;"><i class="bi bi-moon-stars"></i> Weekend Off</span></td>
                `;
            } else {
                tr.innerHTML = `
                    <td style="padding:10px 12px;"><b>${row.date}</b><small style="display:block;color:#4F46E5;font-weight:700;">${row.day}</small></td>
                    <td style="padding:10px 12px;"><strong style="color:#0f172a;">${row.subject}</strong></td>
                    <td style="padding:10px 12px;color:#475569;font-size:12px;">${row.time}<br><small style="color:#4F46E5;">${row.venue}</small></td>
                    <td style="padding:10px 12px;text-align:center;"><strong style="color:#047857;background:#ecfdf5;padding:3px 10px;border-radius:6px;border:1px solid #a7f3d0;">${row.obtained_marks !== null ? row.obtained_marks : 'Pending'} / ${row.max_marks}</strong></td>
                `;
            }
            tbody.appendChild(tr);
        });
    }

    if (modal) modal.style.display = 'grid';
}

function closeTestSessionViewModal() {
    const modal = document.getElementById('viewTestSessionModal');
    if (modal) modal.style.display = 'none';
}

function openTestSessionEditModal(data) {
    const stdIdEl = document.getElementById('eModalStudentId');
    const mKeyEl = document.getElementById('eModalMonthKey');
    const subEl = document.getElementById('eModalSubTitle');
    const container = document.getElementById('eModalMarksInputs');
    const modal = document.getElementById('editTestSessionModal');

    if (stdIdEl) stdIdEl.value = data.student_id;
    if (mKeyEl) mKeyEl.value = data.month_key;
    if (subEl) subEl.innerText = data.name + ' (Roll #' + data.roll_no + ') • ' + data.month_title;

    if (container) {
        container.innerHTML = '';
        const sched = data.schedule || [];
        sched.forEach(row => {
            if (!row.is_off && row.subject) {
                const div = document.createElement('div');
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                div.style.justifyContent = 'space-between';
                div.style.gap = '12px';
                div.style.background = '#f8fafc';
                div.style.padding = '10px 14px';
                div.style.borderRadius = '10px';
                div.style.border = '1px solid #e2e8f0';

                const obt = row.obtained_marks !== null ? row.obtained_marks : '';
                div.innerHTML = `
                    <div style="flex:1;">
                        <strong style="color:#0f172a;display:block;font-size:13.5px;">${row.subject}</strong>
                        <small style="color:#64748b;font-size:11px;">Max Marks: ${row.max_marks || 50}</small>
                    </div>
                    <div style="width:120px;">
                        <input type="number" name="marks[${row.subject}]" value="${obt}" min="0" max="${row.max_marks || 50}" required style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-weight:700;font-size:13.5px;color:#0f172a;">
                    </div>
                `;
                container.appendChild(div);
            }
        });
    }

    if (modal) modal.style.display = 'grid';
}

function closeTestSessionEditModal() {
    const modal = document.getElementById('editTestSessionModal');
    if (modal) modal.style.display = 'none';
}

// Global DOM listeners
document.addEventListener('DOMContentLoaded', () => {
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    const leaveModal = document.getElementById('stdLeaveDetailModal') || document.getElementById('viewLeaveDetailModal');
    if (leaveModal) {
        leaveModal.addEventListener('click', (e) => {
            if (e.target === leaveModal) closeLeaveModal();
        });
    }

    const resModal = document.getElementById('viewStudentResultModal');
    if (resModal) {
        resModal.addEventListener('click', (e) => {
            if (e.target === resModal) closeStudentResultModal();
        });
    }

    const testViewModal = document.getElementById('viewTestSessionModal');
    if (testViewModal) {
        testViewModal.addEventListener('click', (e) => {
            if (e.target === testViewModal) closeTestSessionViewModal();
        });
    }

    const testEditModal = document.getElementById('editTestSessionModal');
    if (testEditModal) {
        testEditModal.addEventListener('click', (e) => {
            if (e.target === testEditModal) closeTestSessionEditModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeLeaveModal();
            closeStudentResultModal();
            closeTestSessionViewModal();
            closeTestSessionEditModal();
        }
    });
});
