<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Request #{{ $workRequest->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }

        .container {
            max-width: 8.5in;
            height: 11in;
            margin: 0 auto;
            padding: 0.5in;
            position: relative;
        }

        header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 0.3in;
            margin-bottom: 0.3in;
        }

        .title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 0.1in;
        }

        .subtitle {
            font-size: 10pt;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 0.2in;
        }

        .status-badge.draft {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .status-badge.submitted {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .status-badge.inspected {
            background-color: #e9d5ff;
            color: #6b21a8;
            border: 1px solid #d8b4fe;
        }

        .status-badge.reviewed {
            background-color: #e0e7ff;
            color: #312e81;
            border: 1px solid #c7d2fe;
        }

        .status-badge.approved {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .status-badge.rejected {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        section {
            margin-bottom: 0.25in;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            background-color: #f3f4f6;
            padding: 0.1in 0.15in;
            margin-bottom: 0.15in;
            border-left: 3px solid #333;
        }

        .row {
            display: flex;
            margin-bottom: 0.1in;
            page-break-inside: avoid;
        }

        .col {
            flex: 1;
            margin-right: 0.15in;
        }

        .col:last-child {
            margin-right: 0;
        }

        .field-label {
            font-weight: bold;
            color: #666;
            font-size: 10pt;
        }

        .field-value {
            padding: 0.08in 0;
            border-bottom: 1px solid #e5e7eb;
            min-height: 0.2in;
        }

        .full-width {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.15in;
        }

        th {
            background-color: #f3f4f6;
            padding: 0.08in;
            border: 1px solid #d1d5db;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }

        td {
            padding: 0.08in;
            border: 1px solid #d1d5db;
        }

        .footer {
            position: absolute;
            bottom: 0.3in;
            left: 0.5in;
            right: 0.5in;
            font-size: 9pt;
            color: #999;
            text-align: center;
            border-top: 1px solid #d1d5db;
            padding-top: 0.1in;
        }

        .signature-line {
            display: inline-block;
            width: 2in;
            border-bottom: 1px solid #333;
            text-align: center;
            margin-right: 0.3in;
        }

        .whitespace-pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100%;
                height: auto;
                padding: 0.5in;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="title">WORK REQUEST</div>
            <div class="status-badge {{ $workRequest->status }}">
                {{ ucfirst($workRequest->status) }}
            </div>
            <div class="subtitle">Request ID: #{{ $workRequest->id }} | Date: {{ $workRequest->created_at->format('M d, Y') }}</div>
        </header>

        <!-- Project Information -->
        <section>
            <div class="section-title">PROJECT INFORMATION</div>
            <div class="row">
                <div class="col" style="flex: 1.5;">
                    <div class="field-label">Project Name</div>
                    <div class="field-value">{{ $workRequest->name_of_project }}</div>
                </div>
                <div class="col">
                    <div class="field-label">For Office</div>
                    <div class="field-value">{{ $workRequest->for_office ?? '-' }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col" style="flex: 1.5;">
                    <div class="field-label">Project Location</div>
                    <div class="field-value">{{ $workRequest->project_location }}</div>
                </div>
                <div class="col">
                    <div class="field-label">From Requester</div>
                    <div class="field-value">{{ $workRequest->from_requester ?? '-' }}</div>
                </div>
            </div>
        </section>

        <!-- Request Details -->
        <section>
            <div class="section-title">REQUEST DETAILS</div>
            <div class="row">
                <div class="col">
                    <div class="field-label">Requested By</div>
                    <div class="field-value">{{ $workRequest->requested_by }}</div>
                </div>
                <div class="col">
                    <div class="field-label">Start Date</div>
                    <div class="field-value">{{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '-' }}</div>
                </div>
                <div class="col">
                    <div class="field-label">Start Time</div>
                    <div class="field-value">{{ $workRequest->requested_work_start_time ?? '-' }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col full-width">
                    <div class="field-label">Description of Work Requested</div>
                    <div class="field-value whitespace-pre" style="min-height: 0.4in;">{{ $workRequest->description_of_work_requested }}</div>
                </div>
            </div>
        </section>

        <!-- Pay Item Details -->
        <section>
            <div class="section-title">PAY ITEM DETAILS</div>
            <div class="row">
                <div class="col">
                    <div class="field-label">Item Number</div>
                    <div class="field-value">{{ $workRequest->item_no ?? '-' }}</div>
                </div>
                <div class="col">
                    <div class="field-label">Equipment to be Used</div>
                    <div class="field-value">{{ $workRequest->equipment_to_be_used ?? '-' }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="field-label">Estimated Quantity</div>
                    <div class="field-value">{{ $workRequest->estimated_quantity ?? '-' }}</div>
                </div>
                <div class="col">
                    <div class="field-label">Unit</div>
                    <div class="field-value">{{ $workRequest->unit ?? '-' }}</div>
                </div>
            </div>
            @if($workRequest->description)
                <div class="row">
                    <div class="col full-width">
                        <div class="field-label">Description</div>
                        <div class="field-value whitespace-pre" style="min-height: 0.3in;">{{ $workRequest->description }}</div>
                    </div>
                </div>
            @endif
        </section>

        <!-- Submission Details -->
        <section>
            <div class="section-title">SUBMISSION DETAILS</div>
            <div class="row">
                <div class="col">
                    <div class="field-label">Contractor Name</div>
                    <div class="field-value">{{ $workRequest->contractor_name ?? '-' }}</div>
                </div>
                <div class="col">
                    <div class="field-label">Submitted Date</div>
                    <div class="field-value">{{ $workRequest->submitted_date?->format('M d, Y') ?? '-' }}</div>
                </div>
            </div>
            @if($workRequest->notes)
                <div class="row">
                    <div class="col full-width">
                        <div class="field-label">Additional Notes</div>
                        <div class="field-value whitespace-pre" style="min-height: 0.25in;">{{ $workRequest->notes }}</div>
                    </div>
                </div>
            @endif
        </section>

        <div class="footer">
            Generated on {{ now()->format('M d, Y H:i:s') }} | Work Request System
        </div>
    </div>
</body>
</html>
