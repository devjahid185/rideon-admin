@extends('layouts.admin')
@section('content')
    <section class="content">
        <div class="row gap-2">
            @include($leftSideMenu)

            <div id="calendarID" class="col-md-9">


                <div class="row">
                    <div class="col-xs-5 col-md-2 col-sm-2">
                        <h2>
                            <a id="previousButton"
                                href="{{ route($routeIndex, ['month' => $prevMonth, 'year' => $prevYear, 'id' => $id, 'module' => $module]) }}"
                                class="btn btn-secondary">{{ trans('') }} <i
                                    class="fa fa-chevron-left fa-lg calendar-icon-style"></i></a>
                            <a id="nextButton"
                                href="{{ route($routeIndex, ['month' => $nextMonth, 'year' => $nextYear, 'id' => $id, 'module' =>$module]) }}"
                                class="btn btn-secondary">{{ trans('') }} <i
                                    class="fa fa-chevron-right fa-lg calendar-icon-style"></i></a>
                        </h2>
                    </div>
                    <div class="col-xs-5 col-2 col-md-8 col-sm-8 current-month-selection">
                    

                        <select id="monthYearSelect">
                            @for ($i = 0; $i <= 48; $i++)
                                @php
                                    $optionDate = date('Y-m', strtotime("+$i months", strtotime(date('Y-m'))));
                                    $optionYear = date('Y', strtotime($optionDate));
                                    $optionMonth = date('n', strtotime($optionDate));
                                @endphp
                                <option
                                    value="{{ route($routeIndex, ['month' => $optionMonth, 'year' => $optionYear, 'id' => $id, 'module' =>$module]) }}"
                                    {{ $optionMonth == $month && $optionYear == $year ? 'selected' : '' }}>
                                    {{ $monthNames[$optionMonth] }} {{ $optionYear }}
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>
            </div>



        </div>
    </section>
    <!-- Include jQuery library if not already included -->
    @endsection
    @section('scripts')
    <script>
        $(document).ready(function() {
            attachButtonHandlers();
            $("#monthYearSelect").trigger('change');

            function showCustomLoader() {
                $('#customLoader').show();
            }

            function hideCustomLoader() {
                $('#customLoader').hide();
            }

            function updateCalendar(data) {

                hideCustomLoader(); // Reattach event handlers after updating content
                $('#calendarID').html(data.html);
                attachButtonHandlers();
            }

            function attachButtonHandlers() {

                // Capture click event on "Previous" button
                // $("#previousButton").on("click", function(e) {
                // $('body').on('click', '#previousButton', function(e){
                $('body').off('click', '#previousButton').on('click', '#previousButton', function(e) {
                    e.preventDefault();
                    var url = $(this).attr("href");
                    showCustomLoader();
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {

                            updateCalendar(data);
                            hideCustomLoader();
                        },
                        error: function(error) {
                            console.error("Error:", error);
                            hideCustomLoader();
                        }
                    });
                });

                // Capture click event on "Next" button
                // $("#nextButton").on("click", function(e) {
                $('body').off('click', '#nextButton').on('click', '#nextButton', function(e) {
                    e.preventDefault();
                    var url = $(this).attr("href");
                    showCustomLoader();
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            updateCalendar(data);
                            hideCustomLoader();

                        },
                        error: function(error) {
                            console.error("Error:", error);
                            hideCustomLoader();
                        }
                    });
                });

                // Capture change event on the select dropdown
                $('body').on('change', '#monthYearSelect', function() {
                    // $("#monthYearSelect").on("change", function() {
                    var selectedOption = $(this).find("option:selected");
                    var url = selectedOption.val();
                    showCustomLoader();


                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(data) {
                            updateCalendar(data);
                            hideCustomLoader();
                        },
                        error: function(error) {
                            console.error("Error:", error);
                            hideCustomLoader();
                        }
                    });
                });
                // $('.calendar-date').on('click', function() {
                $('body').off('click', '.calendar-date').on('click', '.calendar-date', function() {
                    // Extract the selected date from the data attribute
                    var selectedDate = $(this).data('date');
                    var price = $(this).data('pricevalue');
                    var datevalue = $(this).data('datevalue');
                    var statusvalue = $(this).data('statusvalue');
                    var minStayValue = $(this).data('minstayvalue');
                    console.log(minStayValue);

                    // Get the current date
                    var currentDate = new Date();
                    var selectedDateObj = new Date(selectedDate);

                    // Check if the selected date is the current date or a future date
                    if (selectedDateObj >= currentDate || selectedDateObj.toDateString() === currentDate
                        .toDateString()) {
                        console.log(selectedDate);

                        $('#dtpc_start_admin').val(selectedDate);
                        $('#dtpc_end_admin').val(selectedDate);
                        $('#dtpc_price').val(price);
                        $('#dtpc_datevalue').val(datevalue);
                        $('#dtpc_statusvalue').val(statusvalue);
                        $('#dtpc_status').val(statusvalue);
                        $('#dtpc_minstay_admin').val(minStayValue);
                        $('#priceModal').modal('show');
                    } else {
                        console.error('Selected date is not the current date or a future date.');
                    }
                });



            }

            attachButtonHandlers(); // Initial attachment of event handlers
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#submitCalanderform').click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '{{ route($routeUpdate) }}',
                    data: $('#calanderAdd').serialize(),
                    success: function(data) {

                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.errors) {
                            var errors = response.responseJSON.errors;
                            $('.error-message').text('');

                            // Then display new error messages
                            for (var field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    var errorMessage = errors[field][
                                        0
                                    ]; // get the first error message
                                    $('#descriptionerror-' + field).text(errorMessage);
                                }
                            }
                        }
                    }
                });
            });


        });
    </script>
<script>
     $(document).ready(function () {  
        $('.toggle-select').click(function () {     
            alert('fdlyj');
            $('#monthYearSelect').click();
        });
    })
</script>


@endsection