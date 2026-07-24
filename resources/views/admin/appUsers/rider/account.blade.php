@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}" media="screen">
@endsection
@section('content')
    <div class="container-fluid content">
        @include('admin.appUsers.rider.menu')
        <div class="driver-profile-page">
            <div class="profile-container">
                <div class="row g-3 text-capitalize align-items-center">
                    @php
                        $toggles = [
                            ['label' => __('user.profile_verify'), 'field' => 'status', 'value' => $appUser->status, 'class' => 'profileVerify'],
                            ['label' => __('user.email_verify'), 'field' => 'email_verify', 'value' => $appUser->email_verify, 'class' => 'emailVerify'],
                            ['label' => __('user.phone_verify'), 'field' => 'phone_verify', 'value' => $appUser->phone_verify, 'class' => 'phoneVerify']
                        ];
                    @endphp

                    @foreach($toggles as $toggle)
                        <div class="col-md-4 form-group">
                            <label for="{{ $toggle['field'] }}">{{ $toggle['label'] }}</label>
                            <div class="custom-toggle inline-block">
                                <label class="switch">
                                    <input type="checkbox" data-id="{{ $appUser->id }}" class="{{ $toggle['class'] }}"
                                        data-toggle="toggle" data-on="Active" data-off="InActive" {{ $toggle['value'] == 1 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="row g-3">
                    <form method="POST"
                        action="{{ route('admin.rider.account.update', $appUser->id) }}?from_overviewprofile=true"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_type" value="{{ request('user_type') }}">
                        <div class="col-md-12">
                            <h3 class="section-title mb-0">{{ trans('user.driver_information') }}</h3>
                        </div>
                        {{-- First Name --}}
                        <div class="col-md-6 form-group">
                            <label class="required" for="first_name">{{ __('user.first_name') }}</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" id="first_name" value="{{ old('first_name', $appUser->first_name) }}"
                                required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="col-md-6 form-group">
                            <label for="last_name">{{ __('user.last_name') }}</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" id="last_name" value="{{ old('last_name', $appUser->last_name) }}">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6 form-group">
                            <label for="email">{{ __('user.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" value="{{ old('email', $appUser->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6 form-group">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="phone_country">{{ __('user.phone_country') }}</label>
                                    <select name="phone_country" id="phone_country"
                                        class="form-control @error('phone_country') is-invalid @enderror"
                                        onchange="updateDefaultCountry()">
                                        @foreach (config('countries') as $country)
                                            <option value="{{ $country['dial_code'] }}" {{ old('phone_country', $appUser->phone_country) == $country['dial_code'] ? 'selected' : '' }}>
                                                {{ $country['name'] }} ({{ $country['dial_code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('phone_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="required" for="phone">{{ __('user.phone') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" id="phone" value="{{ old('phone', $appUser->phone) }}" required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <input type="hidden" name="default_country" id="default_country"
                                value="{{ old('default_country', $appUser->default_country) }}">
                        </div>

                        {{-- Profile Image --}}
                        <div class="col-md-12 form-group">
                            <label for="profile_image">{{ __('global.profile_image') }}</label>
                            <div class="needsclick dropzone @error('profile_image') is-invalid @enderror"
                                id="profile_image-dropzone"></div>
                            @error('profile_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>


                        {{-- Submit --}}
                        <div class="col-md-12 form-group">
                            <button class="btn btn-primary btn-lg w-100 py-3" type="submit">
                                {{ __('global.Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        Dropzone.options.profileImageDropzone = {
            url: '{{ route('admin.app-users.storeMedia') }}',
            maxFilesize: 1,
            acceptedFiles: 'image/jpeg,image/png,image/gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            params: { size: 1, width: 4096, height: 4096 },
            success(file, response) {
                const form = file.previewElement.closest('form');
                form.querySelector('input[name="profile_image"]')?.remove();
                form.insertAdjacentHTML('beforeend', `<input type="hidden" name="profile_image" value="${response.name}">`);
            },
            removedfile(file) {
                file.previewElement.remove();
                if (file.status !== 'error') {
                    file.previewElement.closest('form').querySelector('input[name="profile_image"]')?.remove();
                    this.options.maxFiles++;
                }
            },
            init() {
                @if($appUser?->profile_image)
                    const file = @json($appUser->profile_image);
                    this.options.addedfile.call(this, file);
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url);
                    file.previewElement.classList.add('dz-complete');
                    this.element.closest('form').insertAdjacentHTML('beforeend',
                        `<input type="hidden" name="profile_image" value="${file.file_name}">`);
                    this.options.maxFiles--;
                @endif
            },
            error(file, response) {
                const message = typeof response === 'string' ? response : response.errors.file;
                file.previewElement.classList.add('dz-error');
                file.previewElement.querySelector('[data-dz-errormessage]').textContent = message;
            }
        };

        function updateDefaultCountry() {
            const dialCode = document.getElementById('phone_country').value;
            const country = @json(config('countries')).find(c => c.dial_code === dialCode);
            if (country) {
                document.getElementById('default_country').value = country.code;
            }
        }

        document.addEventListener('DOMContentLoaded', updateDefaultCountry);

        function setupToggle(selector, field, url) {
                $(selector).change(function () {
                    const value = $(this).prop('checked') ? 1 : 0;
                    const id = $(this).data('id');
                    const data = { _token: $('meta[name="csrf-token"]').attr('content'), pid: id };
                    data[field] = value;

                    $.post(url, data, function (response) {
                        toastr.success(response.message, '{{ trans('global.success') }}', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    });
                });
            }

    setupToggle('.profileVerify', 'status', '/admin/driver/account/profileVerify/{{$appUser->id}}');
    setupToggle('.emailVerify', 'email_verify', '/admin/driver/account/emailVerify/{{$appUser->id}}');
    setupToggle('.documentVerify', 'document_verify', '/admin/driver/account/documentVerify/{{$appUser->id}}');
    setupToggle('.phoneVerify', 'phone_verify', '/admin/driver/account/phoneVerify/{{$appUser->id}}');
    </script>
@endsection