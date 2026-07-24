@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('global.allPackage_title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.all-packages.index') }}">
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
                                        {{ $allPackage->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.package_name') }}
                                    </th>
                                    <td>
                                        {{ $allPackage->package_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.package_total_day') }}
                                    </th>
                                    <td>
                                        {{ $allPackage->package_total_day }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.package_price') }}
                                    </th>
                                    <td>
                                        {{ $allPackage->package_price }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.package_description') }}
                                    </th>
                                    <td>
                                        {!! $allPackage->package_description !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.package_image') }}
                                    </th>
                                    <td>
                                        @if($allPackage->package_image)
                                            <a href="{{ $allPackage->package_image->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $allPackage->package_image->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\AllPackage::STATUS_SELECT[$allPackage->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.max_item') }}
                                    </th>
                                    <td>
                                        {{ $allPackage->max_item }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.all-packages.index') }}">
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