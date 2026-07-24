@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('global.module') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.module.index') }}">
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
                                        {{ $module->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.name') }}
                                    </th>
                                    <td>
                                        {{ $module->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.description') }}
                                    </th>
                                    <td>
                                        {{ $module->description }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Module::STATUS_SELECT[$module->status] ?? '' }}
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.module.index') }}">
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