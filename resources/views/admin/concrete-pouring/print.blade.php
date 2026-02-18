<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concrete Pouring Request #{{ $concretePouring->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 13px;
        }
        .section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .section h2 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #ff9800;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .grid-item {
            display: flex;
            flex-direction: column;
        }
        .grid-item label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .grid-item .value {
            font-size: 13px;
            color: #333;
            font-weight: normal;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .status-box {
            padding: 10px;
            border-radius: 3px;
            margin: 10px 0;
            font-size: 12px;
        }
        .status-approved {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status-disapproved {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status-requested {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .remarks {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #ff9800;
            font-size: 12px;
            margin: 10px 0;
        }
        .checklist {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .checklist-item {
            display: flex;
            align-items: center;
            font-size: 12px;
            padding: 8px;
            background-color: #f9f9f9;
            border-radius: 3px;
        }
        .checklist-item .check {
            width: 15px;
            height: 15px;
            margin-right: 8px;
            border: 1px solid #ddd;
            text-align: center;
            font-weight: bold;
            color: green;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background-color: #4caf50;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: white;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Company Header --}}
        <div class="company-info">
            <h2>{{ config('app.name', 'PEO') }}</h2>
            <p>Concrete Pouring Request Form</p>
        </div>

        {{-- Main Header --}}
        <div class="header">
            <h1>Concrete Pouring Request #{{ $concretePouring->id }}</h1>
            <p>Date: {{ $concretePouring->created_at?->format('M d, Y') }}</p>
        </div>

        {{-- Status --}}
        <div class="section">
            @switch($concretePouring->status)
                @case('approved')
                    <div class="status-box status-approved">
                        ✓ APPROVED by {{ $concretePouring->approver?->user?->name ?? 'N/A' }} on {{ $concretePouring->approved_date?->format('M d, Y') }}
                    </div>
                    @break
                @case('disapproved')
                    <div class="status-box status-disapproved">
                        ✗ DISAPPROVED by {{ $concretePouring->disapprover?->user?->name ?? 'N/A' }} on {{ $concretePouring->disapproved_date?->format('M d, Y') }}
                    </div>
                    @break
                @default
                    <div class="status-box status-requested">
                        ⏱ PENDING APPROVAL
                    </div>
            @endswitch
        </div>

        {{-- Project Information --}}
        <div class="section">
            <h2>Project Information</h2>
            <div class="grid">
                <div class="grid-item">
                    <label>Project Name</label>
                    <div class="value">{{ $concretePouring->project_name }}</div>
                </div>
                <div class="grid-item">
                    <label>Location</label>
                    <div class="value">{{ $concretePouring->location }}</div>
                </div>
                <div class="grid-item">
                    <label>Contractor</label>
                    <div class="value">{{ $concretePouring->contractor }}</div>
                </div>
                <div class="grid-item">
                    <label>Part of Structure</label>
                    <div class="value">{{ $concretePouring->part_of_structure }}</div>
                </div>
                <div class="grid-item">
                    <label>Station Limits/Section</label>
                    <div class="value">{{ $concretePouring->station_limits_section }}</div>
                </div>
                <div class="grid-item">
                    <label>Estimated Volume (m³)</label>
                    <div class="value">{{ $concretePouring->estimated_volume }}</div>
                </div>
                <div class="grid-item">
                    <label>Pouring Date & Time</label>
                    <div class="value">{{ $concretePouring->pouring_datetime?->format('M d, Y H:i') }}</div>
                </div>
                <div class="grid-item">
                    <label>Requested By</label>
                    <div class="value">{{ $concretePouring->requestedBy?->user?->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Checklist Progress --}}
        <div class="section">
            <h2>Checklist Progress</h2>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $concretePouring->checklist_progress }}%">
                    {{ $concretePouring->checklist_progress }}%
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="section">
            <h2>Review Status</h2>
            <div class="grid-2">
                <div class="grid-item">
                    <label>ME/MTQA Review</label>
                    <div class="value">
                        @if($concretePouring->me_mtqa_checked_by)
                            ✓ Completed<br/>
                            Reviewed by: {{ $concretePouring->meMtqaChecker?->user?->name ?? 'N/A' }}<br/>
                            Date: {{ $concretePouring->me_mtqa_date?->format('M d, Y') }}
                        @else
                            ✗ Pending
                        @endif
                    </div>
                </div>
                <div class="grid-item">
                    <label>Resident Engineer Review</label>
                    <div class="value">
                        @if($concretePouring->re_checked_by)
                            ✓ Completed<br/>
                            Reviewed by: {{ $concretePouring->residentEngineer?->user?->name ?? 'N/A' }}<br/>
                            Date: {{ $concretePouring->re_date?->format('M d, Y') }}
                        @else
                            ✗ Pending
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Approval Details --}}
        @if($concretePouring->approval_remarks)
            <div class="section">
                <h2>Approval Remarks</h2>
                <div class="remarks">
                    {{ $concretePouring->approval_remarks }}
                </div>
            </div>
        @endif

        {{-- Provincial Engineer Note --}}
        @if($concretePouring->noted_by)
            <div class="section">
                <h2>Provincial Engineer Note</h2>
                <div class="grid-item">
                    <label>Noted By</label>
                    <div class="value">{{ $concretePouring->notedByEngineer?->user?->name ?? 'N/A' }}</div>
                </div>
                <div class="grid-item">
                    <label>Date</label>
                    <div class="value">{{ $concretePouring->noted_date?->format('M d, Y') }}</div>
                </div>
            </div>
        @endif

        {{-- Print Footer --}}
        <div style="margin-top: 40px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #ddd; padding-top: 15px;">
            <p>This document was automatically generated on {{ now()->format('M d, Y H:i A') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
