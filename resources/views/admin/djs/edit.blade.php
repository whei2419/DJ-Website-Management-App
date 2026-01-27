@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="my-4">Edit DJ Details</h1>

        <form action="{{ route('admin.djs.update', $dj->id) }}" method="POST" enctype="multipart/form-data" id="editDJForm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">DJ Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $dj->name }}"
                    required>
            </div>

            <input type="hidden" name="date_id" id="djDateId" value="{{ $dj->date_id }}">
            <div class="mb-3">
                <label class="form-label">Assigned Date</label>
                <div class="form-control-plaintext mb-2">
                    {{ $dj->date ? ($dj->date->date instanceof \Illuminate\Support\Carbon ? $dj->date->date->format('M d, Y') : '-') : '-' }}
                </div>
                <div class="form-text">Assigned date for this DJ. To change it, use the DJs list modal.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Video</label>
                <div id="videoDropZoneEdit" class="border rounded p-3 text-center" style="cursor: pointer;">
                    <input id="videoInputEdit" type="file" name="video" accept="video/*" class="form-control d-none">
                    <div id="videoDropLabelEdit">Drag & drop a video here, paste (Ctrl/Cmd+V), or click to select</div>
                    <div id="videoFileNameEdit" class="mt-2 text-muted">
                        {{ $dj->video_path ? basename($dj->video_path) : '' }}</div>
                </div>
                @if ($dj->video_path)
                    <div class="mt-2">
                        <video src="{{ asset('storage/' . $dj->video_path) }}" controls
                            style="max-width: 100%; height: auto;"></video>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="thumbnail" class="form-label">Thumbnail (optional)</label>
                <input type="file" name="thumbnail" id="thumbnailEdit" accept="image/*" class="form-control">
                @if ($dj->thumbnail_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $dj->thumbnail_path) }}" alt="Thumbnail"
                            style="max-width: 200px; height: auto;" />
                    </div>
                @endif
            </div>
        @endpush
        var rem = document.createElement('button'); rem.type='button';
        rem.className='btn btn-sm btn-outline-danger remove-slot'; rem.textContent='Remove';
        rem.addEventListener('click', function(){ clone.remove(); });
        // remove any existing add button
        var last = clone.querySelector('button'); if (last) last.remove(); clone.appendChild(rem);
        existing.appendChild(clone);
        });
        r.appendChild(addBtn);
        });
        });
        }

        // delegate remove buttons on existing slots
        existing && existing.addEventListener('click', function(e){ if (e.target &&
        e.target.classList.contains('remove-slot')){ e.target.closest('.slot-row').remove(); } });

        // on submit gather all slot-row entries (both existingSlots and weekendDatesListEdit if user didn't move)
        and serialize
        if (form){
        form.addEventListener('submit', function(e){
        var rows = [];
        var els = document.querySelectorAll('.slot-row');
        els.forEach(function(r){
        var date = r.dataset.date;
        var timeEl = r.querySelector('.slot-time');
        var time = timeEl ? timeEl.value : '';
        if (date){ rows.push({date: date, time: time}); }
        });
        slotInput.value = rows.length ? JSON.stringify(rows) : '';
        });
        }
        })();
        </script>
    @endpush
