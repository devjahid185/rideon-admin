@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{$title}}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route($indexRoute) }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('global.id') }}
                                    </th>
                                    <td>
                                        {{ $city->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.city_name') }}
                                    </th>
                                    <td>
                                        {{ $city->city_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.image') }}
                                    </th>
                                    <td>
                                        @if($city->image)
                                            <a href="{{ $city->image->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $city->image->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.description') }}
                                    </th>
                                    <td>
                                        {!! $city->description !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\City::STATUS_SELECT[$city->status] ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route($indexRoute) }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.relatedData') }}
                </div>
                <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
                    <li role="presentation">
                        <a href="#place_properties" aria-controls="place_properties" role="tab" data-toggle="tab">
                            {{ trans('global.property_title') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" role="tabpanel" id="place_properties">
                        @includeIf('admin.boat-location.relationships.placeProperties', ['properties' => $city->placeProperties])
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection