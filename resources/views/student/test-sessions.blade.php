@extends('student-layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/test-sessions.css') }}?v={{ time() }}">

@if(session('success'))
    <div class="student-toast"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<div class="ts-page-wrapper">
    <section class="page-heading">
        <div class="ts-header-flex">
            <div>
                <p style="letter-spacing:0.05em;text-transform:uppercase;font-weight:700;color:#64748b;margin:0 0 4px;">EXAMINATION & ASSESSMENT</p>
                <h1 style="margin:0;font-size:24px;color:#0f172a;font-weight:800;">Monthly Test Sessions & Results</h1>
            </div>

            {{-- MONTH FILTER DROPDOWN --}}
            <div class="ts-month-filter-box">
                <i class="bi bi-calendar3" style="color:#0d8f83;font-size:16px;"></i>
                <span style="font-size:12.5px;font-weight:700;color:#64748b;">Select Month:</span>
                <select id="testSessionMonthSelect" onchange="switchTestSessionMonth(this.value)">
                    @foreach($testSessions as $mKey => $sData)
                        <option value="{{ $mKey }}" {{ $mKey === '2026-10' ? 'selected' : '' }}>{{ $sData->month_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    {{-- TEST SESSION NOTICE BADGE --}}
    <div class="ts-hero-badge">
        <div style="display:flex;align-items:center;gap:14px;">
            <div class="ts-icon-box"><i class="bi bi-journal-check"></i></div>
            <div>
                <span style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#a7f3d0;">Official Test Schedule & Assessment</span>
                <h3 style="margin:2px 0 0;font-size:17px;font-weight:800;color:#ffffff;">1-Week Monthly Test Series (Mon &ndash; Fri Exams &bull; Sat &amp; Sun Off)</h3>
            </div>
        </div>
        <div class="ts-notice-info">
            <i class="bi bi-info-circle-fill" style="color:#a7f3d0;"></i> Read-only student schedule view
        </div>
    </div>

    {{-- MONTHLY TEST SESSION PANELS --}}
    @foreach($testSessions as $mKey => $sData)
        @php
            $schedule = $sData->schedule_json ?? [];
            $isCompleted = $sData->status === 'Evaluated';
        @endphp
        <div id="testSessionPanel-{{ $mKey }}" class="test-session-month-panel" style="display: {{ $mKey === '2026-10' ? 'block' : 'none' }};">
            
            {{-- ROLL SLIP / EXAM NOTICE CARD --}}
            <article class="ts-card">
                <div class="ts-card-head">
                    <div>
                        <span class="ts-badge-slip">
                            <i class="bi bi-card-checklist"></i> Test Session Schedule & Slip Info
                        </span>
                        <h2 style="margin:0;font-size:20px;color:#0f172a;font-weight:800;">{{ $sData->title }}</h2>
                        <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Exam Duration: <b>{{ \Carbon\Carbon::parse($sData->start_date)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($sData->end_date)->format('d M Y') }}</b> (7 Consecutive Days)</p>
                    </div>
                    
                    <div style="text-align:right;">
                        @if($isCompleted)
                            <span class="ts-status-pill evaluated">
                                <i class="bi bi-check-circle-fill"></i> Evaluated &amp; Published
                            </span>
                        @else
                            <span class="ts-status-pill upcoming">
                                <i class="bi bi-clock-history"></i> Upcoming Session
                            </span>
                        @endif
                    </div>
                </div>

                {{-- STUDENT CANDIDATE DETAILS GRID --}}
                <div class="ts-student-grid">
                    <div>
                        <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Student Name</small>
                        <strong style="font-size:14px;color:#0f172a;">{{ $student->name }}</strong>
                    </div>
                    <div>
                        <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Registration No</small>
                        <strong style="font-size:14px;color:#0d8f83;">{{ $stdRegNo }}</strong>
                    </div>
                    <div>
                        <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll Number</small>
                        <strong style="font-size:14px;color:#0f172a;">Roll #{{ $student->std_id }}</strong>
                    </div>
                    <div>
                        <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Class & Section</small>
                        <strong style="font-size:14px;color:#0f172a;">{{ $student->class }} ({{ $student->section }})</strong>
                    </div>
                </div>

                {{-- DATE SHEET TABLE (1 WEEK: MON-FRI EXAMS, SAT-SUN OFF) --}}
                <div class="ts-table-wrapper" style="border: 1px solid #E2E8F0; border-radius: 14px; overflow: hidden; background: #ffffff; margin-bottom: 24px;">
                    <div style="padding:14px 20px;background:#ffffff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:10px;">
                        <i class="bi bi-calendar-check-fill" style="color:#4F46E5;font-size:18px;"></i>
                        <h3 style="margin:0;font-size:15px;color:#0f172a;font-weight:700;">Examination Datesheet Schedule</h3>
                    </div>
                    <table class="ts-table" style="width: 100%; border-collapse: collapse; margin: 0;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #1E1B4B 0%, #4F46E5 100%) !important;">
                                <th style="padding: 13px 14px; color: #FFFFFF !important; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; border: none; width: 18%;">DATE &amp; DAY</th>
                                <th style="padding: 13px 14px; color: #FFFFFF !important; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; border: none; width: 26%;">SUBJECT EXAM</th>
                                <th style="padding: 13px 14px; color: #FFFFFF !important; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; border: none; width: 18%;">TIMING</th>
                                <th style="padding: 13px 14px; color: #FFFFFF !important; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; border: none; width: 14%;">VENUE</th>
                                <th style="padding: 13px 14px; color: #FFFFFF !important; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; text-align:center; border: none; width: 11%;">MAX MARKS</th>
                                <th style="padding: 13px 14px; color: #FFFFFF !important; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; text-align:center; border: none; width: 13%;">OBTAINED MARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedule as $row)
                                <tr class="{{ ($row['is_off'] ?? false) ? 'ts-row-off' : 'ts-row-exam' }}">
                                    <td>
                                        <b style="color:#0f172a;">{{ $row['date'] }}</b>
                                        <small style="display:block;color:{{ ($row['is_off'] ?? false) ? '#64748b' : '#0d8f83' }};font-weight:700;">{{ $row['day'] }}</small>
                                    </td>
                                    <td>
                                        @if($row['is_off'] ?? false)
                                            <span class="ts-off-badge">
                                                <i class="bi bi-moon-stars-fill"></i> Weekend Off (No Exam)
                                            </span>
                                        @else
                                            <strong style="color:#0f172a;font-size:14px;">{{ $row['subject'] }}</strong>
                                        @endif
                                    </td>
                                    <td style="color:#475569;font-weight:600;">
                                        {{ $row['time'] }}
                                    </td>
                                    <td style="color:#475569;font-weight:600;">
                                        {{ $row['venue'] }}
                                    </td>
                                    <td style="text-align:center;font-weight:800;color:#334155;">
                                        {{ ($row['is_off'] ?? false) ? '—' : $row['max_marks'] }}
                                    </td>
                                    <td style="text-align:center;">
                                        @if($row['is_off'] ?? false)
                                            <span style="color:#94a3b8;font-weight:700;">—</span>
                                        @elseif($isCompleted)
                                            <strong style="background:#ecfdf5;color:#047857;padding:4px 12px;border-radius:6px;border:1px solid #a7f3d0;font-size:13px;">
                                                {{ $row['obtained_marks'] }} / {{ $row['max_marks'] }}
                                            </strong>
                                        @else
                                            <span style="color:#b45309;font-size:12px;font-weight:700;background:#fffbeb;padding:4px 10px;border-radius:6px;border:1px solid #fde68a;">
                                                Pending Evaluation
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ACTION BUTTONS: VIEW RESULT MODAL & DOWNLOAD PRINTABLE SLIP --}}
                <div class="ts-actions-bar">
                    <button type="button" class="ts-btn ts-btn-view" onclick="openTestResultModal('{{ $mKey }}')">
                        <i class="bi bi-eye-fill"></i> View Result Card
                    </button>
                    <a href="{{ route('student.tests.download', ['month' => $mKey]) }}" target="_blank" class="ts-btn ts-btn-download">
                        <i class="bi bi-download"></i> Download Result Slip
                    </a>
                </div>
            </article>

            {{-- TEST SESSION MARKS & PERFORMANCE SUMMARY --}}
            @if($isCompleted)
                <article class="ts-summary-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                        <div>
                            <span style="font-size:11px;font-weight:800;color:#7e22ce;text-transform:uppercase;letter-spacing:0.05em;">Published Evaluation Summary</span>
                            <h3 style="margin:2px 0 0;font-size:18px;color:#581c87;font-weight:800;">Monthly Test Result &amp; Performance Card</h3>
                        </div>

                        <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                            <div class="ts-summary-metric">
                                <small style="display:block;font-size:10px;font-weight:800;color:#6b21a8;text-transform:uppercase;">Total Marks</small>
                                <strong style="font-size:16px;color:#581c87;">{{ $sData->total_obtained_marks }} / {{ $sData->total_max_marks }}</strong>
                            </div>

                            <div class="ts-summary-metric">
                                <small style="display:block;font-size:10px;font-weight:800;color:#6b21a8;text-transform:uppercase;">Percentage</small>
                                <strong style="font-size:16px;color:#047857;">{{ $sData->percentage }}%</strong>
                            </div>

                            <div class="ts-summary-metric">
                                <small style="display:block;font-size:10px;font-weight:800;color:#6b21a8;text-transform:uppercase;">Grade</small>
                                <span style="background:#f3e8ff;color:#7e22ce;padding:2px 10px;border-radius:6px;font-weight:800;font-size:14px;display:inline-block;">{{ $sData->grade }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            @endif
        </div>
    @endforeach
</div>

{{-- INTERACTIVE TEST RESULT MODAL --}}
<div id="testResultModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,0.65);backdrop-filter:blur(5px);place-items:center;padding:20px;">
    <div style="position:relative;width:min(100%, 650px);max-height:calc(100vh - 40px);background:#ffffff;border-radius:20px;padding:22px 26px;box-shadow:0 25px 60px rgba(0,0,0,0.3);display:flex;flex-direction:column;overflow:hidden;animation:securityRise 0.2s ease both;">
        <button type="button" onclick="closeTestResultModal()" style="position:absolute;top:16px;right:16px;border:none;background:#f1f5f9;width:34px;height:34px;border-radius:50%;font-size:20px;cursor:pointer;color:#64748b;display:grid;place-items:center;" title="Close">&times;</button>
        
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;flex-shrink:0;">
            <div style="width:40px;height:40px;border-radius:12px;background:#ecfdf5;color:#047857;display:grid;place-items:center;font-size:20px;"><i class="bi bi-award-fill"></i></div>
            <div>
                <h3 style="margin:0;font-size:17px;color:#0f172a;font-weight:800;" id="modalTestTitle">Monthly Test Result</h3>
                <span style="font-size:11.5px;color:#64748b;font-weight:600;" id="modalTestMonth">October 2026</span>
            </div>
        </div>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:12px 16px;margin-bottom:14px;display:grid;grid-template-columns:repeat(2, 1fr);gap:8px;font-size:12.5px;flex-shrink:0;">
            <div><small style="color:#64748b;font-weight:700;display:block;">STUDENT NAME</small><b id="modalStdName">{{ $student->name }}</b></div>
            <div><small style="color:#64748b;font-weight:700;display:block;">REGISTRATION NO</small><b style="color:#0d8f83;">{{ $stdRegNo }}</b></div>
            <div><small style="color:#64748b;font-weight:700;display:block;">ROLL NO</small><b>Roll #{{ $student->std_id }}</b></div>
            <div><small style="color:#64748b;font-weight:700;display:block;">CLASS & SECTION</small><b>{{ $student->class }} ({{ $student->section }})</b></div>
        </div>

        <div id="modalTestContent" style="flex:1;min-height:100px;max-height:220px;overflow-y:auto;border:1px solid #cbd5e1;border-radius:10px;margin-bottom:14px;">
            <!-- Dynamic table inserted by JS -->
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;background:#faf5ff;border:1px solid #e9d5ff;padding:10px 16px;border-radius:12px;margin-bottom:14px;flex-shrink:0;">
            <div><small style="display:block;font-size:10.5px;color:#7e22ce;font-weight:700;">TOTAL OBTAINED</small><strong id="modalTotalObtained" style="font-size:15px;color:#581c87;">217 / 250</strong></div>
            <div><small style="display:block;font-size:10.5px;color:#7e22ce;font-weight:700;">PERCENTAGE</small><strong id="modalPercentage" style="font-size:15px;color:#047857;">86.8%</strong></div>
            <div><small style="display:block;font-size:10.5px;color:#7e22ce;font-weight:700;">GRADE</small><span id="modalGrade" style="background:#f3e8ff;color:#7e22ce;padding:2px 8px;border-radius:6px;font-weight:800;font-size:13px;">A+</span></div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;flex-shrink:0;">
            <button type="button" onclick="closeTestResultModal()" style="background:#f1f5f9;color:#334155;border:none;padding:8px 18px;border-radius:10px;font-weight:700;font-size:12.5px;cursor:pointer;">Close</button>
            <a id="modalDownloadBtn" href="#" target="_blank" style="background:#0d8f83;color:#ffffff;text-decoration:none;padding:8px 18px;border-radius:10px;font-weight:700;font-size:12.5px;display:inline-flex;align-items:center;gap:6px;"><i class="bi bi-download"></i> Download Slip</a>
        </div>
    </div>
</div>

<script>
    window.testSessionsData = @json($testSessions);
</script>
<script src="{{ asset('js/test-sessions.js') }}?v={{ time() }}"></script>
@endsection
