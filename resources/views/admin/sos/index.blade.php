@extends('layouts.admin')
@section('content')
    @php
        $i = 0;
        $j = 0;
    @endphp

    <div class="content">
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.sos.create') }}">
                    {{ trans('global.add') }} SOS Number
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        SOS Numbers List
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table-bordered table-striped table-hover datatable table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('global.name') }}</th>
                                        <th>{{ trans('global.number') }}</th>
                                        <th>{{ trans('global.status') }}</th>
                                        <th>{{ trans('global.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sosNumbers as $sos)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sos->name }}</td>
                                            <td>{{ $sos->sos_number }}</td>
                                            <td>
                                                <div
                                                    class="status-toggle d-flex justify-content-between align-items-center">
                                                    <input data-id="{{ $sos->id }}" class="check statusdata"
                                                        type="checkbox" id="sos{{ $i++ }}" data-toggle="toggle"
                                                        data-on="Active" data-off="Inactive" data-onstyle="success"
                                                        data-offstyle="danger" {{ $sos->status == '1' ? 'checked' : '' }}>
                                                    <label for="sos{{ $j++ }}"
                                                        class="checktoggle">checkbox</label>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.sos.edit', $sos->id) }}"
                                                    class="btn btn-xs btn-info">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>

                                                <a href="{{ route('admin.sos.destroy', $sos->id) }}"
                                                    class="btn btn-xs btn-danger delete-button"
                                                    data-id="{{ $sos->id }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $sosNumbers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Handle delete with confirmation
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            toastr.success('Deleted successfully', 'Success');
                            setTimeout(() => location.reload(), 1000);
                        },
                        error: function() {
                            toastr.error('An error occurred while deleting', 'Error');
                        }
                    });
                }
            });
        });

        // Handle status toggle
        $('.statusdata').change(function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.sos.update-status') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                },
                error: function() {
                    toastr.error('Something went wrong', 'Error');
                }
            });
        });
    </script>
@endsection
