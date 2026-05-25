@if($workRequest->canEdit())
    <div class="wrd-danger-zone">
        <div class="wrd-danger-text">
            <h4>Delete this work request</h4>
            <p>This action is permanent and cannot be undone.</p>
        </div>
        <form action="{{ route('user.work-requests.destroy', $workRequest) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this work request? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="wrd-btn-danger">
                <i class="fas fa-trash"></i> Delete Request
            </button>
        </form>
    </div>
@endif