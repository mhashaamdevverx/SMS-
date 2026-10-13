<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Roll Slip - {{ $rollSlip->student_name }}</title>
<style>
body{margin:0;background:linear-gradient(135deg,#eef7f6,#f8fbfb);color:#18383b;font-family:Arial,sans-serif}.slip-page{max-width:820px;margin:32px auto;padding:0 18px}.slip-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}.slip-toolbar-actions{display:flex;gap:9px}.slip-back,.slip-action{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:0 14px;border:1px solid #b9d8d4;border-radius:9px;background:#fff;color:#0f766e;font:700 13px Arial;text-decoration:none;cursor:pointer;box-shadow:0 5px 14px rgba(23,61,71,.08);transition:.18s ease}.slip-back:hover,.slip-action:hover{border-color:#0f766e;background:#f2fbf9;transform:translateY(-1px)}.slip-back .arrow{font-size:19px;line-height:1}.slip-action.primary{border-color:#0f766e;background:#0f766e;color:#fff}.slip-action.primary:hover{background:#0b625c}.roll-slip{border:2px solid #0f766e;border-radius:14px;background:#fff;overflow:hidden;box-shadow:0 18px 38px rgba(23,61,71,.14)}.slip-head{padding:25px 28px;background:linear-gradient(135deg,#0f766e,#075f5b);color:#fff;text-align:center}.slip-head h1{margin:0;font-size:27px}.slip-head p{margin:7px 0 0;font-size:14px}.student-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1px;background:#d7e6e3;border-bottom:1px solid #d7e6e3}.student-grid div{padding:13px 16px;background:#fff}.student-grid small{display:block;margin-bottom:4px;color:#678082;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}.student-grid strong{font-size:15px}.date-sheet{padding:22px 28px 16px}.date-sheet h2,.paper-instructions h2{margin:0 0 13px;color:#0f766e;font-size:18px}.date-sheet table{width:100%;border-collapse:collapse}.date-sheet th,.date-sheet td{padding:11px 10px;border:1px solid #d7e6e3;text-align:left;font-size:13px}.date-sheet th{background:#eef8f6;color:#0f6059;text-transform:uppercase;font-size:11px;letter-spacing:.04em}.paper-instructions{margin:0 28px;padding:14px 16px;border:1px solid #d7e6e3;border-radius:8px;background:#f7fbfa}.paper-instructions h2{margin-bottom:8px;font-size:16px}.paper-instructions ol{margin:0;padding-left:20px;color:#476366;font-size:12px;line-height:1.65}.slip-note{margin:18px 28px 26px;color:#607879;font-size:12px;text-align:center}@page{size:A4 portrait;margin:8mm}@media print{html,body{width:auto;height:auto}body{background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}.slip-page{max-width:none;margin:0;padding:0}.slip-toolbar{display:none}.roll-slip{overflow:visible;border-radius:8px;box-shadow:none}.slip-head{padding:16px 20px}.slip-head h1{font-size:23px}.slip-head p{margin-top:4px;font-size:12px}.student-grid div{padding:8px 11px}.student-grid small{margin-bottom:2px;font-size:9px}.student-grid strong{font-size:13px}.date-sheet{padding:13px 18px 9px}.date-sheet h2,.paper-instructions h2{margin-bottom:8px;font-size:15px}.date-sheet th,.date-sheet td{padding:7px 8px;font-size:11px}.paper-instructions{margin:0 18px;padding:9px 12px}.paper-instructions h2{font-size:14px}.paper-instructions ol{font-size:10px;line-height:1.4}.slip-note{margin:11px 18px 14px;font-size:10px}}@media(max-width:560px){.slip-page{margin:18px auto;padding:0 10px}.slip-toolbar{align-items:stretch;flex-direction:column}.slip-toolbar-actions{display:grid;grid-template-columns:1fr 1fr}.slip-back,.slip-action{width:auto}.student-grid{grid-template-columns:1fr}.slip-head{padding:20px}.date-sheet{padding:18px}.paper-instructions{margin:0 18px}.date-sheet th,.date-sheet td{padding:9px 7px;font-size:12px}}
</style>
</head>
<body>
<main class="slip-page">
@if(empty($download))
    <nav class="slip-toolbar" aria-label="Roll slip actions">
        <a class="slip-back" href="{{ route('roll-slips.index') }}"><span class="arrow" aria-hidden="true">&larr;</span><span>Back to Roll Slips</span></a>
        <div class="slip-toolbar-actions">
            <button type="button" class="slip-action" data-print-roll-slip>Print</button>
            <a class="slip-action primary" href="{{ route('roll-slips.download', $rollSlip) }}">Download</a>
        </div>
    </nav>
@endif
<article class="roll-slip">
<header class="slip-head"><h1>Roll Number Slip</h1><p>{{ $rollSlip->exam?->name }}{{ $rollSlip->exam?->session ? ' - '.$rollSlip->exam->session : '' }}</p></header>
<section class="student-grid"><div><small>Student name</small><strong>{{ $rollSlip->student_name }}</strong></div><div><small>Std. ID / Roll no</small><strong>{{ $rollSlip->roll_no }}</strong></div><div><small>Class / section</small><strong>{{ $rollSlip->class_name }} / {{ $rollSlip->section_name }}</strong></div><div><small>Room no / Class no</small><strong>{{ $rollSlip->room_no }}</strong></div><div><small>Chair no</small><strong>{{ $rollSlip->chair_no }}</strong></div><div><small>Issued on</small><strong>{{ $rollSlip->created_at?->format('d M Y') }}</strong></div></section>
<section class="date-sheet"><h2>Examination Datesheet</h2><table><thead><tr><th>Subject</th><th>Paper Date</th></tr></thead><tbody>@forelse($rollSlip->datesheet ?? [] as $paper)<tr><td>{{ $paper['subject_name'] }}</td><td>{{ \Carbon\Carbon::parse($paper['exam_date'])->format('D, d M Y') }}</td></tr>@empty<tr><td colspan="2">No subjects assigned for this datesheet.</td></tr>@endforelse</tbody></table></section>
<section class="paper-instructions"><h2>Paper Instructions</h2><ol><li>Bring this roll-number slip for every examination paper.</li><li>Report to your assigned room and chair at least 15 minutes before the paper.</li><li>Mobile phones, notes and unauthorized material are not allowed in the examination room.</li><li>Follow the invigilator's instructions throughout the examination.</li></ol></section>
<p class="slip-note">Keep this slip safe until all examination papers are completed.</p>
</article>
</main>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>