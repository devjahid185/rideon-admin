@extends('layouts.admin')
@section('content')
<section class="content">
		<div class="row">
				<div class="col-md-3 settings_bar_gap">
				<div class="box box-info box_info">
	<div class="panel-body">
		<h4 class="all_settings">Manage Settings</h4>
		<ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
		<li>
				<a href="{{ route('admin.settings') }}" data-group="profile"  class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">General</a>
                           </li>
                           
							<li>
					<a href="{{ route('admin.preferences') }}" data-group="profile" class="{{ request()->routeIs('admin.preferences') ? 'active' : '' }}">Preferences</a>
				</li>
			
							<li class="">
						<a href="{{ route('admin.smssetting') }}" data-group="sms" class="{{ request()->routeIs('admin.smssetting') ? 'active' : '' }}">SMS Settings</a>
				</li>
				<li>
                <a href="{{ route('admin.email') }}" data-group="sms" class="{{ request()->routeIs('admin.email') ? 'active' : '' }}">Email Settings</a>
				</li>
			
							<li>
                            <a href="{{ route('admin.fees') }}" data-group="sms" class="{{ request()->routeIs('admin.fees') ? 'active' : '' }}">Fees</a>
				</li>
			
							<li>
                            <a href="{{ route('admin.language') }}" data-group="sms" class="{{ request()->routeIs('admin.language') ? 'active' : '' }}">Language</a>
				</li>
			
							<li>
                            <a href="{{ route('admin.api-informations') }}" data-group="sms" class="{{ request()->routeIs('admin.api-informations') ? 'active' : '' }}">Api Credentials</a>
				</li>
			
			
				<li>
                <a href="{{ route('admin.payment-methods') }}" data-group="sms" class="{{ request()->routeIs('admin.payment-methods') ? 'active' : '' }}">Payment Methods</a>
				</li>
			
							<li>
                            <a href="{{ route('admin.social-links') }}" data-group="sms" class="{{ request()->routeIs('admin.social-links') ? 'active' : '' }}">Social Links</a>
				</li>
			
                             <li>
                             <a href="{{ route('admin.social-logins') }}" data-group="sms" class="{{ request()->routeIs('admin.social-logins') ? 'active' : '' }}">Social Logins</a>
                 </li>
			
					</ul>
	</div>
</div>
				</div>
				<div class="col-md-9">

					<div class="box box_info">
						<div class="box-header">
							<h4 class="box-title type_info_header">Language</h4>
							<div class="pull-right"><a class="btn btn-success" href="{{ route('admin.addlanguage')}}">Add Language</a></div>
						</div><hr>
						<!-- /.box-header -->
						<div class="box-body table-responsive">
							<div id="dataTableBuilder_wrapper" class="dataTables_wrapper no-footer">
								<div class="dataTables_length" id="dataTableBuilder_length">
									<label><select name="dataTableBuilder_length" aria-controls="dataTableBuilder" class="">
										<option value="10">10</option>
										<option value="25">25</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select></label>
								</div>
								<div id="dataTableBuilder_filter" class="dataTables_filter">
								<label>Search:<input type="search" class="" placeholder="" aria-controls="dataTableBuilder"></label></div>
								<div id="dataTableBuilder_processing" class="dataTables_processing" style="display: none;">Processing...</div>
								<table class="table dataTable no-footer" id="dataTableBuilder" role="grid" aria-describedby="dataTableBuilder_info" style="width: 791px;">
								<thead>
									
									<tr role="row">
										<th title="Name" class="sorting_desc" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" style="width: 169px;" aria-sort="descending" aria-label="Name: activate to sort column ascending">Name</th>
									<th title="Short Name" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" style="width: 240px;" aria-label="Short Name: activate to sort column ascending">Short Name</th>

									<th title="Status" class="sorting" tabindex="0" aria-controls="dataTableBuilder" rowspan="1" colspan="1" style="width: 142px;" aria-label="Status: activate to sort column ascending">Status</th>
									
									<th title="Action" class="sorting_disabled" rowspan="1" colspan="1" style="width: 176px;" aria-label="Action">Action</th>
								</tr>
							</thead>
							<tbody>
							@foreach($listdata as $listdata1)
								<tr role="row" class="odd">
									<td>{{ $listdata1->name }}</td>
									<td class="sorting_1">{{ $listdata1->short_name }}</td>
									<td>
										@if($listdata1->language_status == 1)
											Active
										@else
											Inactive
										@endif
									</td>
									<td><a href="{{ route('admin.editlanguage',['id' => $listdata1->id]) }}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;<a href="javascript:void(0)" data-id="{{ $listdata1->id }}" class="btn btn-xs btn-danger delete-warning deletedrecord"><i class="glyphicon glyphicon-trash"></i></a></td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<div class="dataTables_info" id="dataTableBuilder_info" role="status" aria-live="polite">Showing 1 to 7 of 7 entries</div><div class="dataTables_paginate paging_simple_numbers" id="dataTableBuilder_paginate"><a class="paginate_button previous disabled" aria-controls="dataTableBuilder" data-dt-idx="0" tabindex="0" id="dataTableBuilder_previous">Previous</a><span><a class="paginate_button current" aria-controls="dataTableBuilder" data-dt-idx="1" tabindex="0">1</a></span><a class="paginate_button next disabled" aria-controls="dataTableBuilder" data-dt-idx="2" tabindex="0" id="dataTableBuilder_next">Next</a></div></div>
						</div>
					</div>
				</div>
	</section>


<!-- Add SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">

<!-- Add jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Add SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Your script that uses SweetAlert2 -->
<script>
$('.deletedrecord').on("click", function(event) {
    event.preventDefault(); // Prevent the default anchor tag behavior

    var id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning", // Use "icon" instead of "type"
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function(t) {
        if (t.isConfirmed) { // Use "isConfirmed" instead of "value"
            $.ajax({
                url: "{{ route('admin.deletelanguage', ['id' => ':id']) }}".replace(':id', id),
                type: 'get',
                data: {
                    "_token": "qtpYK9Oaogr89ysECvavlQu5QOwD9nqpAqleFjzu"
                },
                success: function(response) {
                    toastr.success(response.message, "Success!", {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
            });
        }
    });
});
</script>


@endsection
