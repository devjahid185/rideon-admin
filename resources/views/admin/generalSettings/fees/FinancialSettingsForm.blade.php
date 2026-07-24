	@extends('layouts.admin')
@section('content')
<section class="content">
			<div class="row">
				<div class="col-md-3 settings_bar_gap">
					<div class="box box-info box_info">
	<div class="">
	<h4 class="all_settings f-18 mt-1" style="margin-left:15px;">{{ trans('global.manage_settings') }}</h4>
		@include('admin.generalSettings.general-setting-links.links')
	</div>
</div>
				</div>

				<div class="col-md-9">
					<div class="box box-info">
                        
						<div class="box-header with-border">
							<h3 class="box-title">{{ trans('global.fees_title_singular') }}</h3><span class="email_status" style="display: none;">(<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>)</span>
						</div>

						<form id="fees_setting" method="post" action="{{ route('admin.FeesSetupadd') }}" class="form-horizontal " novalidate="novalidate">
							{{ csrf_field() }}
							<div class="box-body">
																	<!-- <div class="form-group guest_service_charge">
			<label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.guest_service_charge') }} <span class="text-danger">*</span></label>
		<div class="col-sm-6">
		<input type="text" name="feesetup_guest_service_charge" class="form-control " id="guest_service_charge" value="{{ $feesetup_guest_service_charge->meta_value ?? '' }}" placeholder="Guest service charge (%)">
		<span class="text-danger"></span>
	</div> -->
	<!-- <div class="col-sm-3">
		<small>
			 <input type="radio" id="admin" name="feesetup_guest_service_charge_get" value="admin" {{ ($feesetup_guest_service_charge_get->meta_value ?? 'admin') === 'admin' ? 'checked' : '' }}>
        <label for="admin">{{ trans('global.admin') }}</label>

        <input type="radio" id="vendor" name="feesetup_guest_service_charge_get" value="vendor" {{ ($feesetup_guest_service_charge_get->meta_value ?? 'admin') === 'vendor' ? 'checked' : '' }}>
        <label for="vendor">{{ trans('global.vendor') }}</label>
	</small>
	</div> 
</div>		-->														
	<!-- <div class="form-group iva_tax">
			<label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.iva_tax') }} <span class="text-danger">*</span></label>
		<div class="col-sm-6">
		<input type="text" name="feesetup_iva_tax" class="form-control " id="iva_tax" value="{{ $feesetup_iva_tax->meta_value ?? '' }}" placeholder="I.V.A Tax (%)">
		<span class="text-danger"></span>
	</div>
	<div class="col-sm-3">
		<small><input type="radio" id="admin" name="feesetup_iva_tax_get" value="admin" {{ ($feesetup_iva_tax_get->meta_value ?? 'admin') === 'admin' ? 'checked' : '' }}>
<label for="admin">{{ trans('global.admin') }}</label>

<input type="radio" id="vendor" name="feesetup_iva_tax_get" value="vendor" {{ ($feesetup_iva_tax_get->meta_value ?? 'vendor') === 'vendor' ? 'checked' : '' }}>
<label for="vendor">{{ trans('global.vendor') }}</label>
</small>
	</div>
</div>		 -->															

<div class="form-group accomodation_tax">
			<label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.iva_tax') }} <span class="text-danger">*</span></label>
		<div class="col-sm-6">
		<input type="text" name="feesetup_iva_tax" class="form-control " id="accomodation_tax" value="{{ $feesetup_iva_tax->meta_value ?? '' }}" placeholder="Accomadation Tax (%)">
		<span class="text-danger"></span>
	</div>
	<div class="col-sm-3" style="display: none;">
		<small><input type="radio" id="admin" name="feesetup_accomodation_tax_get" value="admin" {{ ($feesetup_accomodation_tax_get->meta_value ?? 'admin') === 'admin' ? 'checked' : '' }}>
<label for="admin">{{ trans('global.admin') }}</label> 

 <!-- <input type="radio" id="vendor" name="feesetup_accomodation_tax_get" value="vendor" {{ ($feesetup_accomodation_tax_get->meta_value ?? 'vendor') === 'vendor' ? 'checked' : '' }}>
<label for="vendor">{{ trans('global.vendor') }}</label>  -->
</small>
	</div>
</div>				<div class="form-group accomodation_tax">
			<label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.admin_commission') }}  <span class="text-danger">*</span></label>
		<div class="col-sm-6">
		<input type="text" name="feesetup_admin_commission" class="form-control " id="admin_commission" value="{{ $feesetup_admin_commission->meta_value ?? '' }}" placeholder="Admin Commission">
		<span class="text-danger"></span>
	</div>
	
</div>														<div class="text-center" id="error-message"></div>
							</div>

							<div class="box-footer">
								<button type="submit" class="btn btn-info btn-space">{{ trans('global.save') }}</button>
								<a class="btn btn-danger" href="{{ route('admin.settings') }}">{{ trans('global.cancel') }}</a>
															</div>
						</form>
					</div>
				</div>
			</div>
		</section>
		@endsection
		