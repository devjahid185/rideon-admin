@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('global.appUser_title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.app-users.index') }}">
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
                                        {{ $appUser->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.first_name') }}
                                    </th>
                                    <td>
                                        {{ $appUser->first_name }}
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>
                                        {{ trans('global.last_name') }}
                                    </th>
                                    <td>
                                        {{ $appUser->last_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.email') }}
                                    </th>
                                    <td>
                                        @php
                                            $email = $appUser->email;
                                            $emailParts = explode('@', $email);
                                            $username = $emailParts[0];
                                            $domain = $emailParts[1];

                                            // Mask the username
                                            if (strlen($username) > 6) {
                                                $maskedUsername = substr($username, 0, 5) . str_repeat('*', strlen($username) - 4) . substr($username, -2);
                                            } else {
                                                $maskedUsername = $username; // If username is short, do not mask
                                            }

                                            // Limit the length of the masked username to 20 characters
                                            if (strlen($maskedUsername) > 10) {
                                                $maskedUsername = substr($maskedUsername, 0, 8) . '...';
                                            }

                                            // Shorten the domain name if it's greater than 10 characters
                                            if (strlen($domain) > 15) {
                                                $maskedDomain = '...'.substr($domain, -13);
                                            } else {
                                                $maskedDomain = $domain;
                                            }

                                            // Combine masked username with shortened domain
                                            $maskedEmail = $maskedUsername . '@' . $maskedDomain;
                                        @endphp
                                        @can('app_user_contact_access')
                                          
                                          {{ $email }}
                                      @else
                                          
                                          {{ $maskedEmail }}
                                      @endcan
                                        </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.phone') }}
                                    </th>
                                    <td>
                                    {{ $appUser->phone_country ?? '' }} 
                                    @can('app_user_contact_access')
                                          
                                          {{ $appUser->phone ?? '' }} 
                                          @else
                                              
                                          {{ $appUser->phone ? substr($appUser->phone, 0, -6) . str_repeat('*', 6) : '' }}
                                          @endcan
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.profile_image') }}
                                    </th>
                                    <td>
                                        @if($appUser->profile_image)
                                            <a href="{{ $appUser->profile_image->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $appUser->profile_image->getUrl('thumb') }}">
                                            </a>
                                            @else
                                                    <img src="{{ asset('public/images/icon/userdefault.jpg') }}" alt="Default Image" style="display: inline-block;">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\AppUser::STATUS_SELECT[$appUser->status] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.package') }}
                                    </th>
                                    <td>
                                        {{ $appUser->package->package_name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.app-users.index') }}">
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