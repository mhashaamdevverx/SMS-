/**
 * AKM Student Portal Page Functions
 * Handles Attendance Tabs, Leave Application Modals, Test Session Month Filtering,
 * and Fee Voucher Filtering & Modals.
 */

// 1. Attendance Tab Switching
function switchAttTab(type) {
    const btnM = document.getElementById('attTabMonthly');
    const btnY = document.getElementById('attTabYearly');
    const btnD = document.getElementById('attTabDaily');

    const panelM = document.getElementById('attViewMonthly');
    const panelY = document.getElementById('attViewYearly');
    const panelD = document.getElementById('attViewDaily');

    const btns = [btnM, btnY, btnD];
    btns.forEach(b => {
        if (b) { b.style.background = 'transparent'; b.style.color = '#475569'; }
    });

    if (panelM) panelM.style.display = 'none';
    if (panelY) panelY.style.display = 'none';
    if (panelD) panelD.style.display = 'none';

    if (type === 'monthly') {
        if (btnM) { btnM.style.background = '#4F46E5'; btnM.style.color = '#ffffff'; }
        if (panelM) panelM.style.display = 'block';
    } else if (type === 'yearly') {
        if (btnY) { btnY.style.background = '#4F46E5'; btnY.style.color = '#ffffff'; }
        if (panelY) panelY.style.display = 'block';
    } else {
        if (btnD) { btnD.style.background = '#4F46E5'; btnD.style.color = '#ffffff'; }
        if (panelD) panelD.style.display = 'block';
    }
}

function changeAttMonthSelect(mKey) {
    const wrappers = document.querySelectorAll('.att-month-grid-wrapper');
    wrappers.forEach(w => w.style.display = 'none');
    
    const target = document.getElementById('attMonthPanel-' + mKey);
    if (target) target.style.display = 'block';
}

function navigateAttMonth(direction) {
    const sel = document.getElementById('attMonthSelect');
    if (!sel) return;

    let idx = sel.selectedIndex + direction;
    if (idx >= 0 && idx < sel.options.length) {
        sel.selectedIndex = idx;
        changeAttMonthSelect(sel.value);
    }
}

function selectAndShowAttMonth(mKey) {
    switchAttTab('monthly');
    const sel = document.getElementById('attMonthSelect');
    if (sel) {
        sel.value = mKey;
        changeAttMonthSelect(mKey);
    }
}

// 2. Student Leave Modal Controls
function openApplyLeaveModal() {
    const m = document.getElementById('applyLeaveModal');
    if (m) m.style.display = 'grid';
}

function closeApplyLeaveModal() {
    const m = document.getElementById('applyLeaveModal');
    if (m) m.style.display = 'none';
}

function openConfirmFineModal() {
    const m = document.getElementById('fineConfirmModal');
    if (m) m.style.display = 'grid';
}

function closeFineConfirmModal() {
    const m = document.getElementById('fineConfirmModal');
    if (m) m.style.display = 'none';
}

function handleLeaveSubmit() {
    const typeSelect = document.getElementById('leave_type');
    const reasonText = document.getElementById('reason');
    if (!typeSelect || !typeSelect.value) {
        alert('Please select a leave type.');
        return;
    }
    if (!reasonText || !reasonText.value.trim()) {
        alert('Please type the reason for leave.');
        return;
    }

    const leavesBal = typeof userLeavesBalance !== 'undefined' ? userLeavesBalance : 3;
    if (leavesBal <= 0) {
        closeApplyLeaveModal();
        openConfirmFineModal();
    } else {
        document.getElementById('realLeaveForm').submit();
    }
}

function submitFineLeaveForm() {
    closeFineConfirmModal();
    document.getElementById('realLeaveForm').submit();
}

// 3. Test Session Month Switcher
function switchTestSessionMonth(mKey) {
    const panels = document.querySelectorAll('.test-session-month-panel');
    panels.forEach(p => p.style.display = 'none');

    const target = document.getElementById('testSessionPanel-' + mKey);
    if (target) target.style.display = 'block';
}

// 4. Fee Table Filtering & Modals
function filterFeeTable() {
    const monthVal = document.getElementById('feeMonthFilter')?.value.toLowerCase() || 'all';
    const yearVal = document.getElementById('feeYearFilter')?.value.toLowerCase() || 'all';
    
    const rows = document.querySelectorAll('.fee-voucher-row');
    rows.forEach(r => {
        const m = (r.getAttribute('data-month') || '').toLowerCase();
        const y = (r.getAttribute('data-year') || '').toLowerCase();

        const matchMonth = (monthVal === 'all' || m.includes(monthVal));
        const matchYear = (yearVal === 'all' || y.includes(yearVal));

        if (matchMonth && matchYear) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

function switchFeeTab(type) {
    const btnM = document.getElementById('tabBtnMonthly');
    const btnY = document.getElementById('tabBtnYearly');

    if (type === 'monthly') {
        if(btnM) { btnM.style.background = '#4F46E5'; btnM.style.color = '#ffffff'; }
        if(btnY) { btnY.style.background = 'transparent'; btnY.style.color = '#475569'; }
        
        const selM = document.getElementById('feeMonthFilter');
        if (selM) { selM.value = 'ALL'; filterFeeTable(); }
    } else {
        if(btnY) { btnY.style.background = '#4F46E5'; btnY.style.color = '#ffffff'; }
        if(btnM) { btnM.style.background = 'transparent'; btnM.style.color = '#475569'; }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // View Fee Modal Logic
    const viewModal = document.getElementById('feeViewModal');
    const viewBtns = document.querySelectorAll('.btn-view-fee');
    const closeViewBtns = document.querySelectorAll('.close-fee-view');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const vNo = document.getElementById('vModalVoucherNo');
            const vM = document.getElementById('vModalMonth');
            const vT = document.getElementById('vModalTuition');
            const vE = document.getElementById('vModalExam');
            const vO = document.getElementById('vModalOther');
            const vF = document.getElementById('vModalFine');
            const vTot = document.getElementById('vModalTotal');

            if (vNo) vNo.innerText = btn.dataset.voucher;
            if (vM) vM.innerText = btn.dataset.month + ' (Due: ' + btn.dataset.duedate + ')';
            if (vT) vT.innerText = 'Rs ' + btn.dataset.tuition;
            if (vE) vE.innerText = 'Rs ' + btn.dataset.exam;
            if (vO) vO.innerText = 'Rs ' + btn.dataset.other;
            if (vF) vF.innerText = '+ Rs ' + btn.dataset.fine;
            if (vTot) vTot.innerText = 'Rs ' + btn.dataset.total;

            const st = btn.dataset.status || 'Unpaid';
            const statusEl = document.getElementById('vModalStatus');
            if (statusEl) {
                statusEl.innerText = st;
                statusEl.style.color = st.toLowerCase() === 'paid' ? '#047857' : '#b45309';
            }

            if (viewModal) viewModal.style.display = 'grid';
        });
    });

    closeViewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (viewModal) viewModal.style.display = 'none';
        });
    });

    // Pay Fee Modal Logic
    const payModal = document.getElementById('feePayModal');
    const payBtns = document.querySelectorAll('.btn-pay-fee');
    const closePayBtns = document.querySelectorAll('.close-fee-pay');
    const payForm = document.getElementById('feePayForm');

    payBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const pNo = document.getElementById('pModalVoucherNo');
            const pNet = document.getElementById('pModalNet');
            const pFine = document.getElementById('pModalFine');
            const pTot = document.getElementById('pModalTotal');

            if (pNo) pNo.innerText = btn.dataset.voucher + ' (' + btn.dataset.month + ')';
            if (pNet) pNet.innerText = 'Rs ' + btn.dataset.net;
            if (pFine) pFine.innerText = '+ Rs ' + btn.dataset.fine;
            if (pTot) pTot.innerText = 'Rs ' + btn.dataset.total;
            if (payForm) payForm.action = btn.dataset.action;

            if (payModal) payModal.style.display = 'grid';
        });
    });

    closePayBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (payModal) payModal.style.display = 'none';
        });
    });

    if (viewModal) {
        viewModal.addEventListener('click', (e) => {
            if (e.target === viewModal) viewModal.style.display = 'none';
        });
    }

    if (payModal) {
        payModal.addEventListener('click', (e) => {
            if (e.target === payModal) payModal.style.display = 'none';
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (viewModal) viewModal.style.display = 'none';
            if (payModal) payModal.style.display = 'none';
            closeApplyLeaveModal();
            closeFineConfirmModal();
        }
    });
});
