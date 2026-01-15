<div class="btn-group" role="group" aria-label="Actions">
    <form method="POST" action="{{ route('admin.dates.destroy', $date) }}" class="d-inline delete-date-form">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-date" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" aria-label="Delete date">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>