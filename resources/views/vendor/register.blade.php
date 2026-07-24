@extends('layouts.app')
@section('content')
@section('styles')
<style>
    . __login-badge {
        position: absolute;
        inset-inline-end: 25px;
        top: 25px;
    }

    .badge-soft-success {
        color: #00c9a7;
        background-color: rgba(0, 201, 167, 0.1);
        border: 1px solid rgba(0, 201, 167, 0.6);
    }

    .badge {
        text-transform: capitalize;
    }

    .badge {
        white-space: normal;
    }

    .badge {
        padding: .4em .5em;
        line-height: 14px;
        font-size: .74rem;
    }

    .copy-container {

        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 10px;
        position: relative;
    }

    .copy-container span {
        flex: 1;
    }

    .copy-container button {
        position: relative;
        left: 50%;
        transform: translateX(-50%);
        top: 50%;
        transform: translateY(-50%);
    }
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
<section class="login">
		<div class="login_box">
			<div class="left">
            <label class="badge badge-soft-success __login-badge">
                Software version : 1.2
            </label>
				<div class="contact">
					<div class="login-box">
    <div class="login-logo">
        <a href="{{ route('vendor.login') }}">
        {{ trans('global.register') }} to  {{ trans('global.site_title') }}
        </a>
    </div>
    <div class="login-box-body">
       <!-- <p class="login-box-msg">
            {{ trans('global.login') }}
        </p>-->

        @if(session('message'))
            <p class="alert alert-info">
                {{ session('message') }}
            </p>
        @endif

        <form id="registerForm" method="POST">
    @csrf

    <!-- First Name -->
    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
        <input id="first_name" type="text" name="first_name" class="form-control" required placeholder="{{ trans('global.first_name') }}" value="{{ old('first_name') }}">
        @if($errors->has('first_name'))
            <p class="help-block">
                {{ $errors->first('first_name') }}
            </p>
        @endif
    </div>

    <!-- Last Name -->
    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
        <input id="last_name" type="text" name="last_name" class="form-control" required placeholder="{{ trans('global.last_name') }}" value="{{ old('last_name') }}">
        @if($errors->has('last_name'))
            <p class="help-block">
                {{ $errors->first('last_name') }}
            </p>
        @endif
    </div>

    <!-- Email -->
    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
        <input id="email" type="email" name="email" class="form-control" required autocomplete="email" placeholder="{{ trans('global.email') }}" value="{{ old('email') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if($errors->has('email'))
            <p class="help-block">
                {{ $errors->first('email') }}
            </p>
        @endif
    </div>

    <!-- Password -->
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
        <input id="password" type="password" name="password" class="form-control" required placeholder="{{ trans('global.password') }}">
        <span class="input-group-text toggle-password" onclick="togglePassword()">
                                    <i id="eye-icon" class="fas fa-eye-slash"></i>
                                </span>
            @if($errors->has('password'))
            <p class="help-block">
                {{ $errors->first('password') }}
            </p>
        @endif
    </div>

    <!-- Phone Country -->
    <div class="form-group{{ $errors->has('phone_country') ? ' has-error' : '' }}">
        <div class="input-group">
            <select class="form-control" name="phone_country" id="phone_country" onchange="updateDefaultCountry()">
                @foreach (config('countries') as $country)
                    <option value="{{ $country['dial_code'] }}" @if(old('phone_country') == $country['dial_code']) selected @endif>
                        {{ $country['name'] }} ({{ $country['dial_code'] }})
                    </option>
                @endforeach
            </select>
            
        </div>
        @if($errors->has('phone_country'))
            <span class="help-block" role="alert">{{ $errors->first('phone_country') }}</span>
        @endif
    </div>

    <!-- Default Country (Hidden Field) -->
    <div class="form-group" style="display:none;">
        <input class="form-control" type="text" name="default_country" id="default_country" value="{{ old('default_country') }}" placeholder="{{ trans('global.default_country_placeholder') }}">
        @if($errors->has('default_country'))
            <span class="help-block" role="alert">{{ $errors->first('default_country') }}</span>
        @endif
    </div>

    <!-- Phone -->
    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
        <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="{{ trans('global.phone') }}">
        @if($errors->has('phone'))
            <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
        @endif
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-xs-6">
            <button type="button" id="registerButton" class="btn btn-primary btn-block btn-flat">
            {{ trans('global.register') }}
            </button>
        </div>
        <div class="col-xs-6">
                <a href="{{ url('/vendor/login') }}" id = "" class="btn btn-link">
                                        {{ trans('global.login') }}
                                    </a>
                </div>
    </div>
</form>



      


    </div>
</div>
				</div>
			</div>
			<div class="right">
				<div class="right-text">
                @if ($logoUrl && file_exists(public_path($logoUrl)))
            <img src="{{ $logoUrl }}" alt="{{ $siteName ?? trans('global.site_title') }}" />

            @else
            <b>{{ trans('global.site_title') }}</b>
            @endif
            <!-- <img src="{{ $logoUrl }}" /> -->

            <h5>{{$tagLine}}</h5>
				</div>
				
			</div>
		</div>
	</section>
    <div id="customLoader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
    <div class="loader"></div>
</div>
@endsection

@section('scripts')
</script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>

<script>
    // Update the default_country field based on the phone_country selection
    function updateDefaultCountry() {
        var phoneCountrySelect = document.getElementById("phone_country");
        var defaultCountryInput = document.getElementById("default_country");

        phoneCountrySelect.addEventListener("change", function() {
            var selectedDialCode = phoneCountrySelect.value;
            var countries = @json(config('countries')); // Get the countries data as JSON
            var selectedCountry = countries.find(function(country) {
                return country.dial_code === selectedDialCode;
            });

            if (selectedCountry) {
                defaultCountryInput.value = selectedCountry.code;
            }
        });
    }

    // Initialize the function on page load
    document.addEventListener("DOMContentLoaded", function() {
        updateDefaultCountry();
    });
</script>
<script>
   document.getElementById("registerButton").addEventListener("click", function() {
    let formData = new FormData(document.getElementById("registerForm"));

    fetch("{{ route('vendor.register.request') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.status === 200) {
            const otp = data.data.otp_value || '';
            let countdown = 15;

            Swal.fire({
                title: "Enter OTP",
                html: `
                    <p>An OTP has been sent to your phone: <strong>${data.data.phone}</strong></p>
                    <input type="text" id="otpInput" class="swal2-input" placeholder="Enter OTP" value="${otp}">
                    <br>
                    <p id="timerText">Resend the code in 0:15</p>
                    <a href="#" id="resendOtpLink" style="color: #007bff; text-decoration: underline; font-size: 14px; display: none;">Resend OTP</a>
                `,
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: "Submit OTP",
                preConfirm: () => {
                    const otpValue = document.getElementById('otpInput').value;
                    if (!otpValue) {
                        Swal.showValidationMessage('Please enter the OTP.');
                        return false;
                    }
                    return otpValue;
                }
            }).then((otpResult) => {
                if (otpResult.isConfirmed) {
                    const otpValue = otpResult.value;
                    const phone = data.data.phone;
                    const phoneCountry = data.data.phone_country;

                    fetch("{{ route('vendor.otpVerificationVendor') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify({
                            otp_value: otpValue,        
                            phone: phone,               
                            phone_country: phoneCountry, 
                            token: data.data.token     
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 200) {
                            Swal.fire("Success!", "You are registered successfully! Please log in to continue.", "success").then(() => {
                                    window.location.href = "{{ route('vendor.login') }}";
                                });
                        } else {
                            Swal.fire("Error!", data.message || "Invalid OTP. Please try again.", "error");
                        }
                    })
                    .catch(error => {
                        Swal.fire("Error!", "An error occurred while verifying the OTP. Please try again later.", "error");
                    });
                }
            });

            const timerText = document.getElementById('timerText');
            const resendLink = document.getElementById('resendOtpLink');

            function startCountdown() {
                let countdown = 15;  // 15 seconds
                timerText.style.display = 'inline';  // Show the timer
                resendLink.style.display = 'none';  // Hide the resend link initially

                const interval = setInterval(() => {
                    countdown--;
                    const minutes = Math.floor(countdown / 60);
                    const seconds = countdown % 60;

                    timerText.textContent = `Resend the code in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                    if (countdown <= 0) {
                        clearInterval(interval);
                        timerText.style.display = 'none'; // Hide timer
                        resendLink.style.display = 'inline'; // Show resend link
                    }
                }, 1000);
            }

            startCountdown(); // Start the countdown on initial OTP request

            document.getElementById('resendOtpLink').addEventListener('click', function(e) {
                e.preventDefault();

                document.getElementById('customLoader').style.display = 'block';

                fetch("{{ route('vendor.resendOtpVendor') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({
                        phone: data.data.phone,          
                        phone_country: data.data.phone_country,  
                        token: data.data.token,         
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('customLoader').style.display = 'none';
                  
                    if (data.status === 200) {
                        const newOtp = data.data.otp_value || '';
                      
                        document.getElementById('otpInput').value = newOtp;
                        
                        // Reset countdown and start again
                        startCountdown(); 
                        

                    } else {
                        Swal.fire('Error', data.message || 'Failed to resend OTP. Please try again later.', 'error');
                    }
                })
                .catch(error => {
                    document.getElementById('customLoader').style.display = 'none';
                    Swal.fire('Error', 'An error occurred while resending the OTP. Please try again later.', 'error');
                });
            });
        } else {
            Swal.fire("Error", data.message || "Something went wrong!", "error");
        }
    })
    .catch(error => {
        Swal.fire("Error", "Failed to register. Please try again later.", "error");
    });
});

</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var eyeIcon = document.getElementById("eye-icon");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        }
    }
</script>
@endsection