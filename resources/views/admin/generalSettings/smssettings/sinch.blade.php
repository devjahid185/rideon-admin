@extends('layouts.admin')
@section('content')
@php
        $i = 0;
        $j = 0;
    @endphp
<section class="content">
      <div class="row">
        <div class="col-md-3 settings_bar_gap">
          <div class="box box-info box_info">
	<div class="">
  <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">  {{ trans('global.manage_settings') }}</h4>
		@include('admin.generalSettings.general-setting-links.links')
	
	</div>
</div>
        </div>
        <!-- right column -->
       
          @include('admin.generalSettings.smssettings.smsnavicon')
           
    <input class="check statusdata" type="checkbox"
    data-onstyle="success"
    id="user{{ $i }}"
    data-offstyle="danger"
    data-toggle="toggle"
    data-on="Active"
    data-off="Inactive"
    data-url="{{ route('admin.update-sms-provider-name') }}"
    data-user-value="sinch"
    {{ $sms_provider_name != "" && $sms_provider_name->meta_value == 'sinch' ? 'checked' : '' }}>
<label for="user{{ $i }}" style="margin-left: 91%; margin-top: 8px;" class="checktoggle">checkbox</label>


<form method="post" action="{{route('admin.updateSinch')}}" class="form-horizontal smssettingform" enctype="multipart/form-data" novalidate="novalidate">
			{{ csrf_field() }}
              <div class="box-body">
             
              <div class="form-group">
              		<label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.service_plan_id') }}<span class="text-danger">*</span></label>
              		<div class="col-sm-6">
              			<input class="form-control" type="password" name="sinch_service_plan_id" id="sid" placeholder="Service Plan Id" value="{{ $sinch_service_plan_id->meta_value ?? '' }}">
              		</div>
              	</div>
              	<div class="form-group">
              		<label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.api_token') }}<span class="text-danger">*</span></label>
              		<div class="col-sm-6">
              			<input class="form-control" type="password" name="sinch_api_token" id="sid" placeholder="Api Token" value="{{ $sinch_api_token->meta_value ?? '' }}">
              		</div>
              	</div>
              	<div class="form-group">
              		<label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.sender_number') }}<span class="text-danger">*</span></label>
              		<div class="col-sm-6">
              			<input class="form-control" type="password" name="sinch_sender_number" id="token" placeholder="Sender Number" value="{{ $sinch_sender_number->meta_value ?? '' }}">
              		</div>
              	</div>
              
              

              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                                <button type="submit" class="btn btn-info " id="submitBtn">{{ trans('global.save') }}</button>
								<a class="btn btn-danger" href="{{ route('admin.settings') }}">{{ trans('global.cancel') }}</a>

                              
              </div>
              <!-- /.box-footer -->
            </form>
          




          </div>
          <!-- /.box -->

          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
@endsection
@section('scripts')

@include('admin.generalSettings.smssettings.toastrmsg')
<script>
$(document).ready(function() {
    $('.statusdata').change(function() {

        var status = $(this).prop('checked') ? 'Active' : 'Inactive';
        var userValue = $(this).data('user-value');
      
        var id = $(this).data('id');
        var url = $(this).data('url');
        
            if(status == 'Inactive'){
               
                toastr.error("You have to enable one of the sms service", 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                $(this).prop('checked', true);
            }

     else{
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                status: status,
                userValue: userValue,
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
               
                if (response.success) {
                    toastr.success('Status updated successfully', 'Success', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                } else {
                    toastr.error(response.message, 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });

                    // Revert the checkbox state
                    $(this).prop('checked', !status);
                }
            },
            error: function(xhr) {
                console.error(xhr);
            }
        });
     }

    });
});
</script>
@endsection
