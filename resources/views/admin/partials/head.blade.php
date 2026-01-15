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
