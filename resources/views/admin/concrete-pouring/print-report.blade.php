<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concrete Pouring Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .filter-info {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #ff9800;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .summary-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .summary-card h3 {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .summary-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #333;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #333;
        }
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 13px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-disapproved {
            color: #dc3545;
            font-weight: bold;
        }
        .status-requested {
            color: #ffc107;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>Concrete Pouring Report</h1>
            <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>

        {{-- Filter Information --}}
        <div class="filter-info">
            <strong>Report Generated:</strong> {{ now()->format('M d, Y H:i A') }}
        </div>

        {{-- Summary Statistics --}}
        <div class="summary">
            <div class="summary-card">
                <h3>Total Requests</h3>
                <div class="value">{{ $summary['total_requests'] }}</div>
            </div>
            <div class="summary-card">
                <h3>Approved</h3>
                <div class="value" style="color: #28a745;">{{ $summary['approved'] }}</div>
            </div>
            <div class="summary-card">
                <h3>Disapproved</h3>
                <div class="value" style="color: #dc3545;">{{ $summary['disapproved'] }}</div>
            </div>
            <div class="summary-card">
                <h3>Pending</h3>
                <div class="value" style="color: #ffc107;">{{ $summary['pending'] }}</div>
            </div>
        </div>

        {{-- Detailed Table --}}
        <h2 style="margin-bottom: 15px; font-size: 16px;">Detailed List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Project Name</th>
                    <th>Location</th>
                    <th>Contractor</th>
                    <th>Pouring Date</th>
                    <th>Volume (m³)</th>
                    <th>Status</th>
                    <th>Requested By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($concretePourings as $pouring)
                    <tr>
                        <td>{{ $pouring->id }}</td>
                        <td>{{ $pouring->project_name }}</td>
                        <td>{{ $pouring->location }}</td>
                        <td>{{ $pouring->contractor }}</td>
                        <td>{{ $pouring->pouring_datetime?->format('M d, Y') }}</td>
                        <td>{{ $pouring->estimated_volume }}</td>
                        <td>
                            @switch($pouring->status)
                                @case('approved')
                                    <span class="status-approved">Approved</span>
                                    @break
                                @case('disapproved')
                                    <span class="status-disapproved">Disapproved</span>
                                    @break
                                @default
                                    <span class="status-requested">Pending</span>
                            @endswitch
                        </td>
                        <td>{{ $pouring->requestedBy?->user?->name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999;">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="footer">
            <p>This is an automatically generated report. Generated on {{ now()->format('M d, Y H:i A') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
