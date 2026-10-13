<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>All Roll Slips - Class {{ $className }} ({{ $rollSlips->count() }} Students)</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    background: linear-gradient(135deg, #eef7f6, #f8fbfb);
    color: #18383b;
    font-family: Arial, sans-serif;
    line-height: 1.4;
}
.slip-page {
    max-width: 860px;
    margin: 28px auto 50px;
    padding: 0 18px;
}
.slip-toolbar {
    position: sticky;
    top: 14px;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 22px;
    padding: 12px 18px;
    border: 1px solid #b9d8d4;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 24px rgba(23, 61, 71, 0.12);
}
.slip-toolbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.slip-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 38px;
    padding: 0 14px;
    border: 1px solid #b9d8d4;
    border-radius: 8px;
    background: #ffffff;
    color: #0f766e;
    font: 700 13px Arial, sans-serif;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.18s ease;
}
.slip-back:hover {
    border-color: #0f766e;
    background: #f2fbf9;
    color: #075f5b;
    transform: translateX(-2px);
}
.slip-toolbar-info {
    display: flex;
    flex-direction: column;
}
.slip-toolbar-title {
    font-size: 14px;
    font-weight: 700;
    color: #164347;
}
.slip-toolbar-count {
    font-size: 12px;
    color: #648184;
}
.slip-toolbar-actions {
    display: flex;
    align-items: center;
    gap: 9px;
}
.slip-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 38px;
    padding: 0 15px;
    border: 1px solid #b9d8d4;
    border-radius: 8px;
    background: #ffffff;
    color: #0f766e;
    font: 700 13px Arial, sans-serif;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(23, 61, 71, 0.06);
    transition: all 0.18s ease;
}
.slip-action:hover {
    border-color: #0f766e;
    background: #f2fbf9;
    transform: translateY(-1px);
}
.slip-action.primary {
    border-color: #0f766e;
    background: #0f766e;
    color: #ffffff;
}
.slip-action.primary:hover {
    background: #0b625c;
    border-color: #0b625c;
}

/* ROLL SLIP STYLES */
.roll-slip-sheet {
    border: 2px solid #0f766e;
    border-radius: 14px;
    background: #ffffff;
    overflow: hidden;
    box-shadow: 0 16px 36px rgba(23, 61, 71, 0.12);
    margin-bottom: 28px;
}
.slip-head {
    padding: 24px 28px;
    background: linear-gradient(135deg, #0f766e, #075f5b);
    color: #ffffff;
    text-align: center;
    position: relative;
}
.slip-head-tag {
    position: absolute;
    top: 14px;
    right: 18px;
    padding: 3px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.2);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.slip-head h1 {
    margin: 0;
    font-size: 26px;
    letter-spacing: 0.02em;
}
.slip-head p {
    margin: 6px 0 0;
    font-size: 14px;
    color: #c9ede8;
}
.student-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: #d7e6e3;
    border-bottom: 1px solid #d7e6e3;
}
.student-grid div {
    padding: 12px 16px;
    background: #ffffff;
}
.student-grid small {
    display: block;
    margin-bottom: 4px;
    color: #678082;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.student-grid strong {
    font-size: 14px;
    color: #173b3e;
}
.date-sheet {
    padding: 20px 26px 14px;
}
.date-sheet h2,
.paper-instructions h2 {
    margin: 0 0 12px;
    color: #0f766e;
    font-size: 17px;
}
.date-sheet table {
    width: 100%;
    border-collapse: collapse;
}
.date-sheet th,
.date-sheet td {
    padding: 10px 12px;
    border: 1px solid #d7e6e3;
    text-align: left;
    font-size: 13px;
}
.date-sheet th {
    background: #eef8f6;
    color: #0f6059;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.04em;
}
.paper-instructions {
    margin: 4px 26px 0;
    padding: 14px 18px;
    border: 1px solid #d7e6e3;
    border-radius: 8px;
    background: #f7fbfa;
}
.paper-instructions h2 {
    margin-bottom: 8px;
    font-size: 15px;
}
.paper-instructions ol {
    margin: 0;
    padding-left: 20px;
    color: #476366;
    font-size: 12px;
    line-height: 1.6;
}
.slip-note {
    margin: 16px 26px 22px;
    color: #607879;
    font-size: 12px;
    text-align: center;
}

/* SCREEN DIVIDER */
.sheet-divider-screen {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 24px 0 34px;
    position: relative;
}
.sheet-divider-screen::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    border-top: 2px dashed #b9d8d4;
    z-index: 1;
}
.sheet-divider-screen span {
    position: relative;
    z-index: 2;
    padding: 4px 14px;
    border-radius: 999px;
    background: #e6f4f1;
    color: #0f766e;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.03em;
}

/* PRINT MEDIA */
@page {
    size: A4 portrait;
    margin: 8mm;
}
@media print {
    html, body {
        width: 100% !important;
        height: auto !important;
        background: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .slip-page {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .slip-toolbar,
    .sheet-divider-screen {
        display: none !important;
    }
    .roll-slip-sheet {
        page-break-after: always !important;
        break-after: page !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin-bottom: 0 !important;
        border: 2px solid #0f766e !important;
        border-radius: 6px !important;
        box-shadow: none !important;
    }
    .roll-slip-sheet:last-child {
        page-break-after: auto !important;
        break-after: auto !important;
    }
    .slip-head {
        padding: 16px 20px !important;
        background: #0f766e !important;
        color: #ffffff !important;
    }
    .slip-head h1 { font-size: 22px !important; }
    .slip-head p { font-size: 12px !important; }
    .student-grid div { padding: 9px 12px !important; }
    .student-grid small { font-size: 9px !important; }
    .student-grid strong { font-size: 13px !important; }
    .date-sheet { padding: 12px 18px 8px !important; }
    .date-sheet h2, .paper-instructions h2 { font-size: 14px !important; margin-bottom: 6px !important; }
    .date-sheet th, .date-sheet td { padding: 7px 10px !important; font-size: 11px !important; }
    .paper-instructions { margin: 0 18px !important; padding: 10px 14px !important; }
    .paper-instructions ol { font-size: 10px !important; line-height: 1.45 !important; }
    .slip-note { margin: 10px 18px 14px !important; font-size: 10px !important; }
}

@media (max-width: 650px) {
    .slip-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    .slip-toolbar-left {
        flex-direction: column;
        align-items: flex-start;
    }
    .slip-toolbar-actions {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .student-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>
</head>
<body>

<main class="slip-page">

    @if(empty($download))
        {{-- TOP ACTION BAR --}}
        <nav class="slip-toolbar" aria-label="Roll slip document actions">
            <div class="slip-toolbar-left">
                <a class="slip-back" href="{{ route('roll-slips.index') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Back to Roll Slips</span>
                </a>
                <div class="slip-toolbar-info">
                    <span class="slip-toolbar-title">Class {{ $className }} &bull; Roll Slips</span>
                    <span class="slip-toolbar-count">Total: {{ $rollSlips->count() }} Students | One Combined PDF Document</span>
                </div>
            </div>

            <div class="slip-toolbar-actions">
                <button type="button" class="slip-action primary" onclick="window.print()">
                    <i class="bi bi-printer-fill" aria-hidden="true"></i>
                    <span>Print All / Save PDF</span>
                </button>
                <a class="slip-action" href="{{ route('roll-slips.class-download', ['class' => $className]) }}">
                    <i class="bi bi-download" aria-hidden="true"></i>
                    <span>Download</span>
                </a>
            </div>
        </nav>
    @endif

    {{-- MULTI ROLL SLIP CONTAINER --}}
    @foreach($rollSlips as $index => $rollSlip)
        <article class="roll-slip roll-slip-sheet">
            <header class="slip-head">
                <div class="slip-head-tag">Student {{ $index + 1 }} of {{ $rollSlips->count() }}</div>
                <h1>Roll Number Slip</h1>
                <p>{{ $rollSlip->exam?->name ?? 'Examination' }}{{ $rollSlip->exam?->session ? ' - '.$rollSlip->exam->session : '' }}</p>
            </header>

            <section class="student-grid">
                <div>
                    <small>Student name</small>
                    <strong>{{ $rollSlip->student_name }}</strong>
                </div>
                <div>
                    <small>Std. ID / Roll no</small>
                    <strong>{{ $rollSlip->roll_no }}</strong>
                </div>
                <div>
                    <small>Class / section</small>
                    <strong>{{ $rollSlip->class_name }} {{ $rollSlip->section_name ? '/ '.$rollSlip->section_name : '' }}</strong>
                </div>
                <div>
                    <small>Room no / Class no</small>
                    <strong>{{ $rollSlip->room_no }}</strong>
                </div>
                <div>
                    <small>Chair no</small>
                    <strong>{{ $rollSlip->chair_no }}</strong>
                </div>
                <div>
                    <small>Issued on</small>
                    <strong>{{ $rollSlip->created_at ? $rollSlip->created_at->format('d M Y') : date('d M Y') }}</strong>
                </div>
            </section>

            <section class="date-sheet">
                <h2>Examination Datesheet</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 38px;">#</th>
                            <th>Subject</th>
                            <th>Paper Date</th>
                            <th>Day</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rollSlip->datesheet ?? [] as $pIdx => $paper)
                            @php
                                $examDate = !empty($paper['exam_date']) ? \Carbon\Carbon::parse($paper['exam_date']) : null;
                            @endphp
                            <tr>
                                <td style="color:#718687;font-weight:700;">{{ $pIdx + 1 }}</td>
                                <td><strong>{{ $paper['subject_name'] ?? 'Subject' }}</strong></td>
                                <td>{{ $examDate ? $examDate->format('d M Y') : 'Date not fixed' }}</td>
                                <td>{{ $examDate ? $examDate->format('l') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:#829a9b;font-style:italic;">No datesheet subjects configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="paper-instructions">
                <h2>Paper Instructions</h2>
                <ol>
                    <li>Bring this original roll-number slip for every examination paper.</li>
                    <li>Report to your assigned room and chair at least 15 minutes before the paper start time.</li>
                    <li>Mobile phones, smart devices, notes, and unauthorized material are strictly prohibited.</li>
                    <li>Follow all instructions given by the examination invigilator throughout the exam.</li>
                </ol>
            </section>

            <p class="slip-note">Keep this slip safe until all examination papers are completed.</p>
        </article>

        @if(!$loop->last)
            <div class="sheet-divider-screen">
                <span>Page Break &bull; End of Student {{ $index + 1 }} Slip</span>
            </div>
        @endif
    @endforeach

</main>

</body>
</html>
