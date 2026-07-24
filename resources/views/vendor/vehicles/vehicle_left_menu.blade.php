
<div class="col-md-3 settings_bar_gap">
 <div class="box box-info box_info">
                <div>
                <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">  {{ trans('global.vehicle_setting') }}</h4>
                    <ul class="nav navbar-pills nav-tabs nav-stacked no-margin d-flex flex-column f-14" role="tablist">
                        <li class="{{ request()->routeIs('vendor.vehicles.base') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.base', ['id' => $id]) }}"
                                data-group="profile"> {{ trans('global.basics') }}</a>
                        </li>

                       <li class="{{ request()->routeIs('vendor.vehicles.description') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.description', ['id' => $id]) }}"
                                data-group="profile">{{ trans('global.title') }} and {{ trans('global.description') }}</a>
                        </li>

                        <li class="{{ request()->routeIs('vendor.vehicles.location') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.location', ['id' => $id]) }}"
                                data-group="profile"> {{ trans('global.location') }}</a>
                        </li>

                         <li class="{{ request()->routeIs('vendor.vehicles.features') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.features', ['id' => $id]) }}"
                                data-group="profile"> {{ trans('global.feature_title') }}</a>
                        </li>
                      

                        <li class="{{ request()->routeIs('vendor.vehicles.photos') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.photos', ['id' => $id]) }}"
                                data-group="profile"> {{ trans('global.photos') }}</a>
                        </li>

                        <li class="{{ request()->routeIs('vendor.vehicles.pricing') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.pricing', ['id' => $id]) }}"
                                data-group="profile"> {{ trans('global.price') }}</a>
                        </li>
                        <li class="{{ request()->routeIs('vendor.vehicles.cancellation-policies') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.cancellation-policies', ['id' => $id]) }}"
                                data-group="profile">{{ trans('global.cancellation_policies_and_rules') }}</a>
                        </li>
                        <li class="{{ request()->routeIs('vendor.vehicles.calendar') ? 'active' : '' }}">
                            <a href="{{ route('vendor.vehicles.calendar', ['id' => $id]) }}"
                                data-group="profile"> {{ trans('global.calendar') }}</a>
                        </li>
                    </ul>
                </div>
            </div> </div>