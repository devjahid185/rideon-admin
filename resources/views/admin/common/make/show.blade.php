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
                                        {{ $makeData->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.name') }}
                                    </th>
                                    <td>
                                        {{ $makeData->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.description') }}
                                    </th>
                                    <td>
                                        {{ $makeData->description }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\VehicleMake::STATUS_SELECT[$makeData->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.image') }}
                                    </th>
                                    <td>
                                        @if($makeData->image)
                                            <a href="{{ $makeData->image->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $makeData->image->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.vehicle-makes.index') }}">
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