{{-- Component: Badge for user roles --}}
@props(['role' => 'admin', 'label' => null])

@php
    $roleLabel = $label ?? match($role) {
        'admin'               => 'Admin',
        'contractor'          => 'Contractor',
        'resident_engineer'   => 'Resident Engineer',
        'provincial_engineer' => 'Provincial Engineer',
        'mtqa'                => 'ME/MTQA',
        default               => ucwords(str_replace('_', ' ', $role)),
    };
@endphp

<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium tag-role-{{ str_replace('_', '-', $role) }}"
      {{ $attributes }}>
    {{ $roleLabel }}
</span>
