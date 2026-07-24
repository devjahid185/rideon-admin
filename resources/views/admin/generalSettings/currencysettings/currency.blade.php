@extends('layouts.admin')
@section('styles')
<style>

.d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between; /* Ensures items are spaced between edges */
        width: 100%; /* Ensure it takes the full width */
    }

    .multicurrency_status {
        display: flex;
        align-items: center; /* Vertically center toggle with text */
        margin-left: auto; /* Push to the right */
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 22px;
        margin-left: 10px; /* Space between text and switch */
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .toggle-label {
        font-size: 14px;
    }

</style>
@endsection
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
                        
						<!-- <div class="box-header with-border">
							<h3 class="box-title">Currency Settings Wizard</h3><span class="email_status" style="display: none;">(<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>)</span>
						</div> -->
						<div class="box-header with-border d-flex justify-content-between align-items-center" style="width: 100%;">
					<h3 class="box-title">{{ trans('global.currency_settings_wizard')}}</h3>
					<div class="multicurrency_status" style="display: none">
						{{ trans('global.single')}}{{ trans('global.currency')}} <label class="switch">
							<input type="checkbox" id="multicurrency_status" data-offstyle="danger"
								data-toggle="toggle" data-on="Active" data-off="InActive"
								{{ $multicurrency_status && $multicurrency_status->meta_value == 1 ? 'checked' : '' }}>
							<span class="slider round"></span>
						</label> <span style="margin-left: 10px;">{{ trans('global.multicurrency')}}</span>
					</div>
				  </div>

						<form id="fees_setting" method="post" action="{{ route('admin.updateCurrencyAuthKey') }}" class="form-horizontal " novalidate="novalidate">
							{{ csrf_field() }}
							<div class="box-body">
																						

                                <div class="form-group accomodation_tax" style="display: none">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Currency Auth Key <span class="text-danger">*</span></label>
                                        <div class="col-sm-6">
                                                <input type="password" name="currency_auth_key" class="form-control " id="currency_auth_key" value="{{ $currency_auth_key->meta_value ?? '' }}" placeholder="Key">
                                                <a href="https://www.exchangerate-api.com/" target="_blank">
													<i class="fa fa-question-circle text-info" title="Help"></i><small>(Please create the keys and insert it.)</small>
												</a>
                                        </div>
                         
                                </div>	
                                <div class="text-center" id="error-message"></div>

								<div class="form-group accomodation_tax">
							<label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.default_currency')}}<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<select class="form-control validate_field" id="default_currency" name="general_default_currency">
									@foreach($allcurrency as $currency)
									<option value="{{ $currency->currency_code }}" @if(($general_default_currency->meta_value ?? null) == $currency->currency_code) selected @endif>
										{{ $currency->currency_name }}
									</option>
									@endforeach
								</select>
								<span class="text-danger"></span>
							</div>

						</div>
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
		@section('scripts')
<script>
	$(document).ready(function() {

		$('#multicurrency_status').change(function() {
			
			var status = $(this).prop('checked') ? 'Active' : 'Inactive';


			$.ajax({
				url: "{{ route('admin.set-multicurrency') }}",
				type: "POST",
				data: {
					status: status,
					_token: '{{ csrf_token() }}'
				},
				success: function(response) {

					toastr.success('Status updated successfully', 'success', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
				},
				error: function(xhr) {
					console.error(xhr);
				}
			});
		});

	});
	</script>
	@endsection
		