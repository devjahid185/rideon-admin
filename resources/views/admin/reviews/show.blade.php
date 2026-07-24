@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('global_review.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.reviews.index') }}">
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
                                        {{ $review->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.bookingid') }}
                                    </th>
                                    <td>
                                        {{ $review->bookingid }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.property_name') }}
                                    </th>
                                    <td>
                                        {{ $review->property_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.host_name') }}
                                    </th>
                                    <td>
                                        {{ $review->host_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.rating') }}
                                    </th>
                                    <td>
                                        {{ $review->rating }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.reviews.index') }}">
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