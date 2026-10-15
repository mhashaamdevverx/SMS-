/**
 * Excellence Universal Real-Time Notification & User Menu Engine
 * Extracted modular script to eliminate internal inline scripts in layouts.
 */

function getNotifStyle(type, icon) {
    switch (type) {
        case 'leave':
            return {
                bg: '#eef2ff',
                color: '#4338ca',
                icon: icon || 'bi-envelope-paper-fill'
            };
        case 'announcement':
            return {
                bg: '#fef3c7',
                color: '#b45309',
                icon: icon || 'bi-megaphone-fill'
            };
        case 'exam':
            return {
                bg: '#e0f2fe',
                color: '#0284c7',
                icon: icon || 'bi-calendar-check-fill'
            };
        case 'result':
            return {
                bg: '#fdf2f8',
                color: '#be185d',
                icon: icon || 'bi-award-fill'
            };
        case 'attendance':
            return {
                bg: '#ecfdf5',
                color: '#047857',
                icon: icon || 'bi-person-check-fill'
            };
        default:
            return {
                bg: '#f1f5f9',
                color: '#475569',
                icon: icon || 'bi-bell-fill'
            };
    }
}

function fetchAppNotifications() {
    fetch('/api/notifications')
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            const badges = document.querySelectorAll('#notifBadge, #adminNotifBadge, #teacherNotifBadge, .notif-count-badge');
            const pulseDot = document.getElementById('notifPulseDot');

            const unreadCount = data.unread ?? data.unread_count ?? 0;

            badges.forEach(badge => {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    badge.style.display = 'inline-flex';
                    badge.style.alignItems = 'center';
                    badge.style.justifyContent = 'center';
                    badge.style.position = 'absolute';
                    badge.style.top = '-4px';
                    badge.style.right = '-4px';
                    badge.style.minWidth = '18px';
                    badge.style.height = '18px';
                    badge.style.padding = '0 4px';
                    badge.style.borderRadius = '99px';
                    badge.style.fontSize = '10px';
                    badge.style.fontWeight = '900';
                    badge.style.backgroundColor = '#EF4444';
                    badge.style.color = '#ffffff';
                    badge.style.border = '2px solid var(--surface, #ffffff)';
                    badge.style.boxShadow = '0 2px 5px rgba(239, 68, 68, 0.4)';
                    badge.style.zIndex = '10';
                } else {
                    badge.style.display = 'none';
                }
            });

            if (pulseDot) {
                pulseDot.style.display = 'none';
            }

            const lists = document.querySelectorAll('#notifListContainer, #adminNotifListContainer');

            if (lists.length > 0) {
                if (!data.notifications || data.notifications.length === 0) {
                    const emptyHtml = `
                        <div style="text-align: center; padding: 32px 20px; color: var(--muted, #64748b);">
                            <i class="bi bi-bell-slash-fill" style="font-size: 26px; color: var(--border, #cbd5e1); display: block; margin-bottom: 8px;"></i>
                            <span style="font-size: 13px; font-weight: 700; color: var(--text, #0f172a);">No notifications yet</span>
                            <p style="font-size: 11.5px; margin: 4px 0 0; color: #94a3b8;">You're all caught up with your updates!</p>
                        </div>
                    `;
                    lists.forEach(l => l.innerHTML = emptyHtml);
                    return;
                }

                let html = '';
                data.notifications.forEach(item => {
                    const styleMeta = getNotifStyle(item.type, item.icon);
                    const bg = item.is_read ? 'var(--surface, #ffffff)' : '#EEF2FF';
                    const border = item.is_read ? 'var(--border-light, #f1f5f9)' : '#C7D2FE';
                    const dot = item.is_read ? '' : `<span style="width:7px;height:7px;border-radius:50%;background:#4F46E5;display:inline-block;margin-left:6px;vertical-align:middle;"></span>`;

                    html += `
                        <a href="${item.action_url || '#'}" onclick="markNotifRead(${item.id}, event, '${item.action_url || '#'}')" style="display:flex;align-items:flex-start;gap:11px;padding:11px 15px;background:${bg};border-bottom:1px solid ${border};text-decoration:none;transition:background 0.15s ease;" onmouseover="this.style.background='${item.is_read ? '#f8fafc' : '#E0E7FF'}'" onmouseout="this.style.background='${bg}'">
                            <div style="width:34px;height:34px;border-radius:8px;background:${styleMeta.bg};color:${styleMeta.color};display:grid;place-items:center;font-size:15px;flex-shrink:0;">
                                <i class="bi ${styleMeta.icon}"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2px;">
                                    <strong style="font-size:12.5px;color:var(--text, #0f172a);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.title} ${dot}</strong>
                                    <small style="font-size:10px;color:#94a3b8;white-space:nowrap;margin-left:6px;">${item.time_ago}</small>
                                </div>
                                <p style="font-size:11.5px;color:#475569;margin:0;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${item.message}</p>
                            </div>
                        </a>
                    `;
                });
                lists.forEach(l => l.innerHTML = html);
            }
        })
        .catch(err => console.error('Notification error:', err));
}

function markNotifRead(id, event, targetUrl) {
    if (event && (event.ctrlKey || event.metaKey || event.button === 1)) {
        const tokenEl = document.querySelector('meta[name="csrf-token"]');
        const token = tokenEl ? tokenEl.getAttribute('content') : '';
        fetch('/api/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ id: id })
        });
        return;
    }

    if (event) event.preventDefault();
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const token = tokenEl ? tokenEl.getAttribute('content') : '';

    fetch('/api/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ id: id })
    }).finally(() => {
        if (targetUrl && targetUrl !== '#' && !targetUrl.endsWith('#')) {
            window.location.href = targetUrl;
        } else {
            fetchAppNotifications();
        }
    });
}

function markAllNotificationsAsRead() {
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const token = tokenEl ? tokenEl.getAttribute('content') : '';

    // Immediately clear badges optimistically
    const badges = document.querySelectorAll('#notifBadge, #adminNotifBadge, #teacherNotifBadge, .notif-count-badge');
    badges.forEach(b => b.style.display = 'none');

    fetch('/api/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({})
    }).then(() => fetchAppNotifications());
}

function markAllNotificationsAsReadAdmin() {
    markAllNotificationsAsRead();
}

function markNotifReadAdmin(id, event, targetUrl) {
    markNotifRead(id, event, targetUrl);
}

function fetchAdminAppNotifications() {
    fetchAppNotifications();
}

document.addEventListener('DOMContentLoaded', () => {
    const bellBtns = document.querySelectorAll('#notifBellBtn, #adminNotifBellBtn');
    bellBtns.forEach(bellBtn => {
        const notifDropdown = bellBtn.parentElement?.querySelector('#notifDropdown, #adminNotifDropdown')
            || document.getElementById('notifDropdown')
            || document.getElementById('adminNotifDropdown');

        if (notifDropdown) {
            bellBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = notifDropdown.style.display === 'block';
                notifDropdown.style.display = isOpen ? 'none' : 'block';
                if (!isOpen) fetchAppNotifications();
            });

            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                    notifDropdown.style.display = 'none';
                }
            });
        }
    });

    const topUser = document.getElementById('topProfileUser');
    const menu = document.getElementById('userDropdownMenu');
    if (topUser && menu) {
        topUser.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpening = menu.style.display === 'none' || !menu.style.display;
            menu.style.display = isOpening ? 'block' : 'none';
        });

        document.addEventListener('click', () => {
            menu.style.display = 'none';
        });
    }

    const modal = document.getElementById('student-profile-modal');
    const openProfileBtn = document.getElementById('dropdownProfileBtn');
    const closeBtn = document.getElementById('closeStudentModal');

    if (openProfileBtn && modal) {
        openProfileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (menu) menu.style.display = 'none';
            modal.style.display = 'grid';
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            modal.style.display = 'none';
        });
    }

    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal) modal.style.display = 'none';
    });

    fetchAppNotifications();
    setInterval(fetchAppNotifications, 8000);
});
