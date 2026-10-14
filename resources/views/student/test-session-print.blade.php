<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Result Slip - {{ $testSession->month_title }} | {{ $student->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f8fafc; color: #0f172a; padding: 30px 15px; }
        .slip-container { max-width: 800px; margin: 0 auto; background: #ffffff; border: 2px solid #cbd5e1; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px dashed #cbd5e1; padding-bottom: 20px; margin-bottom: 24px; }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand img { width: 56px; height: 56px; object-fit: contain; }
        .brand-text h2 { font-size: 22px; color: #4F46E5; font-weight: 800; letter-spacing: 0.02em; }
        .brand-text p { font-size: 12px; color: #64748b; font-weight: 600; }
        .doc-badge { text-align: right; }
        .doc-badge span { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 99px; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .doc-badge small { display: block; margin-top: 6px; font-size: 11px; color: #64748b; font-weight: 700; }
        
        .student-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; background: #f1f5f9; padding: 16px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
        .student-grid div small { display: block; font-size: 10.5px; color: #64748b; font-weight: 700; text-transform: uppercase; }
        .student-grid div strong { font-size: 14px; color: #0f172a; }

        .table-title { font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 13px; }
        th { background: #1E1B4B; color: #ffffff; padding: 10px 14px; text-align: left; font-weight: 700; }
        td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }

        .summary-box { display: flex; justify-content: space-between; align-items: center; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 18px 24px; margin-bottom: 28px; }
        .summary-metric small { display: block; font-size: 11px; color: #7e22ce; font-weight: 800; text-transform: uppercase; }
        .summary-metric strong { font-size: 18px; color: #581c87; font-weight: 800; }

        .signatures { display: flex; justify-content: space-between; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; }
        .sig-box { width: 200px; }
        .sig-line { border-bottom: 2px solid #0f172a; margin-bottom: 6px; }
        .sig-box small { font-size: 12px; font-weight: 700; color: #475569; }

        .actions { text-align: center; margin-top: 24px; }
        .btn-print { background: #4F46E5; color: #ffffff; border: none; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
        .btn-print:hover { background: #4338CA; }

        @media print {
            body { background: #ffffff; padding: 0; }
            .slip-container { border: none; box-shadow: none; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

<div class="slip-container">
    <div class="header">
        <div class="brand">
            <img src="{{ asset('upload/Logo.png') }}" alt="Excellence School System Logo">
            <div class="brand-text">
                <h2>EXCELLENCE SCHOOL SYSTEM</h2>
                <p>Official Monthly Test Session Result Card & Assessment Slip</p>
            </div>
        </div>
        <div class="doc-badge">
            <span>{{ $testSession->status === 'Evaluated' ? 'OFFICIAL RESULT' : 'SCHEDULE SLIP' }}</span>
            <small>Session {{ date('Y') }}</small>
        </div>
    </div>

    <div class="student-grid">
        <div>
            <small>Student Name</small>
            <strong>{{ $student->name }}</strong>
        </div>
        <div>
            <small>Registration Number</small>
            <strong style="color:#4F46E5;">{{ $stdRegNo }}</strong>
        </div>
        <div>
            <small>Roll Number</small>
            <strong>Roll #{{ $student->std_id }}</strong>
        </div>
        <div>
            <small>Class & Section</small>
            <strong>{{ $student->class }} ({{ $student->section }})</strong>
        </div>
        <div>
            <small>Test Session Month</small>
            <strong>{{ $testSession->month_title }}</strong>
        </div>
        <div>
            <small>Session Date Range</small>
            <strong>{{ \Carbon\Carbon::parse($testSession->start_date)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($testSession->end_date)->format('d M Y') }}</strong>
        </div>
    </div>

    <div class="table-title">
        <i class="bi bi-journal-check" style="color:#4F46E5;"></i> Test Session Date Sheet & Subject Performance
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Day</th>
                <th>Subject Name</th>
                <th>Timing</th>
                <th>Venue</th>
                <th style="text-align:center;">Max Marks</th>
                <th style="text-align:center;">Obtained Marks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($testSession->schedule_json ?? [] as $row)
                <tr>
                    <td>
                        <b>{{ $row['date'] }}</b>
                        <small style="display:block;color:{{ ($row['is_off'] ?? false) ? '#64748b' : '#4F46E5' }};">{{ $row['day'] }}</small>
                    </td>
                    <td>
                        @if($row['is_off'] ?? false)
                            <span style="color:#64748b;font-weight:700;">Weekend Off (No Exam)</span>
                        @else
                            <strong>{{ $row['subject'] }}</strong>
                        @endif
                    </td>
                    <td>{{ $row['time'] }}</td>
                    <td>{{ $row['venue'] }}</td>
                    <td style="text-align:center;">{{ ($row['is_off'] ?? false) ? '—' : $row['max_marks'] }}</td>
                    <td style="text-align:center;">
                        @if($row['is_off'] ?? false)
                            —
                        @elseif($testSession->status === 'Evaluated')
                            <strong style="color:#047857;">{{ $row['obtained_marks'] }} / {{ $row['max_marks'] }}</strong>
                        @else
                            <span style="color:#b45309;">Pending</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($testSession->status === 'Evaluated')
        <div class="summary-box">
            <div class="summary-metric">
                <small>Total Obtained Marks</small>
                <strong>{{ $testSession->total_obtained_marks }} / {{ $testSession->total_max_marks }}</strong>
            </div>
            <div class="summary-metric">
                <small>Percentage</small>
                <strong style="color:#047857;">{{ $testSession->percentage }}%</strong>
            </div>
            <div class="summary-metric">
                <small>Overall Grade</small>
                <strong style="color:#7e22ce;">{{ $testSession->grade }}</strong>
            </div>
            <div class="summary-metric">
                <small>Result Status</small>
                <strong style="color:#047857;">PASSED</strong>
            </div>
        </div>
    @endif

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <small>Class Teacher Signature</small>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <small>Exam Controller / Principal Stamp</small>
        </div>
    </div>

    <div class="actions">
        <button type="button" class="btn-print" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print / Save as PDF</button>
    </div>
</div>

</body>
</html>
