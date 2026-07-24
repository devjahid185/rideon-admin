@extends('layouts.admin')
@section('content')
<div class="content">

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
        <div class="col-lg-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.generalSetting.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-GeneralSetting">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.generalSetting.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.generalSetting.fields.meta_key') }}
                                </th>
                                <th>
                                    {{ trans('cruds.generalSetting.fields.meta_value') }}
                                </th>
                                <th>
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  
  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.general-settings.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'meta_key', name: 'meta_key' },
{ data: 'meta_value', name: 'meta_value' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-GeneralSetting').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection