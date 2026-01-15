<script>
  document.addEventListener('DOMContentLoaded', function(){
    var flash = {
      success: @json(session('success')),
      error: @json(session('error')),
      info: @json(session('info')),
      warning: @json(session('warning'))
    };
    if(flash.success) window.adminToaster?.show('success', flash.success);
    if(flash.error) window.adminToaster?.show('error', flash.error);
    if(flash.info) window.adminToaster?.show('info', flash.info);
    if(flash.warning) window.adminToaster?.show('warning', flash.warning);
  });
</script>
<!-- jQuery and DataTables JS (for enhanced tables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/2.3.6/js/dataTables.dataTables.min.js"></script>
