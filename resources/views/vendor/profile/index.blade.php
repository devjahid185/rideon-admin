@extends('vendor.layout')
@section('content')

@section('styles')
<style>
      .loader {
    border: 8px solid #f3f3f3; /* Light gray background */
    border-top: 8px solid #3498db; /* Blue color for the spinner */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1.5s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

    </style>
@endsection
<div class="content">

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ trans('global.update') }} {{ trans('global.profile') }} 
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('vendor.profile.update', [$appUser->id]) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf

                    <!-- First Row: First Name, Middle Name, Last Name -->
                    <div class="row">
                        <div class="form-group col-md-4 {{ $errors->has('first_name') ? 'has-error' : '' }}">
                            <label class="required" for="first_name">{{ trans('global.first_name') }}</label>
                            <input class="form-control" type="text" name="first_name" id="first_name" value="{{ old('first_name', $appUser->first_name) }}" required>
                            @if($errors->has('first_name'))
                                <span class="help-block" role="alert">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group col-md-4 {{ $errors->has('middle') ? 'has-error' : '' }}">
                            <label for="middle">{{ trans('global.middle') }}</label>
                            <input class="form-control" type="text" name="middle" id="middle" value="{{ old('middle', $appUser->middle) }}">
                            @if($errors->has('middle'))
                                <span class="help-block" role="alert">{{ $errors->first('middle') }}</span>
                            @endif
                        </div>

                        <div class="form-group col-md-4 {{ $errors->has('last_name') ? 'has-error' : '' }}">
                            <label for="last_name">{{ trans('global.last_name') }}</label>
                            <input class="form-control" type="text" name="last_name" id="last_name" value="{{ old('last_name', $appUser->last_name) }}">
                            @if($errors->has('last_name'))
                                <span class="help-block" role="alert">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Second Row: Email, Phone Country, Phone -->
                    <div class="row">
                        {{--<div class="form-group col-md-4 {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="required" for="email">{{ trans('global.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $appUser->email) }}" required>
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                        </div>--}}

                        <div class="form-group col-md-4 {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="required" for="email">{{ trans('global.email') }}</label>
                            <div class="input-group">
                                <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $appUser->email) }}" disabled>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-secondary" id="editEmailButton">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </span>
                            </div>
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        

                        <div class="form-group col-md-4 {{ $errors->has('phone_country') ? 'has-error' : '' }}">
                                <label for="phone_country">{{ trans('global.phone_country') }}</label>
                                <div class="input-group">
                                    <select class="form-control" name="phone_country" id="phone_country" disabled>
                                        @foreach (config('countries') as $country)
                                            <option value="{{ $country['dial_code'] }}"
                                                @if(old('phone_country', $appUser->phone_country) == $country['dial_code']) selected @endif>
                                                {{ $country['name'] }} ({{ $country['dial_code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-secondary" id="editPhoneButton">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </span>
                                </div>
                                @if($errors->has('phone_country'))
                                    <span class="help-block" role="alert">{{ $errors->first('phone_country') }}</span>
                                @endif
                            </div>
                            <div class="col-md-4" style="display:none;">
                                    <label for="default_country">{{ trans('global.default_country') }}</label>
                                    <input class="form-control" type="text" name="default_country" id="default_country" 
                                        value="{{ old('default_country', $appUser->default_country) }}">
                                    @if($errors->has('default_country'))
                                        <span class="help-block" role="alert">{{ $errors->first('default_country') }}</span>
                                    @endif
                                </div>
                            <div class="form-group col-md-4 {{ $errors->has('phone') ? 'has-error' : '' }}">
                                <label class="required" for="phone">{{ trans('global.phone') }}</label>
                                <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $appUser->phone) }}" disabled required>
                                @if($errors->has('phone'))
                                    <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>

                     </div>
                     <!-- Third Row: Password, Status -->
                     <div class="row">
                     <div class="form-group col-md-6 {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="required" for="password">{{ trans('global.password') }}</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password" id="password" placeholder="Suggestion: User@1234" disabled>
                                <span class="input-group-btn">
                                        <button type="button" class="btn btn-secondary" id="editPasswordButton" title="Change Password">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </span>
                                @if($errors->has('password'))
                                    <span class="help-block" role="alert">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group col-md-6 {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label>{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\AppUser::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $appUser->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Fourth Row: Profile Image -->
                    <div class="row">
                        <div class="form-group col-md-12 {{ $errors->has('profile_image') ? 'has-error' : '' }}">
                            <label for="profile_image">{{ trans('global.profile_image') }}</label>
                            <div class="needsclick dropzone" id="profile_image-dropzone"></div>
                            @if($errors->has('profile_image'))
                                <span class="help-block" role="alert">{{ $errors->first('profile_image') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>

<div id="customLoader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
    <div class="loader"></div>
</div>



@endsection

@section('scripts')
<script>
    Dropzone.options.profileImageDropzone = {
    url: '{{ route('admin.app-users.storeMedia') }}',
    maxFilesize: 1, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 1,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="profile_image"]').remove()
      $('form').append('<input type="hidden" name="profile_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="profile_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($appUser) && $appUser->profile_image)
      var file = {!! json_encode($appUser->profile_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="profile_image" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
<script>
    // Update the default_country field based on the phone_country selection
    function updateDefaultCountry() {
        var phoneCountryDialCode = document.getElementById("phone_country").value;
        var countries = @json(config('countries')); // Get the countries data as JSON
        var selectedCountry = countries.find(function(country) {
            return country.dial_code === phoneCountryDialCode;
        });

        // Update the default country input with the selected country code
        if (selectedCountry) {
            document.getElementById("default_country").value = selectedCountry.code;
        }
    }

    // Call the function on page load to set the default country based on the current selection
    document.addEventListener("DOMContentLoaded", function() {
        updateDefaultCountry();
    });
</script>
<script>
document.getElementById('editEmailButton').addEventListener('click', function() {
    const currentEmail = document.getElementById('email').value;
    const csrfToken = '{{ csrf_token() }}';

    Swal.fire({
        title: 'Edit Email',
        html: `
            <input type="email" id="newEmail" class="swal2-input" placeholder="Enter new email" value="${currentEmail}">
        `,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Submit',
        preConfirm: () => {
            const newEmail = document.getElementById('newEmail').value;
            if (!newEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newEmail)) {
                Swal.showValidationMessage('Please enter a valid email address.');
                return false;
            }
            return newEmail;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const newEmail = result.value;

            // Show custom loader
            document.getElementById('customLoader').style.display = 'block';

            fetch('{{ route('vendor.profile.checkEmail') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    email: newEmail,
                    token: '{{ auth()->user()->token }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                // Hide custom loader
                document.getElementById('customLoader').style.display = 'none';

                if (data.status === 200) {
                    const otp = data.data.otp || '';
                    Swal.fire({
                        title: 'OTP Sent',
                        html: `
                            <p>An OTP has been sent to your email: <strong>${newEmail}</strong></p>
                            <input type="text" id="otpInput" class="swal2-input" placeholder="Enter OTP" value="${otp}">
                            <br>
                            <p id="timerText">Resend the code in 0:15</p>
                            <a href="#" id="resendOtpLink" style="color: #007bff; text-decoration: underline; font-size: 14px; display: none;">Resend OTP</a>
                        `,
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        confirmButtonText: 'Verify OTP',
                        preConfirm: () => {
                            const otp = document.getElementById('otpInput').value;
                            if (!otp) {
                                Swal.showValidationMessage('Please enter the OTP.');
                                return false;
                            }
                            return otp;
                        }
                    }).then((otpResult) => {
                        if (otpResult.isConfirmed) {
                            const otp = otpResult.value;

                            // Show custom loader again for OTP verification
                            document.getElementById('customLoader').style.display = 'block';

                            fetch('{{ route('vendor.profile.changeEmail') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({
                                    token: '{{ auth()->user()->token }}',
                                    email: newEmail,
                                    otp_value: otp
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Hide custom loader
                                document.getElementById('customLoader').style.display = 'none';

                                if (data.status === 200) {
                                    Swal.fire('Success', 'Your email has been updated successfully.', 'success')
                                        .then(() => {
                                            // Reload the page after the user clicks 'OK'
                                            location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', data.message || 'Invalid OTP. Please try again.', 'error');
                                }

                            })
                            .catch(error => {
                                // Hide custom loader
                                document.getElementById('customLoader').style.display = 'none';
                                Swal.fire('Error', 'An error occurred while verifying the OTP. Please try again later.', 'error');
                            });
                        }
                    });

                    document.getElementById('resendOtpLink').addEventListener('click', function(e) {
                        e.preventDefault();

                        // Show custom loader for resend OTP request
                        document.getElementById('customLoader').style.display = 'block';

                        fetch('{{ route('vendor.profile.ResendTokenEmailChange') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                token: '{{ auth()->user()->token }}',
                                email: newEmail
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide custom loader
                            document.getElementById('customLoader').style.display = 'none';

                            if (data.status === 200) {
                                const newOtp = data.data.reset_token || '';
                                document.getElementById('otpInput').value = newOtp;
                            } else {
                                Swal.fire('Error', data.message || 'Failed to resend OTP. Please try again later.', 'error');
                            }
                        })
                        .catch(error => {
                            // Hide custom loader
                            document.getElementById('customLoader').style.display = 'none';
                            Swal.fire('Error', 'An error occurred while resending the OTP. Please try again later.', 'error');
                        });
                    });
                    let countdown = 15; // Initial countdown value (15 seconds)
                        const timerText = document.getElementById('timerText');
                        const resendLink = document.getElementById('resendOtpLink');

                        const interval = setInterval(() => {
                            countdown--;
                            const minutes = Math.floor(countdown / 60);
                            const seconds = countdown % 60;

                            timerText.textContent = `Resend the code in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                            if (countdown <= 0) {
                                clearInterval(interval);
                                timerText.style.display = 'none'; // Hide the timer
                                resendLink.style.display = 'inline'; // Show the "Resend OTP" link
                            }
                        }, 1000); 
                } else {
                    Swal.fire('Error', data.message || 'Failed to update email.', 'error');
                }
            })
            .catch(error => {
                // Hide custom loader
                document.getElementById('customLoader').style.display = 'none';
                Swal.fire('Error', 'An error occurred while sending the OTP.', 'error');
            });
        }
    });
});

</script>
<script>
document.getElementById('editPhoneButton').addEventListener('click', function() {
    const currentPhone = document.getElementById('phone').value;
    const currentCountryCode = document.getElementById('phone_country').value;
    const defaultCountry = document.getElementById('default_country').value;  // This is hidden but needs to be passed
    const csrfToken = '{{ csrf_token() }}';

    Swal.fire({
    title: 'Edit Phone Number',
    html: `
        <div class="row">
            <div class="col-md-6">
                <select class="form-control" id="newPhoneCountry" required>
                    @foreach (config('countries') as $country)
                        <option value="{{ $country['dial_code'] }}" 
                            @if(old('phone_country', $appUser->phone_country) == $country['dial_code']) selected @endif>
                             {{ $country['name'] }} ({{ $country['dial_code'] }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="tel" id="newPhone" class="form-control" placeholder="Enter new phone number" value="${currentPhone}" required>
            </div>
        </div>
        <input type="hidden" id="defaultCountry" name="default_country" value="{{ old('default_country', $appUser->default_country) }}">
    `,
    showCancelButton: true,
    cancelButtonText: 'Cancel',
    confirmButtonText: 'Submit',
    didOpen: () => {
        const phoneCountryDropdown = document.getElementById('newPhoneCountry');
        const defaultCountryField = document.getElementById('defaultCountry');
        const newPhoneField = document.getElementById('newPhone');
        // Load country data
        const countries = @json(config('countries')); // Get the countries data as JSON

        function updateDefaultCountry() {
            const selectedDialCode = phoneCountryDropdown.value;
            const selectedCountry = countries.find(country => country.dial_code === selectedDialCode);

            if (selectedCountry) {
                defaultCountryField.value = selectedCountry.code;
            
            } else {
                defaultCountryField.value = '';
            }
            
        }

        // Initialize default country on modal open
        updateDefaultCountry();

        phoneCountryDropdown.addEventListener('change', updateDefaultCountry);
    },
    preConfirm: () => {
        const newPhone = document.getElementById('newPhone').value;
        const newCountryCode = document.getElementById('newPhoneCountry').value;
        const defaultCountry = document.getElementById('defaultCountry').value;

        if (!newPhone || !/^\+?[0-9]{8,12}$/.test(newPhone)) {
            Swal.showValidationMessage('Please enter a valid phone number.');
            return false;
        }
        return { phone: newPhone, country_code: newCountryCode, default_country: defaultCountry };
    }
    }).then((result) => {
        if (result.isConfirmed) {
            const { phone: newPhone, country_code: newCountryCode, default_country } = result.value;

            // Show custom loader
            document.getElementById('customLoader').style.display = 'block';

            fetch('{{ route('vendor.profile.checkMobileNumber') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    phone: newPhone,
                    phone_country: newCountryCode,
                    default_country: default_country,  // Pass default_country as well
                    token: '{{ auth()->user()->token }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                // Hide custom loader
                document.getElementById('customLoader').style.display = 'none';

                if (data.status === 200) {
                    const otp = data.data.otp || '';
                    Swal.fire({
                        title: 'OTP Sent',
                        html: `
                            <p>An OTP has been sent to your phone: <strong>${newPhone}</strong></p>
                            <input type="text" id="otpInput" class="swal2-input" placeholder="Enter OTP" value="${otp}">
                            <br>
                            <p id="timerText">Resend the code in 0:15</p>
                            <a href="#" id="resendOtpLink" style="color: #007bff; text-decoration: underline; font-size: 14px; display: none;">Resend OTP</a>
                        `,
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        confirmButtonText: 'Verify OTP',
                        preConfirm: () => {
                            const otp = document.getElementById('otpInput').value;
                            if (!otp) {
                                Swal.showValidationMessage('Please enter the OTP.');
                                return false;
                            }
                            return otp;
                        }
                    }).then((otpResult) => {
                        if (otpResult.isConfirmed) {
                            const otp = otpResult.value;

                            // Show custom loader again for OTP verification
                            document.getElementById('customLoader').style.display = 'block';

                            fetch('{{ route('vendor.profile.changeMobileNumber') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({
                                    token: '{{ auth()->user()->token }}',
                                    phone: newPhone,
                                    phone_country: newCountryCode,
                                    otp_value: otp,
                                    default_country: default_country  // Send default_country in the final request
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Hide custom loader
                                document.getElementById('customLoader').style.display = 'none';

                                if (data.status === 200) {
                                    Swal.fire('Success', 'Your phone number has been updated successfully.', 'success')
                                        .then(() => {
                                            // Reload the page after the user clicks 'OK'
                                            location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', data.message || 'Invalid OTP. Please try again.', 'error');
                                }

                            })
                            .catch(error => {
                                // Hide custom loader
                                document.getElementById('customLoader').style.display = 'none';
                                Swal.fire('Error', 'An error occurred while verifying the OTP. Please try again later.', 'error');
                            });
                        }
                    });

                    document.getElementById('resendOtpLink').addEventListener('click', function(e) {
                        e.preventDefault();

                        // Show custom loader for resend OTP request
                        document.getElementById('customLoader').style.display = 'block';

                        fetch('{{ route('vendor.profile.ResendToken') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                token: '{{ auth()->user()->token }}',
                                phone: newPhone,
                                phone_country: newCountryCode,
                                default_country: default_country  // Pass default_country in resend OTP request
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide custom loader
                            document.getElementById('customLoader').style.display = 'none';

                            if (data.status === 200) {
                                const newOtp = data.data.reset_token || '';
                                document.getElementById('otpInput').value = newOtp;
                            } else {
                                Swal.fire('Error', data.message || 'Failed to resend OTP. Please try again later.', 'error');
                            }
                        })
                        .catch(error => {
                            // Hide custom loader
                            document.getElementById('customLoader').style.display = 'none';
                            Swal.fire('Error', 'An error occurred while resending the OTP. Please try again later.', 'error');
                        });
                    });

                    let countdown = 15; // Initial countdown value (15 seconds)
                    const timerText = document.getElementById('timerText');
                    const resendLink = document.getElementById('resendOtpLink');

                    const interval = setInterval(() => {
                        countdown--;
                        const minutes = Math.floor(countdown / 60);
                        const seconds = countdown % 60;

                        timerText.textContent = `Resend the code in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                        if (countdown <= 0) {
                            clearInterval(interval);
                            timerText.style.display = 'none'; // Hide the timer
                            resendLink.style.display = 'inline'; // Show the "Resend OTP" link
                        }
                    }, 1000);
                } else {
                    Swal.fire('Error', data.message || 'Failed to update phone number.', 'error');
                }
            })
            .catch(error => {
                // Hide custom loader
                document.getElementById('customLoader').style.display = 'none';
                Swal.fire('Error', 'An error occurred while sending the OTP.', 'error');
            });
        }
    });
});
</script>
<script>
    const csrfToken = '{{ csrf_token() }}'; // CSRF Token from Laravel
    const userToken = '{{ auth()->user()->token }}'; // User's token from Laravel
</script>
<script>
document.getElementById('editPasswordButton').addEventListener('click', function() {
    Swal.fire({
        title: 'Change Password',
        html: `
            <input type="password" id="oldPassword" class="swal2-input" placeholder="Old Password" required>
            <input type="password" id="newPassword" class="swal2-input" placeholder="New Password" required>
            <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirm New Password" required>
        `,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Submit',
        preConfirm: () => {
            const oldPassword = document.getElementById('oldPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!oldPassword || !newPassword || !confirmPassword) {
                Swal.showValidationMessage('Please fill in all fields.');
                return false;
            }

            if (newPassword !== confirmPassword) {
                Swal.showValidationMessage('New password and confirm password do not match.');
                return false;
            }

            return {
                oldPassword: oldPassword,
                newPassword: newPassword,
                confirmPassword: confirmPassword
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { oldPassword, newPassword, confirmPassword } = result.value;

            // Show custom loader (optional)
            document.getElementById('customLoader').style.display = 'block';

            // Send the data via AJAX to the server
            fetch('{{ route('vendor.profile.updatePassword') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // CSRF token
                },
                body: JSON.stringify({
                    token: userToken, // User's token
                    old_password: oldPassword,
                    new_password: newPassword,
                    conf_new_password: confirmPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle success or error
                document.getElementById('customLoader').style.display = 'none'; // Hide the loader
                if (data.status === 200) {
                    Swal.fire('Success!', 'Password updated successfully!', 'success');
                } else {
                    Swal.fire('Error!', data.error || 'Something went wrong!', 'error');
                }
            })
            .catch(error => {
                document.getElementById('customLoader').style.display = 'none'; // Hide the loader
                Swal.fire('Error!', 'An error occurred, please try again later.', 'error');
            });
        }
    });
});

</script>
@endsection