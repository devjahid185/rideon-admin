@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('global.property_title') }}
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
                                        {{ trans('global.id') }}sadz
                                    </th>
                                    <td>
                                        {{ $property->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.title') }}
                                    </th>
                                    <td>
                                        {{ $property->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.userid') }}
                                    </th>
                                    <td>
                                        {{ $property->userid->first_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.property_type') }}
                                    </th>
                                    <td>
                                        {{ $property->property_type->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.property_rating') }}
                                    </th>
                                    <td>
                                        {{ $property->property_rating }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::STATUS_SELECT[$property->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.price') }}
                                    </th>
                                    <td>
                                        {{ $property->price }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.place') }}
                                    </th>
                                    <td>
                                        {{ $property->place->city_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.is_verified') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::IS_VERIFIED_SELECT[$property->is_verified] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.is_featured') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::IS_FEATURED_SELECT[$property->is_featured] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.weekly_discount') }}
                                    </th>
                                    <td>
                                        {{ $property->weekly_discount }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.weekly_discount_type') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::WEEKLY_DISCOUNT_TYPE_SELECT[$property->weekly_discount_type] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.monthly_discount') }}
                                    </th>
                                    <td>
                                        {{ $property->monthly_discount }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.monthly_discount_type') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Property::MONTHLY_DISCOUNT_TYPE_SELECT[$property->monthly_discount_type] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.features') }}
                                    </th>  
                                    @php
                                            $feature_names = [];
                                            foreach($features_ids as $features_id) {
                                                $feature = \App\Models\Modern\ItemFeatures::find($features_id);
                                                if ($feature) {
                                                    $feature_names[] = $feature->name;
                                                }
                                            }
                                            $features_list = implode(', ', $feature_names);
                                        @endphp

                                        <td>{{ $features_list }}</td>

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