<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Voucher - {{ $feeCollection->voucher_no ?? 'Slip' }} - {{ $feeCollection->student_name ?? 'Student' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }
        
        /* TOP TOOLBAR */
        .print-toolbar {
            max-width: 1160px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 22px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }
        .print-btn {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
            transition: all 0.2s;
        }
        .print-btn:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .back-btn {
            color: #475569;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.15s;
        }
        .back-btn:hover {
            color: #0f172a;
        }

        /* 2 SLIPS SIDE BY SIDE */
        .voucher-wrapper {
            display: flex;
            justify-content: center;
            gap: 24px;
            max-width: 1160px;
            margin: 0 auto;
        }
        .slip-box {
            flex: 1;
            max-width: 560px;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 10px;
            padding: 22px 24px;
            color: #0f172a;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            position: relative;
        }

        /* HEADER */
        .slip-header {
            text-align: center;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0f172a;
        }
        .school-name {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 0.8px;
            color: #0f172a;
            line-height: 1.15;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .copy-badge {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #f1f5f9;
            color: #334155;
            padding: 3px 14px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
        }

        /* TOP VOUCHER / MONTH ROW */
        .voucher-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #0f172a;
            border-radius: 8px;
            padding: 7px 12px;
            margin-bottom: 12px;
        }
        .voucher-no-box {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .v-label {
            font-size: 11.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .v-badge {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 5px;
            letter-spacing: 0.5px;
        }
        .month-box {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        /* METADATA FORM */
        .slip-meta {
            margin-bottom: 14px;
            font-size: 13px;
        }
        .meta-row {
            display: flex;
            align-items: center;
            margin-bottom: 7px;
            line-height: 1.35;
        }
        .meta-col-left {
            flex: 1;
            display: flex;
            align-items: center;
            padding-right: 12px;
        }
        .meta-col-right {
            flex: 1;
            display: flex;
            align-items: center;
        }
        .meta-label {
            font-weight: 700;
            color: #475569;
            font-size: 12px;
            white-space: nowrap;
            margin-right: 8px;
            min-width: 65px;
        }
        .meta-label.wide {
            min-width: 105px;
        }
        .line-val {
            flex: 1;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 1px;
            padding-left: 6px;
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
            min-height: 20px;
        }
        .line-val.full {
            width: 100%;
        }

        /* TABLE */
        .slip-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #0f172a;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .slip-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-bottom: 2px solid #0f172a;
            border-right: 1.5px solid #0f172a;
        }
        .slip-table th:last-child {
            border-right: none;
            text-align: right;
        }
        .slip-table td {
            border-bottom: 1px solid #cbd5e1;
            border-right: 1.5px solid #0f172a;
            padding: 6px 12px;
            vertical-align: middle;
        }
        .slip-table td:last-child {
            border-right: none;
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }
        .slip-table .col-particular {
            color: #1e293b;
            font-weight: 600;
        }
        .highlight-row {
            background: #f8fafc;
            border-top: 2px solid #0f172a;
            font-weight: 800;
        }
        .highlight-row td {
            border-bottom: 1px solid #0f172a;
            padding: 7px 12px;
        }
        .highlight-row.total-final {
            background: #eff6ff;
            border-bottom: none;
        }
        .highlight-row.total-final td {
            border-bottom: none;
            color: #1e3a8a;
            font-size: 13.5px;
        }
        .sub-text {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-left: 3px;
        }

        /* NOTE & SIGNATURES */
        .slip-note {
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            padding: 7px 12px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding: 0 10px;
        }
        .sig-col {
            text-align: center;
            width: 42%;
            border-top: 1.5px dashed #64748b;
            padding-top: 4px;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 7mm 6mm;
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
                gap: 16px !important;
            }
            .slip-box {
                flex: 1 !important;
                max-width: 49% !important;
                border: 2px solid #000000 !important;
                box-shadow: none !important;
                padding: 14px 16px !important;
                page-break-inside: avoid;
                border-radius: 0 !important;
            }
            .school-name {
                font-size: 19px !important;
                color: #000000 !important;
            }
            .slip-header, .voucher-top-bar, .slip-table {
                border-color: #000000 !important;
            }
            .slip-table th, .slip-table td {
                border-color: #000000 !important;
                padding: 4px 8px !important;
            }
            .line-val {
                border-color: #000000 !important;
            }
            .slip-note {
                border-color: #000000 !important;
                background: transparent !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <a href="{{ url()->previous() ?: route('admin.page', 'fees') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Portal
        </a>
        <button type="button" onclick="window.print()" class="print-btn">
            <i class="bi bi-printer-fill"></i> Print Fee Slips (2 Copies)
        </button>
    </div>

    @php
        $student = $feeCollection->student;
        $stdId = $feeCollection->std_id ?: ($student?->std_id ?: $feeCollection->student_id);
        $studentName = strtoupper($feeCollection->student_name ?: ($student?->name ?? 'N/A'));
        $fatherName = strtoupper($feeCollection->father_name ?: ($student?->father_name ?? 'N/A'));
        $className = strtoupper($feeCollection->student_class ?: ($student?->class ?? '-'));
        $sectionName = strtoupper($feeCollection->student_section ?: ($student?->section ?? '-'));
        
        // Guarantee unique voucher number
        $voucherNo = $feeCollection->voucher_no ?: ('V-' . $stdId . '-' . date('my') . '-' . rand(1000, 9999));

        $issueDateRaw = $feeCollection->issue_date ?: ($feeCollection->created_at ? $feeCollection->created_at->toDateString() : date('Y-m-d'));
        $dueDateRaw = $feeCollection->due_date ?: date('Y-m-10');
        $issueDate = \Carbon\Carbon::parse($issueDateRaw)->format('d-m-Y');
        $dueDate = \Carbon\Carbon::parse($dueDateRaw)->format('d-m-Y');
        $dueFormattedForNote = \Carbon\Carbon::parse($dueDateRaw)->format('jS F Y');

        $month = $feeCollection->month ?: date('F Y');
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
                {{-- School Header (Only Name & Copy Badge, No Address/Phones) --}}
                <div class="slip-header">
                    <div class="school-name">EXCELLENCE SCHOOL SYSTEM</div>
                    <div class="copy-badge">{{ $copyTitle }}</div>
                </div>

                {{-- Prominent Top Bar: Unique Voucher No & Fee Month --}}
                <div class="voucher-top-bar">
                    <div class="voucher-no-box">
                        <span class="v-label">Voucher No:</span>
                        <span class="v-badge">{{ $voucherNo }}</span>
                    </div>
                    <div class="month-box">
                        <span class="v-label">Month:</span> <strong>{{ $month }}</strong>
                    </div>
                </div>

                {{-- Meta Information Fields (No Comp.No) --}}
                <div class="slip-meta">
                    {{-- Row 1: Student's Name --}}
                    <div class="meta-row">
                        <span class="meta-label wide">Student's Name:</span>
                        <span class="line-val full">{{ $studentName }}</span>
                    </div>

                    {{-- Row 2: Father's Name --}}
                    <div class="meta-row">
                        <span class="meta-label wide">Father's Name:</span>
                        <span class="line-val full">{{ $fatherName }}</span>
                    </div>

                    {{-- Row 3: Class & Section --}}
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

                    {{-- Row 4: Issue Date & Due Date --}}
                    <div class="meta-row">
                        <div class="meta-col-left">
                            <span class="meta-label">Issue Date:</span>
                            <span class="line-val">{{ $issueDate }}</span>
                        </div>
                        <div class="meta-col-right">
                            <span class="meta-label">Due Date:</span>
                            <span class="line-val">{{ $dueDate }}</span>
                        </div>
                    </div>

                    {{-- Row 5: Roll # & Mobile No --}}
                    <div class="meta-row">
                        <div class="meta-col-left">
                            <span class="meta-label">Roll / Std ID:</span>
                            <span class="line-val">{{ $stdId ?: '-' }}</span>
                        </div>
                        <div class="meta-col-right">
                            <span class="meta-label">Mob. No:</span>
                            <span class="line-val">{{ $mobNo }}</span>
                        </div>
                    </div>
                </div>

                {{-- Particulars & Amounts Table --}}
                <table class="slip-table">
                    <thead>
                        <tr>
                            <th>Particulars / Description</th>
                            <th>Amount (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-particular">Monthly Tuition Fee</td>
                            <td>{{ number_format($monthlyFee) }}</td>
                        </tr>
                        <tr>
                            <td class="col-particular">Arrears / Previous Dues</td>
                            <td>{{ $arrears > 0 ? number_format($arrears) : '0' }}</td>
                        </tr>
                        <tr>
                            <td class="col-particular">Admission Fee</td>
                            <td>{{ $admissionFee > 0 ? number_format($admissionFee) : '0' }}</td>
                        </tr>
                        @if($discount > 0)
                        <tr>
                            <td class="col-particular" style="color: #059669;">Concession / Discount</td>
                            <td style="color: #059669;">-{{ number_format($discount) }}</td>
                        </tr>
                        @endif
                        <tr class="highlight-row">
                            <td class="col-particular">
                                Total Receivable Amount
                                <span class="sub-text">(Within due date)</span>
                            </td>
                            <td>
                                Rs. {{ number_format($withinDueDate) }}
                            </td>
                        </tr>
                        <tr class="highlight-row total-final">
                            <td class="col-particular">
                                Total Receivable Amount
                                <span class="sub-text">(After due date)</span>
                            </td>
                            <td>
                                Rs. {{ number_format($afterDueDate) }}/-
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Note & Due Date Instructions (No Stamp) --}}
                <div class="slip-note">
                    <i class="bi bi-clock-history"></i>
                    <span><strong>Note:</strong> Pay your dues by <strong>{{ $dueFormattedForNote }}</strong> to avoid late charges.</span>
                </div>

                {{-- Signatures --}}
                <div class="signature-row">
                    <div class="sig-col">Depositor's Signature</div>
                    <div class="sig-col">Cashier / Authorized Seal</div>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
