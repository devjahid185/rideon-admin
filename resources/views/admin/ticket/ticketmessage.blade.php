@extends('layouts.admin')
@section('content')
<div class="content">
<!--/*seaction 1 start here*/-->
  <style>
        .dataTables_info{
            display: none;
        }
        . select-checkbox{
            display: none;
        }
        .paging_simple_numbers{
            display: none;
        }
        .pagination.justify-content-end{
            float: right;
        }
        .main-footer {
    overflow: hidden;
    margin-left: 0;
}
.abc table.dataTable tbody td.select-checkbox::before{
    display:none;
}
.dataTables_length{
    display:none;
}
    </style>
 
    <!--/*seaction 1  end*/-->
    <div >
            <div class="panel panel-default">
                <div class="panel-heading">
                  Ticket Reply List
                </div>
                <div class="panel-body abc">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Property">
                        <thead>
                            <tr>
                               
                                <th>
                                    Id
                                </th>
                                <th>
                                  User Name
                                </th>
                                <th>
                                  Thread Id
                                     </th>
                                <th>
                                  Title
                                </th>
                                <th>
                                  Description
                                </th>
                                <th>
                                  Admin Reply
                                </th>
                                <th>
                                  Message
                                </th>
                               
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($replies as $reply)
                            <tr data-entry-id="{{ $reply->id }}">
                                <td>{{ $reply->id }}</td>
                                <td>
                                @if ($reply->AppUser) <!-- Check if the user exists -->
                                    <a href="{{ route('admin.app-users.edit', $reply->AppUser->id) }}">
                                        {{ $reply->AppUser->first_name }} {{ $reply->AppUser->last_name }}
                                    </a>
                                @else
                                    <span class="text-muted">Admin</span>
                                @endif
                                </td>
                                <td>{{ $data->id }}</td> 
                                <td>{{ $data->title }}</td> 
                                <td>{{ $data->description }}</td> 
                                <td>
                                    @if ($reply->is_admin_reply)
                                        Yes
                                    @else
                                        No
                                    @endif
                                </td>
                                <td>{{ $reply->message }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                        <nav aria-label="...">
                        <ul class="pagination justify-content-end">
                        {{-- Previous Page Link --}}
                        @if ($replies->currentPage() > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $replies->previousPageUrl() }}" tabindex="-1">Previous</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Previous</span>
                            </li>
                        @endif

                        {{-- Numeric Pagination Links --}}
                        @for ($i = 1; $i <= $replies->lastPage(); $i++)
                            <li class="page-item {{ $i == $replies->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $replies->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($replies->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $replies->nextPageUrl() }}">Next</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Next</span>
                            </li>
                        @endif
                    </ul>
</nav>
                    </div>
                </div>
            </div>



        </div>
        
        
        
        
        
        
        
        
        
        
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Include Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        alert();
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)


  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 0, 'desc' ]],
    // pageLength: 10,
  });
  let table = $('.datatable-Property:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
</script>




@endsection