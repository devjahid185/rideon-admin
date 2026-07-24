@extends('layouts.admin')
@section('content')
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

    .main-footer {
        overflow: hidden;
        margin-left: 0;
    }

    .dataTables_length {
        display: none;
    }
</style>
<div class="content">
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8"
                id="ticketFilterForm">
                <div class="col-md-12">
                    <div class="row">


                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label> {{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="" {{ !request()->input('status') ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Open</option>
                                <option value="0" {{ request()->input('status') === '0' ? 'selected' : '' }}>Close</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-2 col-xs-4 mt-5">
                            <br>
                            <button type="submit" name="btn" class="btn btn-primary btn-flat">
                                {{ trans('global.filter') }}</button>
                            <button type="button" id='resetBtn' class="btn btn-primary btn-flat">
                                {{ trans('global.reset') }}</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('admin.ticket.liveTrashSwitcher')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{$moduleName}} {{ trans('global.tickets_title') }} {{trans('global.list')}}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table
                            class=" table table-bordered table-striped table-hover datatable datatable-Booking randum">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        {{ trans('global.id') }}
                                    </th>
                                    <th>
                                        {{ trans('global.user') }}
                                    </th>
                                    <th>
                                        {{ trans('global.tickets_thread') }}
                                    </th>
                                    <th>
                                        {{ trans('global.title') }}
                                    </th>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <th>
                                        {{ trans('global.description') }}
                                    </th>
                                    <th>
                                    {{ trans('global.action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $ticket)
                                <tr data-entry-id="{{ $ticket->id }}">
                                    <td></td>

                                    <td>{{ $ticket->id }}</td>

                                    <td>
                                        @if ($ticket->appUser)
                                        <a href="{{ route('admin.overview', $ticket->appUser->id) }}">
                                            {{ $ticket->appUser->first_name ?? '' }} {{ $ticket->appUser->last_name ?? '' }}
                                        </a>
                                        @else

                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.ticket.thread', $ticket->id) }}">
                                            {{ $ticket->thread_id ?? '' }}
                                        </a>
                                    </td>

                                    <td>{{ $ticket->title ?? '' }}</td>

                                    <td>
                                        @if($ticket->thread_status == 1)
                                        Open
                                        @else
                                        Close
                                        @endif
                                    </td>

                                    <td>{{ $ticket->description ?? '' }}</td>

                                    <td>
                                        @can('ticket_delete')
                                        <button type="button" class="btn btn-xs btn-danger delete-button" data-id="{{ $ticket->id }}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end">
                                {{-- Previous Page Link --}}
                                @if ($data->currentPage() > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $data->previousPageUrl() }}"
                                        tabindex="-1">{{trans('global.previous')}}</a>
                                </li>
                                @else
                                <li class="page-item disabled">
                                    <span class="page-link">{{trans('global.previous')}}</span>
                                </li>
                                @endif

                                {{-- Numeric Pagination Links --}}
                                @for ($i = 1; $i <= $data->lastPage(); $i++)
                                    <li class="page-item {{ $i == $data->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $data->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($data->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $data->nextPageUrl() }}">{{trans('global.next')}}</a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{trans('global.next')}}</span>
                                    </li>
                                    @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


@endsection
@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const ticketId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        deleteTicket(ticketId);
                    }
                });
            });
        });

        function deleteTicket(ticketId) {
            const url = `{{ route('admin.ticket.destroy', ':id') }}`.replace(':id', ticketId);

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                success: function(response) {
                    Swal.close();
                    toastr.success('Ticket deleted successfully.', 'Success', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    location.reload(); // Optionally, refresh the page or update UI as needed
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    toastr.error('Error deleting ticket.', 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    console.error(error);
                }
            });
        }
    });
</script>

<script>
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        function handleDeletion(route) {
            return function(e, dt, node, config) {
                var ids = $.map(dt.rows({
                    selected: true
                }).nodes(), function(entry) {
                    return $(entry).data('entry-id');
                });

                if (ids.length === 0) {
                    Swal.fire({
                        title: 'No entries selected',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            method: 'POST',
                            url: route,
                            data: {
                                ids: ids
                            }
                        }).done(function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Messages have been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });

                            // Remove the rows from DataTables
                            location.reload();
                        }).fail(function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the messages.',
                                'error'
                            );
                        });
                    }
                });
            };
        }

        let deleteRoute = "{{ route('admin.ticket.deleteAll') }}";

        let deleteButton = {
            text: 'Delete all',
            className: 'btn-danger',
            action: handleDeletion(deleteRoute)
        };

        dtButtons.push(deleteButton);

        let table = $('.datatable-Booking:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

    })
</script>
<script>
    $(document).ready(function() {
        // Fetch the selected status from local storage
        const selectedStatus = localStorage.getItem('selectedStatus');

        // Check if the selected status is not null or empty
        if (selectedStatus) {
            // Set the selected status in the dropdown
            $('#status').val(selectedStatus);
        }

        // Event handler when the status value changes
        $('#status').on('change', function() {
            // Store the selected status in local storage
            const selectedValue = $(this).val();
            localStorage.setItem('selectedStatus', selectedValue);
        });

        // Check if the URL parameters for status, from, and to are empty
        const urlStatus = "{{ request()->input('status') }}";
        const urlFrom = "{{ request()->input('from') }}";
        const urlTo = "{{ request()->input('to') }}";

        if (!urlStatus) {
            // If all URL parameters are empty, remove the stored status value from local storage
            localStorage.removeItem('selectedStatus');
        }
    });
</script>

<script>
    $('#resetBtn').click(function() {
        $('#ticketFilterForm')[0].reset();
        var baseUrl = '{{ route('admin.ticket.index') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>
@endsection