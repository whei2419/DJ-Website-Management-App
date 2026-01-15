{{-- Admin head partial: assets and head-only includes --}}
<!-- Load jQuery and DataTables early so Vite bundles can depend on them -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  console.log('head loaded');
</script>
@vite(['resources/sass/admin.scss'])
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap5.min.css">
<style>
  /* Small UI polish for admin toasts and icon buttons moved here from layout */
  .btn-icon { width:34px; height:34px; padding:6px; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; }
  #tablerToasts .admin-toast { background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(15,23,42,0.08); display:flex; align-items:flex-start; padding:12px; gap:12px; border-left:6px solid transparent; }
  #tablerToasts .admin-toast .admin-toast-icon { font-size:20px; line-height:1; }
  #tablerToasts .admin-toast .admin-toast-body .title { font-weight:600; font-size:13px; }
  #tablerToasts .admin-toast .admin-toast-body .message { font-size:13px; color:#475569; }
  /* Avatar initial styling */
  .avatar-initial { width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; background:#0f1724; color:#fff; font-weight:700; }
</style>

<style>
  /* Modern table styling */
  .table-modern {
    border-collapse: separate;
    border-spacing: 0 8px;
    background: transparent;
  }
  .table-modern thead th {
    background: transparent;
    border-bottom: none;
    color: #475569;
    font-size: 14px;
    padding: 12px 16px;
    letter-spacing: .04em;
    text-transform: uppercase;
    font-weight: 600;
  }
  .table-modern tbody tr {
    background: #fff;
    box-shadow: 0 6px 18px rgba(15,23,42,0.03);
    border-radius: 8px;
  }
  .table-modern td {
    vertical-align: middle;
    border-top: none !important;
    border-bottom: none !important;
    padding: 12px 16px;
  }
  .date-pill {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 999px;
    background: linear-gradient(90deg,#eef2ff,#fff);
    color: #0f1724;
    font-weight: 600;
    font-size: 13px;
  }
  .table-modern .dataTables_wrapper .dataTables_filter input {
    border-radius: 6px; border:1px solid #e6e9ef; padding:6px 10px;
  }
  .btn-icon { width:34px; height:34px; padding:6px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; }
  .btn-ghost { background: transparent; border: 1px solid rgba(15,23,42,0.06); }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background:#0b5ed7; color:#fff; border-radius:6px; padding:6px 10px;
  }
</style>

<style>
  /* Tabler-style compact search */
  .tabler-search { position: relative; display: inline-block; }
  .tabler-search .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9aa4b2; pointer-events: none; }
  .tabler-search input.tabler-input {
    padding-left: 36px;
    padding-right: 12px;
    height: 38px;
    border-radius: 8px;
    border: 1px solid #e6e9ef;
    background: #fff;
    box-shadow: none;
  }
  .tabler-search input.tabler-input:focus { outline: none; box-shadow: 0 0 0 4px rgba(11,93,215,0.06); border-color: #0b5ed7; }
</style>

<style>
  /* Icons for table headers */
  table.table-modern thead th i {
    font-size: 13px;
    color: #9aa4b2;
    vertical-align: middle;
  }

  /* Style DataTables length and search for compact modern controls */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .dataTables_wrapper .dataTables_length label,
  .dataTables_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #475569;
    font-size: 13px;
  }

  .dataTables_wrapper .dataTables_length select {
    min-width: 88px;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid #e6e9ef;
    background: #fff;
  }

  .dataTables_wrapper .dataTables_filter input {
    padding: 6px 10px 6px 34px;
    border-radius: 8px;
    border: 1px solid #e6e9ef;
    background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%239aa4b2" viewBox="0 0 24 24"><path d="M21.53 20.47l-3.66-3.66A8 8 0 1 0 4 11a8 8 0 0 0 13.81 5.87l3.66 3.66a.75.75 0 1 0 1.06-1.06zM6.5 11A4.5 4.5 0 1 1 11 15.5 4.5 4.5 0 0 1 6.5 11z"/></svg>') no-repeat 8px center;
    background-size: 16px 16px;
  }

  /* Reduce label text for length control */
  .dataTables_wrapper .dataTables_length label span {
    font-weight: 600;
  }
</style>
