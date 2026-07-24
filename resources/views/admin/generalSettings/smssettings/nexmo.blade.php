@extends('layouts.admin')
@section('content')
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
           
         
            <form method="post" action="{{route('admin.updatenexmosetting')}}" class="form-horizontal smssettingform" enctype="multipart/form-data" novalidate="novalidate">
			{{ csrf_field() }}
              <div class="box-body">
             
              	<div class="form-group">
              		<label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.key') }}<span class="text-danger">*</span></label>
              		<div class="col-sm-6">
              			<input class="form-control" type="text" name="nexmo_key" id="sid" placeholder="key" value="{{ $nexmo_key->meta_value ?? '' }}">
              		</div>
              	</div>
              	<div class="form-group">
              		<label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.secrete') }}<span class="text-danger">*</span></label>
              		<div class="col-sm-6">
              			<input class="form-control" type="text" name="nexmo_secret" id="token" placeholder="secret" value="{{ $nexmo_secret->meta_value ?? '' }}">
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
@include('admin.generalSettings.smssettings.toastrmsg')