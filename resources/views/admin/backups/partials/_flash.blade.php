{{-- resources/views/admin/backups/partials/_flash.blade.php --}}
@if(session('success'))
    <div class="bp-alert success" id="flash-alert">
        <span class="bp-alert-icon"><i class="fas fa-check-circle"></i></span>
        <span class="bp-alert-body">{{ session('success') }}</span>
        <button class="bp-alert-close" onclick="this.closest('.bp-alert').remove()"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="bp-alert error" id="flash-alert">
        <span class="bp-alert-icon"><i class="fas fa-exclamation-circle"></i></span>
        <span class="bp-alert-body">{{ session('error') }}</span>
        <button class="bp-alert-close" onclick="this.closest('.bp-alert').remove()"><i class="fas fa-times"></i></button>
    </div>
@endif