<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Slip - {{ $feeCollection->voucher_no ?? 'Voucher' }} - {{ $feeCollection->student_name ?? 'Student' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            background: #eef2f6;
            color: #000000;
            padding: 24px;
        }
        .print-toolbar {
            max-width: 1150px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .print-btn {
            background: #1e40af;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.15s;
        }
        .print-btn:hover {
            background: #1d4ed8;
        }
        .back-btn {
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-btn:hover {
            color: #0f172a;
        }

        /* 2 SLIPS WRAPPER */
        .voucher-wrapper {
            display: flex;
            justify-content: center;
            gap: 22px;
            max-width: 1150px;
            margin: 0 auto;
        }
        .slip-box {
            flex: 1;
            max-width: 550px;
            background: #ffffff;
            border: 1.5px solid #000000;
            padding: 20px 22px;
            color: #000000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        /* HEADER */
        .slip-header {
            text-align: center;
            margin-bottom: 14px;
        }
        .school-name {
            font-size: 21px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #000000;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .school-address {
            font-size: 10.5px;
            line-height: 1.35;
            color: #000000;
            margin-top: 3px;
        }
        .copy-title {
            font-size: 13px;
            font-weight: 700;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* METADATA ROWS */
        .slip-meta {
            margin-bottom: 12px;
            font-size: 13.5px;
        }
        .meta-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 7px;
            line-height: 1.3;
        }
        .meta-col-left {
            flex: 1;
            display: flex;
            align-items: flex-end;
            padding-right: 12px;
        }
        .meta-col-right {
            flex: 1;
            display: flex;
            align-items: flex-end;
        }
        .meta-label {
            font-weight: 600;
            white-space: nowrap;
            margin-right: 6px;
            color: #000;
        }
        .line-val {
            flex: 1;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            padding-left: 4px;
            font-weight: 600;
            color: #000;
            min-height: 18px;
        }
        .line-val.full {
            width: 100%;
        }
        .comp-box {
            border: 1.5px solid #000;
            padding: 1px 16px;
            font-weight: 700;
            letter-spacing: 1px;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }

        /* TABLE */
        .slip-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 12px;
            font-size: 13.5px;
        }
        .slip-table td {
            border: 1px solid #000;
            padding: 5px 10px;
            vertical-align: middle;
        }
        .slip-table .col-particular {
            width: 65%;
            color: #000;
        }
        .slip-table .col-amount {
            width: 35%;
            text-align: right;
            font-weight: 600;
            color: #000;
            padding-right: 14px;
        }
        .highlight-row {
            font-weight: 700;
        }
        .sub-text {
            font-size: 11px;
            font-weight: normal;
            margin-left: 2px;
        }

        /* NOTE */
        .slip-note {
            font-size: 13px;
            margin-top: 8px;
            color: #000;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm 6mm;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .voucher-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                gap: 15px !important;
            }
            .slip-box {
                flex: 1 !important;
                max-width: 49% !important;
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                padding: 14px 16px !important;
                page-break-inside: avoid;
            }
            .school-name {
                font-size: 19px !important;
            }
            .slip-table td {
                padding: 4px 8px !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <a href="{{ url()->previous() ?: route('admin.page', 'fees') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <button type="button" onclick="window.print()" class="print-btn">
            <i class="bi bi-printer-fill"></i> Print Fee Slips (2 Copies)
        </button>
    </div>

    @php
        $student = $feeCollection->student;
        $compNo = $feeCollection->std_id ?: ($student?->std_id ?: $feeCollection->student_id);
        $studentName = strtoupper($feeCollection->student_name ?: ($student?->name ?? 'N/A'));
        $fatherName = strtoupper($feeCollection->father_name ?: ($student?->father_name ?? 'N/A'));
        $className = strtoupper($feeCollection->student_class ?: ($student?->class ?? '-'));
        $sectionName = strtoupper($feeCollection->student_section ?: ($student?->section ?? '-'));
        $voucherNo = $feeCollection->voucher_no ?: ('V-' . $compNo . '-' . date('my'));

        $issueDateRaw = $feeCollection->issue_date ?: ($feeCollection->created_at ? $feeCollection->created_at->toDateString() : date('Y-m-d'));
        $dueDateRaw = $feeCollection->due_date ?: date('Y-m-10');
        $issueDate = \Carbon\Carbon::parse($issueDateRaw)->format('d-m-Y');
        $dueDate = \Carbon\Carbon::parse($dueDateRaw)->format('d-m-Y');
        $dueFormattedForNote = \Carbon\Carbon::parse($dueDateRaw)->format('jS F Y');

        $month = $feeCollection->month ?: date('M - Y');
        $mobNo = $feeCollection->mobile_no ?: ($student?->phone_no ?: ($student?->emergency_phone ?? '-'));

        $monthlyFee = (float) ($feeCollection->tuition_fee > 0 ? $feeCollection->tuition_fee : ($feeCollection->amount ?? 0));
        $arrears = (float) ($feeCollection->arrears ?? 0);
        $admissionFee = (float) ($feeCollection->admission_fee ?? 0);
        $lateFee = (float) ($feeCollection->late_fee > 0 ? $feeCollection->late_fee : 500);
        $discount = (float) ($feeCollection->discount ?? 0);

        $withinDueDate = max(0, ($monthlyFee + $arrears + $admissionFee) - $discount);
        $afterDueDate = $withinDueDate + $lateFee;

        $copies = [
            'SCHOOL COPY',
            'STUDENT COPY',
        ];
    @endphp

    <div class="voucher-wrapper">
        @foreach($copies as $copyTitle)
            <div class="slip-box">
                {{-- School Header --}}
                <div class="slip-header">
                    <div class="school-name">QAZI GRAMMAR GIRLS HIGH SCHOOL</div>
                    <div class="school-address">Koh-e-Noor Housing Scheme,Qainchi Amer Sidhu Lahore Ph-042-35919480-82,35919485-87,35825777,Mob:-0301-7799777,0320-1117555.</div>
                    <div class="copy-title">({{ $copyTitle }})</div>
                </div>

                {{-- Meta Information Fields --}}
                <div class="slip-meta">
                    {{-- Row 1: Comp No & Month --}}
                    <div class="meta-row">
                        <div class="meta-col-left">
                            <span class="meta-label">Comp.No:</span>
                            <span class="comp-box">{{ $compNo }}</span>
                        </div>
                        <div class="meta-col-right">
                            <span class="meta-label">Month:-</span>
                            <span class="line-val">{{ $month }}</span>
                        </div>
                    </div>

                    {{-- Row 2: Student's Name --}}
                    <div class="meta-row">
                        <span class="meta-label">Student's Name:</span>
                        <span class="line-val full">{{ $studentName }}</span>
                    </div>

                    {{-- Row 3: Father's Name --}}
                    <div class="meta-row">
                        <span class="meta-label">Father's Name:</span>
                        <span class="line-val full">{{ $fatherName }}</span>
                    </div>

                    {{-- Row 4: Class & Section --}}
                    <div class="meta-row">
                        <div class="meta-col-left">
                            <span class="meta-label">Class:</span>
                            <span class="line-val">{{ $className }}</span>
                        </div>
                        <div class="meta-col-right">
                            <span class="meta-label">Section:</span>
                            <span class="line-val">{{ $sectionName }}</span>
                        </div>
                    </div>

                    {{-- Row 5: Voucher No & Issue Date (Replacing Campus as requested) --}}
                    <div class="meta-row">
                        <div class="meta-col-left">
                            <span class="meta-label">Voucher No:</span>
                            <span class="line-val">{{ $voucherNo }}</span>
                        </div>
                        <div class="meta-col-right">
                            <span class="meta-label">Issue Date:</span>
                            <span class="line-val">{{ $issueDate }}</span>
                        </div>
                    </div>

                    {{-- Row 6: Mob.No & Due Date --}}
                    <div class="meta-row">
                        <div class="meta-col-left">
                            <span class="meta-label">Mob.No:</span>
                            <span class="line-val">{{ $mobNo }}</span>
                        </div>
                        <div class="meta-col-right">
                            <span class="meta-label">Due Date:</span>
                            <span class="line-val">{{ $dueDate }}</span>
                        </div>
                    </div>
                </div>

                {{-- Particulars & Amounts Table --}}
                <table class="slip-table">
                    <tbody>
                        <tr>
                            <td class="col-particular">Monthly Fee</td>
                            <td class="col-amount">{{ number_format($monthlyFee) }}</td>
                        </tr>
                        <tr>
                            <td class="col-particular">Arrears</td>
                            <td class="col-amount">{{ $arrears > 0 ? number_format($arrears) : ($monthlyFee > 0 ? '0' : '-') }}</td>
                        </tr>
                        <tr>
                            <td class="col-particular">Admission Fee</td>
                            <td class="col-amount">{{ $admissionFee > 0 ? number_format($admissionFee) : '-' }}</td>
                        </tr>
                        @if($discount > 0)
                        <tr>
                            <td class="col-particular">Concession / Discount</td>
                            <td class="col-amount">-{{ number_format($discount) }}</td>
                        </tr>
                        @endif
                        <tr class="highlight-row">
                            <td class="col-particular">
                                <strong>Total Receivable Amount</strong>
                                <span class="sub-text">(Within the due date)</span>
                            </td>
                            <td class="col-amount">
                                <strong>{{ number_format($withinDueDate) }}</strong>
                            </td>
                        </tr>
                        <tr class="highlight-row">
                            <td class="col-particular">
                                <strong>Total Receivable Amount</strong>
                                <span class="sub-text">(After the due date)</span>
                            </td>
                            <td class="col-amount">
                                <strong>{{ number_format($afterDueDate) }}/-</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Note & Dues Instruction (No Stamp) --}}
                <div class="slip-note">
                    <strong>Note:</strong> Pay your dues by {{ $dueFormattedForNote }}.
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
