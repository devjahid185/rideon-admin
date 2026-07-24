<script>
    $(document).ready(function() {
        $('.smssettingform').submit(function(event) {
            event.preventDefault();
            
           
            var formData = $(this).serialize();
          
         $.ajax({
    url: $(this).attr('action'), 
    type: $(this).attr('method'), 
    data: formData, 
    success: function(response) {
        // Assuming the server sends a JSON response with an 'error' property if there's a logical error
        if (response.status === 403) {
            toastr.error(response.error, 'Error', {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right"
            });
        } else {
            toastr.success(response.message || '{{ trans("global.data_has_been_submitted") }}', 'Success', {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    },
    error: function(xhr) {
        // Check if the status is 403 to display a specific message
        if (xhr.status === 403) {
            toastr.error('Form submission is disabled in demo mode.', 'Error', {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right"
            });
        } else {
            // General error message for other statuses
            toastr.error('An error occurred. Please try again.', 'Error', {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    }
});


        });
    });
</script>

<script>
    $(document).ready(function() {

        $('#autofillotp').change(function() {
                    var status = $(this).prop('checked') ? 'Active' : 'Inactive';
                    var $toggleButton = $(this);

                    $.ajax({
                        url: "{{ route('admin.update-auto-fill-otp') }}",
                        type: "POST",
                        data: {
                            status: status,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {

                            toastr.success('Status updated successfully', 'success', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                        },
                        error: function(xhr) { //remove code in error block when not in demo mode
                            if (xhr.status === 403) {
                                var response = JSON.parse(xhr.responseText);
                                if (status === 'Active') {
                                    $toggleButton.prop('checked', false); // Revert to unchecked
                                } else {
                                    $toggleButton.prop('checked', true); // Revert to checked
                                }
                                toastr.error(response.error, 'Error', {
                                    CloseButton: true,
                                    ProgressBar: true,
                                    positionClass: "toast-bottom-right"
                                });
                            } else {
                                // General error handling
                                toastr.error('An unexpected error occurred', 'Error', {
                                    CloseButton: true,
                                    ProgressBar: true,
                                    positionClass: "toast-bottom-right"
                                });
                            }
                        }
                    });
                });

    });
</script>