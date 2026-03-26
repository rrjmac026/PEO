{{-- resources/views/reviewer/work-requests/partials/_index-notice.blade.php --}}
@php
$noticeConfig = [
    'site_inspector'     => ['color' => 'blue',   'icon' => 'fa-hard-hat',         'action' => 'inspection findings'],
    'surveyor'           => ['color' => 'purple',  'icon' => 'fa-drafting-compass', 'action' => 'survey findings'],
    'resident_engineer'  => ['color' => 'green',   'icon' => 'fa-hard-hat',         'action' => 'engineer review'],
    'mtqa'               => ['color' => 'amber',   'icon' => 'fa-clipboard-check',  'action' => 'MTQA check — and can print approved requests'],
    'engineeriv'         => ['color' => 'amber',   'icon' => 'fa-user-check',       'action' => 'Engineer IV review'],
    'engineeriii'        => ['color' => 'orange',  'icon' => 'fa-thumbs-up',        'action' => 'recommending approval'],
    'provincial_engineer'=> ['color' => 'yellow',  'icon' => 'fa-gavel',            'action' => 'final approve or reject decision'],
];
$config = $noticeConfig[$role] ?? null;
@endphp

@if($config)
    <div class="wri-notice {{ $config['color'] }}">
        <p class="text-sm">
            <i class="fas {{ $config['icon'] }} mr-2"></i>
            @if($role === 'provincial_engineer')
                You are the <strong>final decision maker</strong>. You can
                <strong>{{ $config['action'] }}</strong> on each assigned work request.
            @elseif($role === 'mtqa')
                You can submit <strong>MTQA checks</strong> on assigned requests,
                and <strong>print / download</strong> any work request that has been
                approved by the Provincial Engineer.
            @else
                You can submit <strong>{{ $config['action'] }}</strong> on each assigned work request.
            @endif
        </p>
    </div>
@endif