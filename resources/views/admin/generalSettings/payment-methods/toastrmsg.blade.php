<script>
    $(document).ready(function() {
        $('#onlinepayment').change(function() {
            var $this = $(this);
            var currentStatus = $this.prop('checked'); // Get the current checked status
            console.log(currentStatus);
            var status = currentStatus ? 'Active' : 'Inactive';
            $.ajax({
                url: "{{ route('admin.update-online-status') }}",
                type: "POST",
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.success, 'success', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    } else {
                        toastr.error(response.error, 'Error', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    if (xhr.status === 403) {
                        var response = JSON.parse(xhr.responseText);
                        toastr.error(response.message, 'Error', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        if (status === 'Active') {
                            $this.prop('checked', false); // Revert to unchecked
                        } else {
                            $this.prop('checked', true); // Revert to checked
                        }
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
        $('.Paymentmethodform').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.success,
                            '{{ trans('global.data_has_been_submitted') }}', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                    } else {
                        toastr.error(response.error, 'Error', {
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
