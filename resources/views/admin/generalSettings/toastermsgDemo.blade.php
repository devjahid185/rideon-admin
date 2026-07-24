
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        @if(session('error'))
        toastr.error("{{ session('error') }}", 'Error', {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-bottom-right"
        });
    @endif
    });
</script>


