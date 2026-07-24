@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.show') }} {{ trans('global.contacttitle_singular') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.contacts.index') }}">
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
                                        {{ $contact->id ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.title') }}
                                    </th>
                                    <td>
                                        {{ $contact->tittle ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.description') }}
                                    </th>
                                    <td>
                                        {{ $contact->description ?? ''}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.user') }}
                                    </th>
                                    <td>{{ $contact->appUser->first_name ?? '' }} {{ $contact->appUser->last_name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>
                                    {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        @if($contact->status == 1)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                     </td>
                                </tr>
                               
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.contacts.index') }}">
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