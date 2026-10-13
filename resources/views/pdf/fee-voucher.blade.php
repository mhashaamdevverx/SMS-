<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Voucher - {{ $feeCollection->voucher_no }} - {{ $feeCollection->student_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            padding: 20px;
        }
        .print-toolbar {
            max-width: 1100px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .print-btn {
            background: #4F46E5;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .print-btn:hover {
            background: #4338CA;
        }
        .back-btn {
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .voucher-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .voucher-copy {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 16px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .copy-tag {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            background: #e2e8f0;
            color: #475569;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .school-header {
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .school-logo {
            width: 44px;
            height: 44px;
            margin-bottom: 4px;
        }
        .school-title {
            font-size: 15px;
            font-weight: 800;
            color: #1E1B4B;
        }
        .school-sub {
            font-size: 11px;
            color: #64748b;
        }
        .voucher-info-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .voucher-info-table td {
            padding: 3px 0;
        }
        .fee-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 12px;
        }
        .fee-breakdown-table th, .fee-breakdown-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
        }
        .fee-breakdown-table th {
            background: #f1f5f9;
            text-align: left;
            font-weight: 700;
            color: #334155;
        }
        .total-row {
            font-weight: 800;
            background: #f0fdf4;
            color: #065f46;
        }
        .signature-row {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
        .sig-box {
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
            width: 45%;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .print-toolbar {
                display: none !important;
            }
            .voucher-grid {
                max-width: 100%;
                gap: 10px;
            }
            .voucher-copy {
                border: 1px solid #000;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-toolbar">
        <a href="{{ url()->previous() ?: route('student.page', 'fees') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back to Portal / Fee Dues</a>
        <button type="button" onclick="window.print()" class="print-btn">
            <i class="bi bi-printer-fill"></i> Download / Print Fee Challan
        </button>
    </div>

    @php
        $copies = ['Bank Copy', 'School Copy', 'Student Copy'];
        $isPaid = strtolower(trim($feeCollection->status)) === 'paid';
        $dueDate = $feeCollection->due_date ? \Carbon\Carbon::parse($feeCollection->due_date) : null;
        $overdueDays = 0;
        $lateFine = 0;
        if (!$isPaid && $dueDate && now()->greaterThan($dueDate)) {
            $overdueDays = (int) $dueDate->diffInDays(now());
            $lateFine = $overdueDays * 500;
        }
        $totalPayable = $feeCollection->net_amount + $lateFine;
    @endphp

    <div class="voucher-grid">
        @foreach($copies as $copyName)
            <div class="voucher-copy" style="position:relative;overflow:hidden;">
                @if($isPaid)
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-25deg);border:4px solid #16a34a;color:#16a34a;font-size:32px;font-weight:900;padding:6px 20px;border-radius:12px;opacity:0.25;letter-spacing:0.15em;pointer-events:none;text-transform:uppercase;">
                        PAID & CLEARED
                    </div>
                @endif

                <span class="copy-tag">{{ $copyName }}</span>

                <div class="school-header">
                    <img src="{{ asset('upload/Logo.png') }}" alt="Logo" class="school-logo">
                    <div class="school-title">AKM SCHOOL SYSTEM</div>
                    <div class="school-sub">Official Student Fee Voucher / Receipt</div>
                </div>

                <table class="voucher-info-table">
                    <tr>
                        <td><strong>Voucher #:</strong></td>
                        <td style="text-align: right; color: #0d9488; font-weight: 700;">{{ $feeCollection->voucher_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Student:</strong></td>
                        <td style="text-align: right;">{{ $feeCollection->student_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Std ID / Class:</strong></td>
                        <td style="text-align: right;">#{{ $feeCollection->std_id ?? 'N/A' }} ({{ $feeCollection->student_class }} - {{ $feeCollection->student_section ?? 'A' }})</td>
                    </tr>
                    <tr>
                        <td><strong>Month:</strong></td>
                        <td style="text-align: right;">{{ $feeCollection->month }}</td>
                    </tr>
                    <tr>
                        <td><strong>Due Date (10th):</strong></td>
                        <td style="text-align: right; color: #dc2626; font-weight: 700;">{{ $dueDate ? $dueDate->format('d M Y') : '10th of Month' }}</td>
                    </tr>
                </table>

                <table class="fee-breakdown-table">
                    <thead>
                        <tr>
                            <th>Fee Particulars</th>
                            <th style="text-align: right;">Amount (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tuition Fee</td>
                            <td style="text-align: right;">{{ number_format($feeCollection->tuition_fee) }}</td>
                        </tr>
                        @if($feeCollection->admission_fee > 0)
                            <tr>
                                <td>Admission Fee</td>
                                <td style="text-align: right;">{{ number_format($feeCollection->admission_fee) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Exam Fee</td>
                            <td style="text-align: right;">{{ number_format($feeCollection->exam_fee) }}</td>
                        </tr>
                        @if($feeCollection->lab_sports_fee > 0)
                            <tr>
                                <td>Lab / Sports Fee</td>
                                <td style="text-align: right;">{{ number_format($feeCollection->lab_sports_fee) }}</td>
                            </tr>
                        @endif
                        @if($feeCollection->other_charges > 0)
                            <tr>
                                <td>Other Dues / Service Fine</td>
                                <td style="text-align: right;">{{ number_format($feeCollection->other_charges) }}</td>
                            </tr>
                        @endif
                        @if($lateFine > 0)
                            <tr style="color:#dc2626;font-weight:700;">
                                <td>Late Fine ({{ $overdueDays }} days @ Rs 500/day)</td>
                                <td style="text-align: right;">+ {{ number_format($lateFine) }}</td>
                            </tr>
                        @endif
                        @if($feeCollection->discount > 0)
                            <tr style="color: #047857;">
                                <td>Concession / Discount</td>
                                <td style="text-align: right;">-{{ number_format($feeCollection->discount) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Total Amount Payable</td>
                            <td style="text-align: right;">Rs. {{ number_format($totalPayable) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div style="font-size: 10.5px; color: #475569; margin-top: 8px; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <strong>Payment Status:</strong>
                    @if($isPaid)
                        <span style="color: #15803d; font-weight: 800;"><i class="bi bi-check-circle-fill"></i> PAID CLEARED</span>
                        @if($feeCollection->payment_date)
                            <small style="display:block;color:#64748b;margin-top:2px;">Paid Date: {{ \Carbon\Carbon::parse($feeCollection->payment_date)->format('d M Y') }} ({{ $feeCollection->payment_method ?: 'Online Transfer' }})</small>
                        @endif
                    @else
                        <span style="color: #b45309; font-weight: 800;"><i class="bi bi-clock-history"></i> UNPAID / PENDING</span>
                        <small style="display:block;color:#dc2626;margin-top:2px;">Please pay before 10th to avoid Rs 500/day fine.</small>
                    @endif
                </div>

                <div class="signature-row">
                    <div class="sig-box">Depositor Sig.</div>
                    <div class="sig-box">Officer / Cashier</div>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
