@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="my-4">Edit DJ Details</h1>

    <form action="{{ route('admin.djs.update', $dj->id) }}" method="POST" enctype="multipart/form-data" id="editDJForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">DJ Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $dj->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Existing Slots</label>
            <div id="existingSlots">
                @php $existing = json_decode($dj->slot, true) ?? []; @endphp
                @if(is_array($existing) && count($existing))
                    @foreach($existing as $s)
                        <div class="d-flex align-items-center mb-2 slot-row" data-date="{{ $s['date'] }}">
                            <div class="me-2">{{ $s['date'] }}</div>
                            <input type="time" class="form-control form-control-sm me-2 slot-time" value="{{ $s['time'] }}" style="width:140px;">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-slot">Remove</button>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted">No slots assigned yet.</div>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Add slots by date range (weekends)</label>
            <div class="row g-2">
                <div class="col">
                    <input type="date" id="rangeStartEdit" class="form-control" placeholder="Start date">
                </div>
                <div class="col">
                    <input type="date" id="rangeEndEdit" class="form-control" placeholder="End date">
                </div>
                <div class="col-auto">
                    <button id="generateWeekendsEdit" type="button" class="btn btn-outline-secondary">Generate Weekends</button>
                </div>
            </div>
            <div id="weekendDatesListEdit" class="mt-3"></div>
            <input type="hidden" name="slot" id="slotInputEdit">
        </div>

        <div class="mb-3">
            <label class="form-label">Video</label>
            <div id="videoDropZoneEdit" class="border rounded p-3 text-center" style="cursor: pointer;">
                <input id="videoInputEdit" type="file" name="video" accept="video/*" class="form-control d-none">
                <div id="videoDropLabelEdit">Drag & drop a video here, paste (Ctrl/Cmd+V), or click to select</div>
                <div id="videoFileNameEdit" class="mt-2 text-muted">{{ $dj->video_path ? basename($dj->video_path) : '' }}</div>
            </div>
            @if($dj->video_path)
                <div class="mt-2">
                    <video src="{{ asset('storage/' . $dj->video_path) }}" controls style="max-width: 100%; height: auto;"></video>
                </div>
            @endif
        </div>

        

        <button type="submit" class="btn btn-success">Update DJ Details</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        var zone = document.getElementById('videoDropZoneEdit');
        var input = document.getElementById('videoInputEdit');
        var nameEl = document.getElementById('videoFileNameEdit');
        if (!zone) return;
        zone.addEventListener('click', function(){ input.click(); });
        input.addEventListener('change', function(){ var f = input.files[0]; nameEl.textContent = f ? f.name + ' (' + Math.round(f.size/1024/1024) + 'MB)' : ''; });
        zone.addEventListener('dragover', function(e){ e.preventDefault(); zone.classList.add('bg-light'); });
        zone.addEventListener('dragleave', function(e){ zone.classList.remove('bg-light'); });
        zone.addEventListener('drop', function(e){ e.preventDefault(); zone.classList.remove('bg-light'); var f = e.dataTransfer.files && e.dataTransfer.files[0]; if (f) { input.files = e.dataTransfer.files; nameEl.textContent = f.name + ' (' + Math.round(f.size/1024/1024) + 'MB)'; } });
        document.addEventListener('paste', function(e){ var items = e.clipboardData && e.clipboardData.items; if (!items) return; for (var i=0;i<items.length;i++){ var item = items[i]; if (item.kind === 'file' && item.type.indexOf('video') === 0){ var blob = item.getAsFile(); var dt = new DataTransfer(); dt.items.add(blob); input.files = dt.files; nameEl.textContent = blob.name || 'pasted-video'; } } });
    })();
</script>
@endpush

@push('scripts')
<script>
    (function(){
        function pad(n){ return n<10? '0'+n : n; }
        function formatDate(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }

        var genBtn = document.getElementById('generateWeekendsEdit');
        var list = document.getElementById('weekendDatesListEdit');
        var start = document.getElementById('rangeStartEdit');
        var end = document.getElementById('rangeEndEdit');
        var slotInput = document.getElementById('slotInputEdit');
        var form = document.getElementById('editDJForm');
        var existing = document.getElementById('existingSlots');

        function makeRow(dstr){
            var row = document.createElement('div'); row.className = 'd-flex align-items-center mb-2 slot-row'; row.dataset.date = dstr;
            var left = document.createElement('div'); left.className = 'me-2'; left.textContent = dstr;
            var time = document.createElement('input'); time.type = 'time'; time.className = 'form-control form-control-sm me-2 slot-time'; time.style.width = '140px'; time.value = '20:00'; time.dataset.date = dstr;
            var btn = document.createElement('button'); btn.type='button'; btn.className='btn btn-sm btn-outline-danger remove-slot'; btn.textContent='Remove';
            btn.addEventListener('click', function(){ row.remove(); });
            row.appendChild(left); row.appendChild(time); row.appendChild(btn);
            return row;
        }

        if (genBtn){
            genBtn.addEventListener('click', function(){
                list.innerHTML = '';
                if (!start.value || !end.value){ alert('Please select both start and end dates'); return; }
                var s = new Date(start.value); var e = new Date(end.value);
                if (e < s){ alert('End date must be after start date'); return; }
                var cur = new Date(s); var found=0;
                while (cur <= e){ var day = cur.getDay(); if (day===6||day===0){ var dstr=formatDate(cur); var row = makeRow(dstr); list.appendChild(row); found++; } cur.setDate(cur.getDate()+1); }
                if(!found) list.innerHTML = '<div class="text-muted">No weekend dates in range</div>';
                // attach add buttons to transfer rows into existingSlots
                var generated = list.querySelectorAll('.slot-row');
                generated.forEach(function(r){
                    r.addEventListener('click', function(){ /* noop */ });
                    var addBtn = document.createElement('button'); addBtn.type='button'; addBtn.className='btn btn-sm btn-primary ms-2'; addBtn.textContent='Add';
                    addBtn.addEventListener('click', function(){
                        // clone row for existing slots
                        var clone = r.cloneNode(true);
                        // replace add button with remove
                        var rem = document.createElement('button'); rem.type='button'; rem.className='btn btn-sm btn-outline-danger remove-slot'; rem.textContent='Remove';
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
        existing && existing.addEventListener('click', function(e){ if (e.target && e.target.classList.contains('remove-slot')){ e.target.closest('.slot-row').remove(); } });

        // on submit gather all slot-row entries (both existingSlots and weekendDatesListEdit if user didn't move) and serialize
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