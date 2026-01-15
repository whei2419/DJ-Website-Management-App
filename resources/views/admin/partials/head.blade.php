{{-- Admin head partial: assets and head-only includes --}}
@vite(['resources/js/admin.js','resources/sass/admin.scss'])
@include('components.font-awesome')
<link rel="stylesheet" href="//cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">
<style>
  /* Small UI polish for admin toasts and icon buttons moved here from layout */
  .btn-icon { width:34px; height:34px; padding:6px; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; }
  #tablerToasts .admin-toast { background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(15,23,42,0.08); display:flex; align-items:flex-start; padding:12px; gap:12px; border-left:6px solid transparent; }
  #tablerToasts .admin-toast .admin-toast-icon { font-size:20px; line-height:1; }
  #tablerToasts .admin-toast .admin-toast-body .title { font-weight:600; font-size:13px; }
  #tablerToasts .admin-toast .admin-toast-body .message { font-size:13px; color:#475569; }
</style>
