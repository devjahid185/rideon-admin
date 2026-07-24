@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.property.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.properties.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $property->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $property->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.userid') }}
                                    </th>
                                    <td>
                                        {{ $property->userid->first_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.property_type') }}
                                    </th>
                                    <td>
                                        {{ $property->property_type->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.property_rating') }}
                                    </th>
                                    <td>
                                        {{ $property->property_rating }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::STATUS_SELECT[$property->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.price') }}
                                    </th>
                                    <td>
                                        {{ $property->price }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.place') }}
                                    </th>
                                    <td>
                                        {{ $property->place->city_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.is_verified') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::IS_VERIFIED_SELECT[$property->is_verified] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.is_featured') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::IS_FEATURED_SELECT[$property->is_featured] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.weekly_discount') }}
                                    </th>
                                    <td>
                                        {{ $property->weekly_discount }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.weekly_discount_type') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::WEEKLY_DISCOUNT_TYPE_SELECT[$property->weekly_discount_type] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.monthly_discount') }}
                                    </th>
                                    <td>
                                        {{ $property->monthly_discount }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.property.fields.monthly_discount_type') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::MONTHLY_DISCOUNT_TYPE_SELECT[$property->monthly_discount_type] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('cruds.property.fields.amenities') }}
                                    </th>  
                                    @php
                                            $amenity_names = [];
                                            foreach($amenities_ids as $amenity_id) {
                                                $amenity = \App\Models\Amenity::find($amenity_id);
                                                if ($amenity) {
                                                    $amenity_names[] = $amenity->name;
                                                }
                                            }
                                            $amenities_list = implode(', ', $amenity_names);
                                        @endphp

                                        <td>{{ $amenities_list }}</td>

                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.properties.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection