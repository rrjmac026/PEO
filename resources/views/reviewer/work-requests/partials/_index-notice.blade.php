@php
$noticeConfig = [
    'site_inspector'    => ['color' => 'blue',   'icon' => 'fa-hard-hat',         'action' => 'inspection findings'],
    'surveyor'          => ['color' => 'purple',  'icon' => 'fa-drafting-compass', 'action' => 'survey findings'],
    'resident_engineer' => ['color' => 'green',   'icon' => 'fa-hard-hat',         'action' => 'engineer review'],
    'engineeriv'        => ['color' => 'amber',   'icon' => 'fa-clipboard-check',  'action' => 'MTQA check'],
    'engineeriii'       => ['color' => 'orange',  'icon' => 'fa-thumbs-up',        'action' => 'recommending approval'],
    'provincial_engineer'=> ['color' => 'yellow', 'icon' => 'fa-user-tie',         'action' => 'provincial engineer approval'],
];
$config = $noticeConfig[$role] ?? null;
@endphp

@if($config)
    <div class="wri-notice {{ $config['color'] }}">
        <p class="text-sm">
            <i class="fas {{ $config['icon'] }} mr-2"></i>
            You can submit <strong>{{ $config['action'] }}</strong> on each work request.
        </p>
    </div>
@endif