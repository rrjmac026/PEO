<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Work Request</title>
<style>

@page {
    size: A4 portrait;
    margin: 0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    width: 210mm;
    background: #fff;
    font-family: Arial, sans-serif;
    font-size: 8pt;
    color: #000;
}

.paper {
    width: 210mm;
    min-height: 297mm;
    padding: 9mm 11mm 8mm 11mm;
    background: #fff;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.t-header {
    width: 188mm;
    border-collapse: collapse;
    margin-bottom: 2.5mm;
}
.t-header td { padding: 0; vertical-align: middle; }
.th-seal,
.th-logo  { width: 22mm; text-align: center; }
.th-seal img,
.th-logo img { width: 20mm; height: 20mm; display: block; margin: 0 auto; }
.th-mid   { width: 144mm; text-align: center; }
.th-mid .line1 { font-size: 8pt; }
.th-mid .line2 { font-size: 11pt; font-weight: bold; }
.th-mid .line3 { font-size: 8pt; }

.t-proj {
    width: 188mm;
    border-collapse: collapse;
    margin-bottom: 2mm;
}
.t-proj td { font-size: 8.5pt; padding: 0.4mm 1mm; vertical-align: top; }
.tp-lbl    { width: 26mm; white-space: nowrap; }
.tp-sep    { width: 4mm;  text-align: center; }

/* BANNER — blue */
.banner {
    width: 188mm;
    background: #00b0f0;
    color: #fff;
    text-align: center;
    font-size: 11pt;
    font-weight: bold;
    padding: 2mm 0 0.3mm;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* BANNER2 — white */
.banner2 {
    width: 188mm;
    background: #fff;
    color: #000;
    text-align: center;
    font-size: 7.5pt;
    padding-bottom: 1.5mm;
}

.t-form {
    width: 188mm;
    border-collapse: collapse;
    border: 0.3mm solid #000;
    table-layout: fixed;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.t-form td {
    border: 0.3mm solid #000;
    padding: 1mm 1.5mm;
    vertical-align: top;
    word-wrap: break-word;
    overflow: hidden;
}
.col-a { width: 72mm; }
.col-b { width: 58mm; }
.col-c { width: 58mm; }

.lbl { font-size: 7pt; color: #555; display: block; margin-bottom: 0.3mm; }
.val { font-size: 8.5pt; display: block; min-height: 3.8mm; }
.bld { font-weight: bold; }

.band {
    background: #00b0f0;
    color: #fff;
    font-weight: bold;
    font-size: 9pt;
    text-align: center;
    padding: 1.2mm;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.note { font-size: 6.5pt; font-style: italic; margin-top: 1mm; }

.sig-cell {
    height: 16mm;
    text-align: center;
    vertical-align: bottom !important;
    padding-bottom: 1mm !important;
}
.sig-name {
    display: inline-block;
    min-width: 44mm;
    border-top: 0.3mm solid #000;
    font-size: 8pt;
    font-weight: bold;
    padding-top: 0.5mm;
}
.sig-role {
    font-size: 6.5pt;
    display: block;
    margin-top: 0.5mm;
}

.fnd { height: 16mm; vertical-align: top !important; }

.apv-cell {
    height: 17mm;
    vertical-align: top;
    padding-top: 1mm !important;
}
.apv-lbl { font-size: 7.5pt; font-weight: bold; display: block; }
.apv-sig  { display: block; text-align: center; padding-top: 4mm; }

.no-break { page-break-inside: avoid; }

.t-items {
    width: 188mm;
    border-collapse: collapse;
    table-layout: fixed;
}
.t-items th,
.t-items td {
    border: 0.3mm solid #000;
    padding: 1mm 1.5mm;
    font-size: 8pt;
    vertical-align: top;
    word-wrap: break-word;
}
.t-items th {
    background: #00b0f0;
    color: #fff;
    font-weight: bold;
    text-align: center;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.t-items .c-no   { width: 12mm; text-align: center; }
.t-items .c-desc { width: 80mm; }
.t-items .c-eq   { width: 46mm; }
.t-items .c-qty  { width: 25mm; text-align: right; }
.t-items .c-unit { width: 25mm; text-align: center; }

@media screen {
    body { background: #e0e0e0; display: flex; justify-content: center; padding: 10mm 0; }
    .paper { box-shadow: 0 0 8mm rgba(0,0,0,.3); }
}
@media print {
    html, body { background: #fff; display: block; padding: 0; }
}
</style>
</head>
<body>
<div class="paper">

{{-- ══════════════════════════════════════════
     SECTION 1: AGENCY HEADER
     ══════════════════════════════════════════ --}}
<table class="t-header">
<tr>
    <td class="th-seal">
        @if(file_exists(public_path('assets/province_seal.png')))
            <img src="{{ asset('assets/province_seal.png') }}" alt="Province Seal">
        @endif
    </td>
    <td class="th-mid">
        <div class="line1">Republic of the Philippines</div>
        <div class="line2">PROVINCE OF BUKIDNON</div>
        <div class="line3">Provincial Capitol 8700</div>
    </td>
    <td class="th-logo">
        @if(file_exists(public_path('assets/app_logo.PNG')))
            <img src="{{ asset('assets/app_logo.PNG') }}" alt="App Logo">
        @endif
    </td>
</tr>
</table>

{{-- ══════════════════════════════════════════
     SECTION 2: PROJECT DETAILS
     ══════════════════════════════════════════ --}}
<br>
<table class="t-proj">
<tr>
    <td class="tp-lbl">Name of Project</td>
    <td class="tp-sep">:</td>
    <td>{{ $workRequest->name_of_project }}</td>
</tr>
<tr>
    <td class="tp-lbl">Project Location</td>
    <td class="tp-sep">:</td>
    <td>{{ $workRequest->project_location }}</td>
</tr>
</table>

{{-- ══════════════════════════════════════════
     BANNER
     ══════════════════════════════════════════ --}}
<div class="banner">WORK REQUEST</div>
<div class="banner2">(In Triplicate)</div>

{{-- ══════════════════════════════════════════
     SECTION 3: MAIN FORM TABLE
     ══════════════════════════════════════════ --}}
<table class="t-form">
<colgroup>
    <col class="col-a">
    <col class="col-b">
    <col class="col-c">
</colgroup>

{{-- ── ROW: For | Requested Work to Start On ── --}}
<tr>
    <td>
        <span class="lbl">For :</span>
        <span class="val bld">{{ $workRequest->for_office ?? 'PROVINCIAL ENGINEERS OFFICE' }}</span>
    </td>
    <td colspan="2">
        <span class="lbl">Requested Work to Start on</span>
        <table style="width:100%;border-collapse:collapse;margin-top:0.5mm;">
        <tr>
            <td style="border:none;padding:0;width:60%;">
                <span class="lbl">Date :</span>
                <span class="val">{{ $workRequest->requested_work_start_date
                    ? \Carbon\Carbon::parse($workRequest->requested_work_start_date)->format('M d, Y')
                    : '' }}</span>
            </td>
            <td style="border:none;padding:0;width:40%;">
                <span class="lbl">Time :</span>
                <span class="val">{{ $workRequest->requested_work_start_time ?? '' }}</span>
            </td>
        </tr>
        </table>
        <div class="note">Note: has to submit request in triplicate and with a minimum of 72 hours in advance of scheduled start</div>
    </td>
</tr>

{{-- ── ROW: From ── --}}
<tr>
    <td colspan="3">
        <span class="lbl">From :</span>
        <span class="val">{{ $workRequest->from_requester }}</span>
    </td>
</tr>

{{-- ── PAY ITEM HEADER ── --}}
<tr>
    <td colspan="3" class="band">PAY ITEM REQUESTED</td>
</tr>

{{-- ── ROW: Item No | Equipment / Qty / Unit ── --}}
<tr>
    <td>
        <span class="lbl">Item No. :</span>
        <span class="val">{{ $workRequest->item_no }}</span>
    </td>
    <td colspan="2">
        <table style="width:100%;border-collapse:collapse;">
        <tr>
            <td style="border:none;padding:0;width:52%;">
                <span class="lbl">Equipment to be used :</span>
                <span class="val">{{ $workRequest->equipment_to_be_used }}</span>
            </td>
            <td style="border:none;padding:0 0 0 2mm;width:24%;">
                <span class="lbl">Quantity :</span>
                <span class="val">{{ $workRequest->quantity }}</span>
            </td>
            <td style="border:none;padding:0 0 0 2mm;width:24%;">
                <span class="lbl">Unit :</span>
                <span class="val">{{ $workRequest->unit }}</span>
            </td>
        </tr>
        </table>
    </td>
</tr>

{{-- ── ROW: Description | Estimated Quantity ── --}}
<tr>
    <td>
        <span class="lbl">Description :</span>
        <span class="val">{{ $workRequest->description }}</span>
    </td>
    <td colspan="2">
        <span class="lbl">Estimated Quantity to be Accomplished :</span>
        <span class="val">{{ $workRequest->estimated_quantity }}</span>
    </td>
</tr>

{{-- ── ROW: Description of Work Requested ── --}}
<tr>
    <td colspan="3" style="height:18mm; vertical-align:top;">
        <span class="lbl">Description of Work Requested :</span>
        <span class="val">{{ $workRequest->description_of_work_requested }}</span>
    </td>
</tr>

{{-- ── ROW: Submitted By | Received By ── --}}
<tr>
    <td>
        <span class="lbl">Submitted by :</span>
        <span class="val">{{ $workRequest->submitted_by ?? '' }}</span>
        <div style="font-size:6.5pt; margin-top:3mm;">Contractor</div>
    </td>
    <td colspan="2">
        <span class="lbl">Received By :</span>
        <table style="width:100%;border-collapse:collapse;margin-top:0.5mm;">
        <tr>
            <td style="border:none;padding:0;width:44%;">
                <span class="val bld">{{ $workRequest->received_by ?? '' }}</span>
            </td>
            <td style="border:none;padding:0 0 0 2mm;width:28%;">
                <span class="lbl">Date :</span>
                <span class="val bld">{{ $workRequest->received_date
                    ? \Carbon\Carbon::parse($workRequest->received_date)->format('m/d/Y')
                    : '' }}</span>
            </td>
            <td style="border:none;padding:0 0 0 2mm;width:28%;">
                <span class="lbl">Time :</span>
                <span class="val bld">{{ $workRequest->received_time ?? '' }}</span>
            </td>
        </tr>
        </table>
    </td>
</tr>

{{-- ── INSPECTION COLUMN HEADERS ── --}}
<tr>
    <td style="padding-bottom:0.5mm;">
        <span class="lbl">Inspected by :</span>
    </td>
    <td style="text-align:center; padding-bottom:0.5mm;">
        <span class="lbl">Findings/Comments</span>
    </td>
    <td style="text-align:center; padding-bottom:0.5mm;">
        <span class="lbl">Recommendation</span>
    </td>
</tr>

{{-- ── Site Inspector ── --}}
<tr>
    <td class="sig-cell">
        <span class="sig-name">{{ $workRequest->inspected_by_site_inspector ?? '' }}</span>
        <span class="sig-role">Signature Over Printed Name<br>Site Inspector</span>
    </td>
    <td class="fnd"><span class="val">{{ $workRequest->findings_comments ?? '' }}</span></td>
    <td class="fnd"><span class="val">{{ $workRequest->recommendation ?? '' }}</span></td>
</tr>

{{-- ── Surveyor ── --}}
<tr>
    <td class="sig-cell">
        <span class="sig-name">{{ $workRequest->surveyor_name ?? '' }}</span>
        <span class="sig-role">Signature Over Printed Name<br>Surveyor</span>
    </td>
    <td class="fnd"><span class="val">{{ $workRequest->findings_surveyor ?? '' }}</span></td>
    <td class="fnd"><span class="val">{{ $workRequest->recommendation_surveyor ?? '' }}</span></td>
</tr>

{{-- ── Resident Engineer ── --}}
<tr>
    <td class="sig-cell">
        <span class="sig-name">{{ $workRequest->resident_engineer_name ?? '' }}</span>
        <span class="sig-role">Signature Over Printed Name<br>Resident Engineer/Project In-Charge</span>
    </td>
    <td class="fnd"><span class="val">{{ $workRequest->findings_engineer ?? '' }}</span></td>
    <td class="fnd"><span class="val">{{ $workRequest->recommendation_engineer ?? '' }}</span></td>
</tr>

{{-- ── Checked By | Recommended Action ── --}}
<tr>
    <td style="padding-bottom:0.5mm;"><span class="lbl">Checked by :</span></td>
    <td colspan="2" style="text-align:center; padding-bottom:0.5mm;"><span class="lbl">Recommended Action</span></td>
</tr>
<tr>
    <td class="sig-cell">
        <span class="sig-name">{{ $workRequest->checked_by_mtqa ?? '' }}</span>
        <span class="sig-role">MTQA (assigned)</span>
    </td>
    <td colspan="2" style="vertical-align:top; height:16mm;">
        <span class="val">{{ $workRequest->recommended_action ?? '' }}</span>
    </td>
</tr>

{{-- ── Reviewed By ── --}}
<tr class="no-break">
    <td class="apv-cell">
        <span class="apv-lbl">Reviewed by :</span>
        <span class="apv-sig">
            <span class="sig-name">{{ $workRequest->reviewed_by ?? 'RANDY P. DIAZ' }}</span>
            <span class="sig-role">Engineer IV/Chief, MTQC Division</span>
        </span>
    </td>
    <td colspan="2" style="vertical-align:top;">
        <span class="val">{{ $workRequest->reviewed_by_notes ?? '' }}</span>
    </td>
</tr>

{{-- ── Recommending Approval ── --}}
<tr class="no-break">
    <td class="apv-cell">
        <span class="apv-lbl">Recommending Approval :</span>
        <span class="apv-sig">
            <span class="sig-name">{{ $workRequest->recommending_approval_by ?? 'SANITA E. MAIZA' }}</span>
            <span class="sig-role">Engineer III/ OIC, Construction Division</span>
        </span>
    </td>
    <td colspan="2" style="vertical-align:top;">
        <span class="val">{{ $workRequest->recommending_approval_notes ?? '' }}</span>
    </td>
</tr>

{{-- ── Approved ── --}}
<tr class="no-break">
    <td class="apv-cell" style="height:20mm;">
        <span class="apv-lbl">Approved :</span>
        <span class="apv-sig">
            <span class="sig-name">{{ $workRequest->approved_by ?? 'DELIA E. DAMASCO' }}</span>
            <span class="sig-role">Provincial Engineer</span>
        </span>
    </td>
    <td colspan="2" style="vertical-align:top;">
        <span class="val">{{ $workRequest->approved_notes ?? '' }}</span>
    </td>
</tr>

{{-- ── Accepted By ── --}}
<tr>
    <td>
        <span class="lbl">Accepted by :</span>
        <span class="val bld">{{ $workRequest->accepted_by_contractor ?? '' }}</span>
        <div style="font-size:6.5pt; margin-top:2mm;">Contractor</div>
    </td>
    <td colspan="2">
        <table style="width:100%;border-collapse:collapse;margin-top:0.5mm;">
        <tr>
            <td style="border:none;padding:0;width:50%;">
                <span class="lbl">Date :</span>
                <span class="val bld">{{ $workRequest->accepted_date
                    ? \Carbon\Carbon::parse($workRequest->accepted_date)->format('m/d/Y')
                    : '' }}</span>
            </td>
            <td style="border:none;padding:0 0 0 3mm;width:50%;">
                <span class="lbl">Time :</span>
                <span class="val bld">{{ $workRequest->accepted_time ?? '' }}</span>
            </td>
        </tr>
        </table>
    </td>
</tr>

</table>{{-- /t-form --}}


{{-- ══════════════════════════════════════════
     SECTION 4 (OPTIONAL): DYNAMIC ITEMS TABLE
     Uncomment if $workRequest->items relation exists.
     ══════════════════════════════════════════

@if($workRequest->items && $workRequest->items->count())
<table class="t-items" style="margin-top:3mm;">
<thead>
<tr>
    <th class="c-no">No.</th>
    <th class="c-desc">Description</th>
    <th class="c-eq">Equipment</th>
    <th class="c-qty">Quantity</th>
    <th class="c-unit">Unit</th>
</tr>
</thead>
<tbody>
@foreach($workRequest->items as $i => $item)
<tr>
    <td class="c-no">{{ $i + 1 }}</td>
    <td class="c-desc">{{ $item->description }}</td>
    <td class="c-eq">{{ $item->equipment }}</td>
    <td class="c-qty">{{ number_format($item->quantity, 2) }}</td>
    <td class="c-unit">{{ $item->unit }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

--}}


</div>{{-- /paper --}}

<script>
window.addEventListener('load', function () {
    window.print();
});
</script>
</body>
</html>