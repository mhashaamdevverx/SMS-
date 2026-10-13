<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $student->name }} - Results</title>
    <style>
        body { color: #18332d; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { border-bottom: 2px solid #176b51; margin-bottom: 18px; padding-bottom: 12px; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        .meta { color: #61756b; line-height: 1.7; }
        table { border-collapse: collapse; margin-top: 16px; width: 100%; }
        th { background: #176b51; color: #fff; text-align: left; }
        th, td { border: 1px solid #dce7e0; padding: 8px; }
        .empty { color: #74867c; padding: 22px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <h1>{{ $student->name }} · Academic Results</h1>
        <div class="meta">Roll No: {{ $student->std_id }} · Class: {{ $student->class }} / {{ $student->section }}<br>Generated {{ now()->format('M j, Y') }}</div>
    </header>
    <table>
        <thead><tr><th>Term</th><th>Subject</th><th>Total Marks</th><th>Obtained</th><th>Percentage</th><th>Grade</th></tr></thead>
        <tbody>
            @forelse($results as $result)
                <tr><td>{{ $result->term ?: 'First Term' }}</td><td>{{ $result->subject }}</td><td>{{ $result->total_marks }}</td><td>{{ $result->obtained_marks }}</td><td>{{ number_format((float) $result->percentage, 2) }}%</td><td>{{ $result->grade ?: '—' }}</td></tr>
            @empty
                <tr><td colspan="6" class="empty">No results have been entered yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>