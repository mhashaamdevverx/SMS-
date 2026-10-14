<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay Slip - {{ $teacher->teacher_name }} - {{ $salary->month }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #0f172a; margin: 0; padding: 40px; }
        .slip-card { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 16px; border: 1px solid #cbd5e1; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 36px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #4F46E5; padding-bottom: 20px; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 22px; color: #1E1B4B; font-weight: 800; }
        .header p { margin: 4px 0 0; color: #64748b; font-size: 13px; }
        .badge { background: #D1FAE5; color: #10B981; border: 1px solid #A7F3D0; padding: 6px 14px; border-radius: 99px; font-weight: 800; font-size: 12px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 28px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9; }
        .grid label { display: block; font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
        .grid value { display: block; font-size: 14px; font-weight: 700; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; }
        th { background: #f1f5f9; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 11px; }
        .total-row td { font-weight: 800; font-size: 16px; color: #4F46E5; background: #EEF2FF; border-top: 2px solid #4F46E5; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 32px; border-top: 1px dashed #cbd5e1; padding-top: 16px; }
        @media print {
            body { padding: 0; background: #fff; }
            .slip-card { border: none; box-shadow: none; padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div style="text-align: right; max-width: 700px; margin: 0 auto 16px;" class="no-print">
    <button onclick="window.print()" style="background: #4F46E5; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer;">
        🖨️ Print Pay Slip
    </button>
</div>

<div class="slip-card">
    <div class="header">
        <div>
            <h1>Excellence School System &bull; Salary Slip</h1>
            <p>Official Monthly Salary Disbursement Voucher</p>
        </div>
        <span class="badge">STATUS: {{ strtoupper($salary->status) }}</span>
    </div>

    <div class="grid">
        <div>
            <label>Teacher Name</label>
            <value>{{ $teacher->teacher_name }}</value>
        </div>
        <div>
            <label>Pay Period / Month</label>
            <value>{{ \Carbon\Carbon::parse($salary->month.'-01')->format('F Y') }}</value>
        </div>
        <div>
            <label>Email Address</label>
            <value>{{ $user->email }}</value>
        </div>
        <div>
            <label>Payment Date</label>
            <value>{{ $salary->paid_date ? \Carbon\Carbon::parse($salary->paid_date)->format('d M Y') : '05 Oct 2026' }}</value>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Earnings / Deductions Description</th>
                <th>Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary (Fixed)</td>
                <td>Rs {{ number_format($salary->basic_salary, 2) }}</td>
            </tr>
            <tr>
                <td>Conveyance & Medical Allowances</td>
                <td>+ Rs {{ number_format($salary->allowances, 2) }}</td>
            </tr>
            <tr>
                <td>Tax & PF Deductions</td>
                <td>- Rs {{ number_format($salary->deductions, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>NET PAYABLE DISBURSED</td>
                <td>Rs {{ number_format($salary->net_salary, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="display: flex; justify-content: space-between; margin-top: 40px;">
        <div style="text-align: center; border-top: 1px solid #94a3b8; width: 180px; padding-top: 6px; font-size: 12px; color: #475569; font-weight: 700;">
            Teacher Signature
        </div>
        <div style="text-align: center; border-top: 1px solid #94a3b8; width: 180px; padding-top: 6px; font-size: 12px; color: #475569; font-weight: 700;">
            Accounts Officer
        </div>
    </div>

    <div class="footer">
        Computer Generated Salary Slip &bull; Excellence School System &bull; Issued on {{ date('d M Y') }}
    </div>
</div>

</body>
</html>
