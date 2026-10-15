{{-- EXCELLENCE SCHOOL SYSTEM - ANNOUNCEMENT BROADCAST MODAL --}}
<div id="announcementModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999999; place-items: center; padding: 20px;">
    <div style="background: var(--surface, #ffffff); border: 1px solid var(--border, #e2e8f0); border-radius: var(--radius-lg, 16px); width: 100%; max-width: 520px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); overflow: hidden; animation: notifModalFade 0.2s ease-out;">
        <!-- Header -->
        <div style="padding: 18px 24px; background: var(--bg, #f8fafc); border-bottom: 1px solid var(--border, #e2e8f0); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(13, 148, 136, 0.12); color: var(--primary, #0d9488); display: grid; place-items: center; font-size: 18px;">
                    <i class="bi bi-megaphone-fill"></i>
                </div>
                <div>
                    <strong style="display: block; font-size: 15px; color: var(--text, #0f172a);">Broadcast Announcement</strong>
                    <small style="color: var(--muted, #64748b); font-size: 12px;">Notify students, teachers, or specific classes</small>
                </div>
            </div>
            <button type="button" onclick="closeAnnouncementModal()" style="background: transparent; border: none; font-size: 20px; color: var(--muted, #64748b); cursor: pointer; padding: 4px;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="announcementForm" action="{{ route('announcements.publish') }}" method="POST" style="padding: 22px 24px; margin: 0;">
            @csrf

            <!-- Title -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text, #0f172a); margin-bottom: 6px;">Announcement Title *</label>
                <input type="text" name="title" required placeholder="e.g. Annual Sports Day Schedule Announced" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border, #cbd5e1); border-radius: 8px; font-size: 13.5px; background: var(--surface, #ffffff); color: var(--text, #0f172a); box-sizing: border-box;">
            </div>

            <!-- Audience Target -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text, #0f172a); margin-bottom: 6px;">Target Audience *</label>
                <select name="audience" id="announcementAudience" onchange="toggleAnnouncementClassSection()" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border, #cbd5e1); border-radius: 8px; font-size: 13.5px; background: var(--surface, #ffffff); color: var(--text, #0f172a); box-sizing: border-box;">
                    <option value="all">Entire School (Students, Faculty, Staff)</option>
                    <option value="students">All Enrolled Students</option>
                    <option value="teachers">All Teaching Staff & Faculty</option>
                    <option value="incharges">All Class In-Charge Teachers</option>
                    <option value="class">Specific Class / Section</option>
                </select>
            </div>

            <!-- Class & Section (shown when class selected) -->
            <div id="announcementClassRow" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text, #0f172a); margin-bottom: 6px;">Class</label>
                    <select name="class" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border, #cbd5e1); border-radius: 8px; font-size: 13px; background: var(--surface, #ffffff); color: var(--text, #0f172a); box-sizing: border-box;">
                        @php
                            $allClassOptions = \App\Models\Classes::all(['class_name'])->pluck('class_name')->unique();
                            if ($allClassOptions->isEmpty()) {
                                $allClassOptions = collect(['Pre-KG', 'KG', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN']);
                            }
                        @endphp
                        @foreach($allClassOptions as $cName)
                            <option value="{{ $cName }}">{{ $cName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text, #0f172a); margin-bottom: 6px;">Section</label>
                    <select name="section" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border, #cbd5e1); border-radius: 8px; font-size: 13px; background: var(--surface, #ffffff); color: var(--text, #0f172a); box-sizing: border-box;">
                        <option value="">All Sections</option>
                        <option value="Red">Red</option>
                        <option value="Green">Green</option>
                        <option value="Blue">Blue</option>
                        <option value="Yellow">Yellow</option>
                        <option value="A">Section A</option>
                    </select>
                </div>
            </div>

            <!-- Message Body -->
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text, #0f172a); margin-bottom: 6px;">Announcement Message *</label>
                <textarea name="message" rows="4" required placeholder="Write your announcement details here..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border, #cbd5e1); border-radius: 8px; font-size: 13.5px; background: var(--surface, #ffffff); color: var(--text, #0f172a); box-sizing: border-box; resize: vertical;"></textarea>
            </div>

            <!-- Actions -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeAnnouncementModal()" style="padding: 9px 18px; border: 1px solid var(--border, #cbd5e1); background: var(--bg, #f8fafc); color: var(--text, #0f172a); border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 9px 20px; border: none; background: var(--primary, #0d9488); color: #ffffff; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);">
                    <i class="bi bi-send-fill"></i> Publish Announcement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAnnouncementModal() {
    const modal = document.getElementById('announcementModal');
    if (modal) modal.style.display = 'grid';
}
function closeAnnouncementModal() {
    const modal = document.getElementById('announcementModal');
    if (modal) modal.style.display = 'none';
}
function toggleAnnouncementClassSection() {
    const aud = document.getElementById('announcementAudience');
    const row = document.getElementById('announcementClassRow');
    if (aud && row) {
        row.style.display = aud.value === 'class' ? 'grid' : 'none';
    }
}
</script>
