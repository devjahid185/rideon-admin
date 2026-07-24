@section('scripts')
@parent
<script>
$(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    function handleDeletion(route) {
        return function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
            });

            if (ids.length === 0) {
                Swal.fire({
                    title: '{{ trans("global.no_entries_selected") }}',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: '{{ trans("global.are_you_sure") }}',
                text: '{{ trans("global.delete_confirmation") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        method: 'POST',
                        url: route,
                        data: { ids: ids, _method: 'DELETE' }
                    }).done(function (response) {
                        location.reload(); // Reload the page after deletion
                    }).fail(function (xhr, status, error) {
                        Swal.fire(
                            '{{ trans("global.error") }}',
                            '{{ trans("global.delete_error") }}',
                            'error'
                        );
                    });
                }
            });
        };
    }

    
    let deleteRoute = "{{ route('admin.delete.rows') }}"; 
    if (window.location.href.indexOf('trash') !== -1) {
        deleteRoute = "{{ route('admin.trash-delete.rows') }}"; 
    }

    let deleteButton = {
        text: '{{ trans("global.delete_all") }}',
        className: 'btn-danger',
        action: handleDeletion(deleteRoute) 
    };

    dtButtons.push(deleteButton);

    let table = $('.datatable-Property:not(.ajaxTable)').DataTable({
        buttons: dtButtons,
        orderCellsTop: true,
        select: {
            style: 'multi'
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

});
</script>





{{-- <script>
    $(document).ready(function() {
        $('.filterproduct').click(function(e){
            e.preventDefult();
            $(this).closest('form').submit();
        })
        $('.resetproduct').click(function(e){
            e.preventDefult();
            $(this).closest('form').submit();
        })
        // Initialize the Select2 for the customer select box
        $('#customer').select2({
            ajax: {
                url: "{{ route('admin.searchItem') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    // Transform the response data into Select2 format
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.name,
                            };
                        })
                    };
                },
                cache: true, // Cache the AJAX results to avoid multiple requests for the same data
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error while fetching customer data:", textStatus, errorThrown);
                    // Optionally display an error message to the user
                    alert("An error occurred while loading customer data. Please try again later.");
                }
            }
        });

        // Your other code for DateRangePicker initialization and filters
    });
</script>

<!-- Include DateRangePicker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize the DateRangePicker
    $('#daterange-btn').daterangepicker({
        opens: 'right', // Change the calendar position to the left side of the input
        autoUpdateInput: false, // Disable auto-update of the input fields
        ranges: {
            'Anytime': [moment(), moment()],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'YYYY-MM-DD', // Format the date as you need
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom Range'
        }
    });

    // Check if the start and end dates are stored in local storage
    const storedStartDate = localStorage.getItem('selectedStartDate');
    const storedEndDate = localStorage.getItem('selectedEndDate');

    // Get the URL parameters for 'from' and 'to'
    const urlFrom = "{{ request()->input('from') }}";
    const urlTo = "{{ request()->input('to') }}";

    // If both start and end dates are available in local storage, and the URL parameters 'from' and 'to' are not empty, set the initial date range
    if (storedStartDate && storedEndDate && urlFrom && urlTo) {
        const startDate = moment(storedStartDate);
        const endDate = moment(storedEndDate);
        $('#daterange-btn').data('daterangepicker').setStartDate(startDate);
        $('#daterange-btn').data('daterangepicker').setEndDate(endDate);
        $('#daterange-btn').val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
    } else {
        // Otherwise, clear the date range in DateRangePicker
        $('#daterange-btn').val('');
        $('#startDate').val('');
        $('#endDate').val('');

        // Clear the stored start and end dates from local storage
        localStorage.removeItem('selectedStartDate');
        localStorage.removeItem('selectedEndDate');
    }

    // Update the hidden input fields and button text when the date range changes
    $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
        $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));

        // Store the selected start and end dates in local storage
        localStorage.setItem('selectedStartDate', picker.startDate.format('YYYY-MM-DD'));
        localStorage.setItem('selectedEndDate', picker.endDate.format('YYYY-MM-DD'));
    });

    // Clear the date range selection and input fields when the 'Cancel' button is clicked
    $('#daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#startDate').val('');
        $('#endDate').val('');

        // Clear the stored start and end dates from local storage
        localStorage.removeItem('selectedStartDate');
        localStorage.removeItem('selectedEndDate');
    });

    // Function to reset the filters
    function resetFilters() {
        $('#daterange-btn').val('');
        $('#startDate').val('');
        $('#endDate').val('');
        $('#status').val('');
        $('#customer').val('').trigger('change');
    }

    // Optional: Submit the form when the "Filter" button is clicked
    $('button[name="btn"]').on('click', function() {
        $('form').submit();
    });
});
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
 <script>
	$('.statusdata').change(function() { 
			var status = $(this).prop('checked') == true ? 1 : 0;  
			var id = $(this).data('id');  
            var requestData = {
            'status': status,
            'pid': id
        };
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;
			$.ajax({ 
		
					type: "POST", 
				dataType: "json", 
				url: 'update-item-status', 
				data: requestData, 
				success: function(response){ 
					 toastr.success(response.message, '{{ trans("global.success") }}', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
				} 
			}); 
		})
</script> 
<script>
	$('.isfeatureddata').change(function() { 
			var isfeatured = $(this).prop('checked') == true ? 1 : 0;  
			var id = $(this).data('id');  
            var requestData = {
            'featured': isfeatured,
            'pid': id
        };
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;
			$.ajax({ 
		
					type: "POST", 
				dataType: "json", 
				url: 'update-item-featured', 
				data: requestData, 
				success: function(response){ 
					 toastr.success(response.message, '{{ trans("global.success") }}', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
				} 
			}); 
		})
</script>
<script>
	$('.isvefifieddata').change(function() { 
			var isvefified = $(this).prop('checked') == true ? 1 : 0;  
			var id = $(this).data('id');  
            var requestData = {
            'isverified': isvefified,
            'pid': id
        };
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;
			$.ajax({ 
		
					type: "POST", 
				dataType: "json", 
				url: 'update-item-verified', 
				data: requestData, 
				success: function(response){ 
					 toastr.success(response.message, '{{ trans("global.success") }}', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
				} 
			}); 
		})
</script>
<!-- reset button search filter -->
<script>
    $('#resetBtn').click(function(){
        $('#propertyFilterForm')[0].reset();
        var baseUrl = '{{ route('admin.boat.index') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>
<!-- customer select2  -->
<script>
 $(document).ready(function() {
    // Initialize the Select2 for the customer select box
    $('#customer').select2({
        ajax: {
            url: "{{ route('admin.searchcustomer') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                // Transform the response data into Select2 format
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.first_name,
                        };
                    })
                };
            },
            cache: true, // Cache the AJAX results to avoid multiple requests for the same data
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error while fetching customer data:", textStatus, errorThrown);
                // Optionally display an error message to the user
                alert("An error occurred while loading customer data. Please try again later.");
            }
        }
    });
});

</script>


<script>
    // JavaScript to handle the SweetAlert dialog
    document.addEventListener('DOMContentLoaded', function() {
        // Add a click event listener to the "Delete" button
        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function() {
                var deleteUrl = this.form.action; // Get the form's action URL

                Swal.fire({
                    title: "{{ trans('global.are_you_sure') }}",
                    text: "{{ trans('global.you_able_revert_this') }}",
                    icon: "{{ trans('global.warning') }}",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ trans('global.yes_delete_it') }}",
                    cancelButtonText: "{{ trans('global.cancel') }}",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(function(response) {
                            if (response.ok) {
                              
                                location.reload();
                            } 
                        });
                        location.reload();
                    }
                });
            });
        });
    });
</script> --}}

@endsection