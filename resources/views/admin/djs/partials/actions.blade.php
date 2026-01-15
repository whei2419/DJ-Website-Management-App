<div class="btn-group" role="group" aria-label="Actions">
    <a href="{{ route('admin.djs.edit', $dj) }}" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    <form method="POST" action="{{ route('admin.djs.destroy', $dj) }}" class="d-inline delete-dj-form">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-dj" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>