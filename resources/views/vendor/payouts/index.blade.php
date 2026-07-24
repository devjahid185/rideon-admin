@extends('vendor.layout')
@section('content')
<div class="content">


    <!--/*seaction 1 start here*/-->

    <style>
        .dataTables_info {
            display: none;
        }

        .paging_simple_numbers {
            display: none;
        }

        .pagination.justify-content-end {
            float: right;
        }

        .dataTables_length {
            display: none;
        }

        tr.credit {
            background-color: green !important;
            color: white !important;
        }


        tr.warning td {
            background-color: orangered !important;
            color: black !important;
        }

        .loader {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1.5s linear infinite;
    position: relative;
    z-index: 10000;
}

#customLoader {
    position: fixed; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    z-index: 9999;
    backdrop-filter: blur(5px);
    background-color: rgba(255, 255, 255, 0.5);
    padding: 20px;
    border-radius: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

    </style>
    <!--/*seaction 1  end*/-->

    <!--/*seaction 4 start here*/-->

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" id="requestPayout">
                {{ trans('global.request') }} {{ trans('global.payout_title') }}
            </a>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8" id="payoutFilterForm">

                <div class="col-md-12 d-none">
                    <input class="form-control" type="hidden" id="startDate" name="from" value="">
                    <input class="form-control" type="hidden" id="endDate" name="to" value="">
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.date_range') }} </label>
                            <div class="input-group col-xs-12">
                                <!-- Add the input element here -->
                                <input type="text" class="form-control" id="daterange-btn">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">Please Select Status </option>
                                <option value="pending" {{ request()->input('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="success" {{ request()->input('status') === 'success' ? 'selected' : '' }}>Success</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">

                            <button type="submit" name="btn" class="btn btn-primary btn-flat">{{ trans('global.filter') }}</button>
                            <button type="button" name="reset_btn" id="resetBtn" class="btn btn-primary btn-flat">{{ trans('global.reset') }}</button>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-4 mt-5">
                            <br>

                        </div>
                    </div>
                </div>
            </form>



            <div class="row g-3 coenr-capitalize" style="margin-top: 65px;">

                <!-- First Tile (Wallet Balance) -->
                <div class="col-md-6">
                    <div class="caredoane cardbg-6">
                        <h4 class="title" id="wallet-balance">{{$hostspendmoney}}</h4>
                        <div class="subtitle">Wallet Balance </div>
                        <img class="sweicon" src="/public/images/icon/cash-withdrawal.png" alt="transaction">
                    </div>
                </div>

                <!-- Second Tile (Total Withdrawal Amount) -->
                <div class="col-md-6">
                    <div class="caredoane cardbg-3">
                        <h4 class="title">{{$totalmoney}}</h4>
                        <div class="subtitle">Total Earning</div>
                        <img class="sweicon" src="/public/images/icon/atm.png" alt="transaction">
                    </div>
                </div>
            </div>
            <div class="row g-3 coenr-capitalize">

                <div class="col-md-3">
                    <div class="caredoane cardbg-3">
                        <h4 class="title">{{$hostrecivemoney}}</h4>
                        <div class="subtitle">Total withdrawn amount</div>
                        <img class="sweicon" src="/public/images/icon/atm.png" alt="transaction">
                    </div>
                </div>
                <!-- Third Tile (Pending Withdraw) -->
                <div class="col-md-3">
                    <div class="caredoane cardbg-6">
                        <h4 class="title" id="pending-withdrawal">{{$hostpendingmoney}}</h4>
                        <div class="subtitle">Pending Withdrawal</div>
                        <img class="sweicon" src="/public/images/icon/cash-withdrawal.png" alt="transaction">
                    </div>
                </div>

                <!-- Fourth Tile (Total Earnings) -->
                <div class="col-md-3">
                    <div class="caredoane cardbg-3">
                        <h4 class="title">{{$incoming_amount}}</h4>
                        <div class="subtitle">Incoming Amount</div>
                        <img class="sweicon" src="/public/images/icon/atm.png" alt="transaction">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="caredoane cardbg-6">
                        <h4 class="title">{{$refunded}}</h4>
                        <div class="subtitle">Total Refund </div>
                        <img class="sweicon" src="/public/images/icon/cash-withdrawal.png" alt="transaction">
                    </div>
                </div>

                <!-- Second Tile (Total Withdrawal Amount) -->

            </div>

        </div>
        <div id="customLoader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="loader"></div>
    </div>
    <!--/*seaction 4  here*/-->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.payout_title') }}
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                            <tr style="background-color: #18bebd; color: white;"> <!-- Light blue header -->
                                <th>{{ trans('global.id') }}</th>
                                <th>{{ trans('global.amount') }}</th>
                                <th>{{ trans('global.status') }}</th>
                                <th>{{ trans('global.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payoutTransactions as $key => $payout)
                            <tr style="
                background-color: {{ $payout->payout_status === 'Success' ? '#4CAF50' : ($payout->payout_status === 'Pending' ? '#d5c136' : '#f2f2f2') }};
                color: {{ $payout->payout_status === 'Success' ? 'white' : ($payout->payout_status === 'Pending' ? 'white' : '#555') }};
            ">
                                <td>{{ $payout->id }}</td>
                                <td>{{ $payout->currency ?? '' }} {{ $payout->amount }}</td>

                                <td>
                                    <span style="
                        padding: 5px 10px; 
                        color: white; 
                        border-radius: 4px;
                        background-color: {{ $payout->payout_status === 'Success' ? '#5cb85c' : ($payout->payout_status === 'Pending' ? '#f0ad4e' : '#777') }};
                    ">
                                        {{ $payout->payout_status }}
                                    </span>
                                </td>
                                <td>{{ $payout->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>



                    <nav aria-label="...">
                        <ul class="pagination justify-content-end">
                            {{-- Previous Page Link --}}
                            @if ($payoutTransactions->currentPage() > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $payoutTransactions->previousPageUrl() }}" tabindex="-1">{{ trans('global.previous') }}</a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link">{{ trans('global.previous') }}</span>
                            </li>
                            @endif

                            {{-- Numeric Pagination Links --}}
                            @for ($i = 1; $i <= $payoutTransactions->lastPage(); $i++)
                                <li class="page-item {{ $i == $payoutTransactions->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $payoutTransactions->url($i) }}">{{ $i }}</a>
                                </li>
                                @endfor

                                {{-- Next Page Link --}}
                                @if ($payoutTransactions->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $payoutTransactions->nextPageUrl() }}">{{ trans('global.next') }}</a>
                                </li>
                                @else
                                <li class="page-item disabled">
                                    <span class="page-link">{{ trans('global.next') }}</span>
                                </li>
                                @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>



</div>
@endsection

@section('scripts')
@parent
<script>
$(document).on('click', '#requestPayout', function(e) {
    e.preventDefault();

    var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token

    var walletAmountText = document.getElementById('wallet-balance').innerText.trim(); 
    var pendingAmountText = document.getElementById('pending-withdrawal').innerText.trim(); 

    var walletAmount = parseFloat(walletAmountText.replace(/,/g, '')); 
    var pendingAmount = parseFloat(pendingAmountText.replace(/,/g, '')); 
    if (walletAmount === 0) {
        Swal.fire('Warning', 'Your wallet balance is zero.', 'warning');
        return; // Stop further execution if balance is zero
    }
    var maxPayout = walletAmount - pendingAmount;
    

    Swal.fire({
        title: 'Request Payout',
        html: `
            <input type="number" id="payoutAmount" class="swal2-input" placeholder="Enter amount">
            <p class="text-danger" style="font-size: 12px;">Note: Payout request amount should not be greater than ${maxPayout.toFixed(2)} [wallet balance - pending withdrawal]</p>
        `,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Request Payout',
        preConfirm: function() {
            var payoutAmount = $('#payoutAmount').val();
            
            if (!payoutAmount || parseFloat(payoutAmount) <= 0 || payoutAmount > maxPayout) {
                Swal.showValidationMessage('Please enter a valid amount.');
                return false; // Prevent form submission if amount is invalid
            }
            return payoutAmount;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('customLoader').style.display = 'block';
            // Send the payout request to the server
            $.ajax({
                type: "POST",
                url: "{{ route('vendor.request.payout') }}", // Replace with your payout request route
                data: {
                    _token: csrfToken,
                    amount: result.value
                },
                success: function(response) {
                    document.getElementById('customLoader').style.display = 'none';
                    if (response.status === 200) {
                        Swal.fire('Success', 'Payout requested successfully!', 'success');
                        window.location.reload();
                    } else {
                        document.getElementById('customLoader').style.display = 'none';
                        Swal.fire('Error', response.message, 'error');
                        
                    }
                },
                error: function(xhr, status, error) {
                    $('#customLoader').hide();
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                }
            });
        }
    });
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
<script>
    $('#resetBtn').click(function() {
        $('#payoutFilterForm')[0].reset();
        var baseUrl = '{{ route('vendor.payouts') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>
@endsection