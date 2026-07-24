@extends('layouts.admin')
@section('styles')
<style>
        .dataTables_info{
            display: none;
        }
        .paging_simple_numbers{
            display: none;
        }
        .pagination.justify-content-end{
            float: right;
        }
        
    </style>
@endsection
@section('content')
@php $i = 0; $j = 0; @endphp
<div class="content">
    @can('property_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.vehicles.create') }}">
                    {{ trans('global.add') }} {{ trans('global.vehicle') }}
                </a>
            </div>
        </div>
    @endcan
 

    
    <div class="row">

    <div class="col-lg-12">
				<div class="box">
					<div class="box-body"> 
					<form class="form-horizontal" id="propertyFilterForm" action="" method="GET" accept-charset="UTF-8">
						
						<div>
                            <input class="form-control" type="hidden" id="startDate" name="from" value="">
                            <input class="form-control" type="hidden" id="endDate" name="to" value="">
                        </div>
						
					
						<div class="row">
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.date_range') }}</label>
                            <div class="input-group col-xs-12">
                              
                                <input type="text" class="form-control" id="daterange-btn">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
								<div class="col-md-3 col-sm-12 col-xs-12">
                                        <label> {{ trans('global.status') }}</label>
                                        <select class="form-control select2" name="status" id="status">
                                        <option value="">All</option>
                                        <option value="active" {{ request()->input('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request()->input('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="verified" {{ request()->input('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="featured" {{ request()->input('status') === 'featured' ? 'selected' : '' }}>Featured</option>
                                    </select>

								</div>
								
                                <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>{{ trans('global.host') }}</label>
                            <select class="form-control select2" name="customer" id="customer">
                                <option value="">{{ $customername }}</option>
                               
                            </select>
                        </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>  {{ trans('global.title') }}</label>
                                    <input type="text" class="form-control" name ="title" value="{{ $property_title ?: '' }}">
                                    
                                </div>


								
								<div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">
                                        <br>
                                        <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">{{ trans('global.filter') }}</button>
                                         <button type="button" id="resetBtn"  class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
                                        </div>
                                        
							</div>
						
						</div>
					</form>
                 </div> 
                
                    </div>
                    @include('admin.common.liveTrashSwitcher') 

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.vehicle') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable  datatable-Property">
                            <thead>
                                <tr>
                                <th>
                                      
                                    </th>
                                    <th>
                                        {{ trans('global.id') }}#
                                    </th>
                                    <th>
                                        {{ trans('global.title') }}
                                    </th>
                                    <th>
                                       {{ trans('global.host') }}
                                    </th>
                                    <th>
                                       {{ trans('global.image') }}
                                    </th>
                                    <!-- <th>
                                        {{ trans('global.appUser.fields.middle') }}
                                    </th> -->
                                    <!-- <th>
                                        {{ trans('global.property_type') }}
                                    </th> -->
                                    <!-- <th>
                                        {{ trans('global.property_rating') }}
                                    </th> -->
                                    <th width="50">
                                        {{ trans('global.price') }}
                                    </th>
                                    <th>
                                   {{ trans('global.place') }}
                                    </th>
                                    <th>
                                        {{ trans('global.is_verified') }}
                                    </th>
                                    <th>
                                        {{ trans('global.is_featured') }}
                                    </th>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <!-- <th>
                                        {{ trans('global.weekly_discount') }}
                                    </th>
                                    <th>
                                        {{ trans('global.weekly_discount_type') }}
                                    </th>
                                    <th>
                                        {{ trans('global.monthly_discount') }}
                                    </th>
                                    <th>
                                        {{ trans('global.monthly_discount_type') }}
                                    </th> -->
                                    <th>   {{ trans('global.actions') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($properties as $key => $property)
                                    <tr >
                                    <td>
                                          
                                        </td>
                                        <td>
                                            #{{ $property->id ?? '' }}
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.vehicles.base', ['id' => $property->id]) }}">
                                            {{ $property->title ?? '' }}
                                        </a>
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.overview', $property->userid_id) }}">
                                            {{ $property->userid->first_name ?? '' }} {{ $property->userid->last_name ?? '' }}
                                            </a>
                                        </td>
                                        <td>
                                        @if($property->front_image)
                                            <a href="{{ $property->front_image->url}}">
                                                <img src="{{ $property->front_image->thumbnail }}" alt="{{ $property->title }}" class="item-image-size">
                                                </a>
                                           
                                            @endif
                                        </td>
                                        <!-- <td>
                                            {{ $property->userid->middle ?? '' }}
                                        </td> -->
                                        <!-- <td>
                                            {{ $property->property_type->name ?? '' }}
                                        </td> -->
                                        <!-- <td>
                                            {{ $property->property_rating ?? '' }}
                                        </td> -->
                                       
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($property->price ?? '') }}

                                        </td>
                                        <td>
                                            {{ $property->place->city_name ?? '' }}
                                        </td>
                                        <td>
                                     
                                            <!-- {{ App\Models\Property::IS_VERIFIED_SELECT[$property->is_verified] ?? '' }} -->
                                           <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$property->id}}" class="check isvefifieddata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $property->is_verified ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div> 
                                        </td>
                                        <td>
                                     
                                            <!-- {{ App\Models\Property::IS_FEATURED_SELECT[$property->is_featured] ?? '' }} -->
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$property->id}}" class="check isfeatureddata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $property->is_featured ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                      
                                            <!-- {{ App\Models\Property::STATUS_SELECT[$property->status] ?? '' }} -->
                                         <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$property->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $property->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <!-- <td>
                                            {{ $property->weekly_discount ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Property::WEEKLY_DISCOUNT_TYPE_SELECT[$property->weekly_discount_type] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $property->monthly_discount ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Property::MONTHLY_DISCOUNT_TYPE_SELECT[$property->monthly_discount_type] ?? '' }}
                                        </td> -->
                                        <td>
                                            @can('property_edit')
                                                <a  style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info" href="{{ route('admin.vehicles.base', ['id' => $property->id]) }}">
                                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            @endcan

                                            @can('property_delete')
                                            <form action="{{ route('admin.vehicles.destroy', $property->id) }}" method="POST" style="display: inline-block;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="button" class="btn btn-xs btn-danger delete-button" data-id="{{ $property->id }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endcan

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="...">
    <ul class="pagination justify-content-end">
        {{-- Previous Page Link --}}
        @if ($properties->currentPage() > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $properties->previousPageUrl() }}" tabindex="-1"> {{ trans('global.previous') }}</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link"> {{ trans('global.previous') }}</span>
            </li>
        @endif

        {{-- Numeric Pagination Links --}}
        @for ($i = 1; $i <= $properties->lastPage(); $i++)
            <li class="page-item {{ $i == $properties->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $properties->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Next Page Link --}}
        @if ($properties->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $properties->nextPageUrl() }}"> {{ trans('global.next') }}</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link"> {{ trans('global.next') }}</span>
            </li>
        @endif
    </ul>
</nav>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>

@endsection

@include('admin.common.addSteps.footer.footerJs')