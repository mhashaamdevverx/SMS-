<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Roll Slip - {{ $rollSlip->student_name }}</title>
<style>
    @page {
        size: A4 portrait;
        margin: 8mm 10mm;
    }
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #1a2e30;
        font-size: 11pt;
        line-height: 1.35;
        background: #ffffff;
    }
    .slip-container {
        border: 2px solid #0f766e;
        border-radius: 8px;
        padding: 0;
        background: #ffffff;
    }
    .slip-header {
        background-color: #1E1B4B;
        color: #ffffff;
        padding: 14px 18px;
        text-align: center;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .slip-header h1 {
        margin: 0;
        font-size: 18pt;
        letter-spacing: 0.5px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .slip-header p {
        margin: 4px 0 0 0;
        font-size: 10.5pt;
        color: #C7D2FE;
    }
    .badge-class {
        display: inline-block;
        background-color: #4F46E5;
        color: #ffffff;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 9pt;
        font-weight: 700;
        margin-top: 5px;
        letter-spacing: 0.5px;
    }
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        background: #ffffff;
        border-bottom: 1.5px solid #4F46E5;
    }
    .info-table td {
        padding: 8px 12px;
        border: 1px solid #d1e7e4;
        width: 33.33%;
        vertical-align: top;
    }
    .info-label {
        font-size: 7.5pt;
        text-transform: uppercase;
        color: #557876;
        font-weight: bold;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-value {
        font-size: 11pt;
        font-weight: bold;
        color: #0d2f30;
    }
    .section-title {
        font-size: 11pt;
        font-weight: bold;
        color: #4F46E5;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 12px 14px 6px 14px;
        border-bottom: 1px solid #4F46E5;
        padding-bottom: 3px;
    }
    .datesheet-table {
        width: calc(100% - 28px);
        margin: 0 14px 10px 14px;
        border-collapse: collapse;
    }
    .datesheet-table th {
        background-color: #EEF2FF;
        color: #4F46E5;
        font-size: 8.5pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 6px 8px;
        border: 1px solid #C7D2FE;
        text-align: left;
    }
    .datesheet-table td {
        padding: 5px 8px;
        border: 1px solid #dbeae7;
        font-size: 9.5pt;
        color: #233b3d;
    }
    .datesheet-table tr:nth-child(even) td {
        background-color: #fafdff;
    }
    .instructions-box {
        margin: 8px 14px;
        padding: 8px 12px;
        background: #F8FAFC;
        border: 1px solid #E5E7EB;
        border-radius: 5px;
    }
    .instructions-box h3 {
        margin: 0 0 4px 0;
        font-size: 9pt;
        color: #4F46E5;
        text-transform: uppercase;
    }
    .instructions-box ol {
        margin: 0;
        padding-left: 18px;
        font-size: 8pt;
        color: #385657;
        line-height: 1.35;
    }
    .signatures-table {
        width: calc(100% - 28px);
        margin: 18px 14px 10px 14px;
        border-collapse: collapse;
    }
    .signatures-table td {
        width: 50%;
        text-align: center;
        padding: 10px 20px;
        border: none;
    }
    .sig-line {
        border-top: 1px solid #233b3d;
        margin: 20px 20px 4px 20px;
    }
    .sig-label {
        font-size: 8pt;
        font-weight: bold;
        color: #49696b;
        text-transform: uppercase;
    }
    .slip-footer-note {
        text-align: center;
        font-size: 7.5pt;
        color: #6b8d8f;
        padding: 6px 14px 10px 14px;
        margin: 0;
        font-style: italic;
    }
</style>
</head>
<body>

<div class="slip-container">
    {{-- HEADER --}}
    <div class="slip-header">
        <h1>Roll Number Slip</h1>
        <p>{{ $rollSlip->exam?->name ?? 'Annual Examination' }} {{ $rollSlip->exam?->session ? '('.$rollSlip->exam->session.')' : '' }}</p>
        <span class="badge-class">CLASS: {{ $rollSlip->class_name }} {{ $rollSlip->section_name ? '('.$rollSlip->section_name.')' : '' }}</span>
    </div>

    {{-- STUDENT & SEATING DETAILS --}}
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Student Name</div>
                <div class="info-value">{{ $rollSlip->student_name }}</div>
            </td>
            <td>
                <div class="info-label">Std ID / Roll No</div>
                <div class="info-value">{{ $rollSlip->roll_no }}</div>
            </td>
            <td>
                <div class="info-label">Class &amp; Section</div>
                <div class="info-value">{{ $rollSlip->class_name }} {{ $rollSlip->section_name ? '('.$rollSlip->section_name.')' : '' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-label">Room / Hall No</div>
                <div class="info-value">{{ $rollSlip->room_no }}</div>
            </td>
            <td>
                <div class="info-label">Chair / Seat No</div>
                <div class="info-value">{{ $rollSlip->chair_no }}</div>
            </td>
            <td>
                <div class="info-label">Issued On</div>
                <div class="info-value">{{ $rollSlip->created_at ? $rollSlip->created_at->format('d M Y') : date('d M Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- EXAMINATION DATESHEET --}}
    <div class="section-title">Examination Datesheet</div>
    <table class="datesheet-table">
        <thead>
            <tr>
                <th style="width: 28px; text-align: center;">#</th>
                <th>Subject</th>
                <th style="width: 120px;">Paper Date</th>
                <th style="width: 90px;">Day</th>
                <th style="width: 110px;">Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rollSlip->datesheet ?? [] as $pIdx => $paper)
                @php
                    $examDate = !empty($paper['exam_date']) ? \Carbon\Carbon::parse($paper['exam_date']) : null;
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #587978;">{{ $pIdx + 1 }}</td>
                    <td><strong>{{ $paper['subject_name'] ?? 'Subject' }}</strong></td>
                    <td>{{ $examDate ? $examDate->format('d M Y') : 'Date not fixed' }}</td>
                    <td>{{ $examDate ? $examDate->format('l') : '—' }}</td>
                    <td>{{ $paper['start_time'] ?? '09:00 AM' }} - {{ $paper['end_time'] ?? '12:00 PM' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b8d8f; font-style: italic;">No subjects assigned in datesheet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- INSTRUCTIONS --}}
    <div class="instructions-box">
        <h3>Examination Rules &amp; Instructions</h3>
        <ol>
            <li>Bring this original printed roll-number slip to every examination session.</li>
            <li>Report to your assigned examination room and chair at least 15 minutes before the paper start time.</li>
            <li>Mobile phones, smartwatches, digital devices, and unauthorized books or notes are strictly prohibited.</li>
            <li>Strictly obey all guidelines and instructions issued by the examination invigilator.</li>
        </ol>
    </div>

    {{-- SIGNATURES --}}
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="sig-label">Student Signature</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-label">Controller of Examinations</div>
            </td>
        </tr>
    </table>

    <p class="slip-footer-note">Note: Keep this slip safe until all examinations are completed. Duplicate slips must be re-issued by administration.</p>
</div>

</body>
</html>
