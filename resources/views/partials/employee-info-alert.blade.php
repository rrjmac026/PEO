@php
    $user = Auth::user();
    $missingEmployeeInfo = $user->role !== 'admin' && !$user->employee;
    $incompleteEmployeeInfo = $user->role !== 'admin'
        && $user->employee
        && (!$user->employee->position || !$user->employee->employee_number || !$user->employee->phone);
@endphp

@if($missingEmployeeInfo || $incompleteEmployeeInfo)
<div id="employee-info-alert"
     style="margin: 0 1.5rem 1.5rem; border-radius: 12px; overflow: hidden; border: 1px solid #fde68a; background: #fffbeb;">
    <div style="padding: 16px 20px; display: flex; align-items: flex-start; gap: 16px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-id-badge" style="color: #d97706; font-size: 18px;"></i>
        </div>
        <div style="flex: 1; min-width: 0;">
            <p style="font-size: 14px; font-weight: 700; color: #92400e; margin: 0 0 4px;">
                @if($missingEmployeeInfo)
                    Employee profile not set up
                @else
                    Employee profile incomplete
                @endif
            </p>
            <p style="font-size: 13px; color: #b45309; margin: 0;">
                @if($missingEmployeeInfo)
                    Your employee details are missing. These are required for work requests and official documents.
                @else
                    Some employee details are missing (e.g. position, employee number, phone). Please complete your profile.
                @endif
            </p>
            <div style="margin-top: 12px;">
                <a href="{{ route('profile.edit') }}#tab-employee"
                   style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; background: #d97706; color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: background 0.2s;"
                   onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
                    <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                    Complete My Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endif