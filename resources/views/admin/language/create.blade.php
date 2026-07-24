@extends('layouts.admin')
@section('content')
<div class="col-md-12">
					<div class="box box-info">
                        
						<div class="box-header with-border">
							<h3 class="box-title">Add Language Form</h3><span class="email_status" style="display: none;">(<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>)</span>
						</div>

						<form id="add_language" method="post" action="{{ route('admin.addlanguagedata')}}" class="form-horizontal " novalidate="novalidate">
							{{ csrf_field() }}
							<div class="box-body">
																	<div class="form-group name">
			<label for="inputEmail3" class="col-sm-3 control-label">Name <span class="text-danger">*</span></label>
		<div class="col-sm-6">
		<input type="text" name="name" class="form-control " id="name" value="" placeholder="Name">
		<span class="text-danger"></span>
	</div>
	<div class="col-sm-3">
		<small></small>
	</div>
</div>																	<div class="form-group short_name">
			<label for="inputEmail3" class="col-sm-3 control-label">Short Name <span class="text-danger">*</span></label>
		<div class="col-sm-6">
		<input type="text" name="short_name" class="form-control " id="short_name" value="" placeholder="Short Name">
		<span class="text-danger"></span>
	</div>
	<div class="col-sm-3">
		<small></small>
	</div>
</div>																	<div class="form-group status">
<label for="inputEmail3" class="col-sm-3 control-label">Status</label>
	<div class="col-sm-6">
		<select class="form-control validate_field valid" id="status" name="language_status">
							<option value="1">Active</option>
							<option value="0">Inactive</option>
					</select>
		<span class="text-danger"></span>
	</div>

	<div class="col-sm-3">
		<small></small>
	</div>
</div>																<div class="text-center" id="error-message"></div>
							</div>

							<div class="box-footer">
								<button type="submit" class="btn btn-info btn-space">Submit</button>
								<a class="btn btn-danger" href="{{ route('admin.settings') }}">Cancel</a>
															</div>
						</form>
					</div>
				</div>
@endsection