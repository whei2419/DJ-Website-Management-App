<div class="btn-group" role="group" aria-label="Actions">
    <form method="POST" action="{{ route('admin.dates.destroy', $date) }}" class="d-inline delete-date-form">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-icon btn-danger btn-delete-date" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>