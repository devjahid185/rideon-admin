@extends('layouts.app')
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
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        {{ trans('global.vendor') }}  {{ trans('global.login') }} to  {{ trans('global.site_title') }}
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

        <form method="POST" action="{{ route('vendor.login') }}">
            @csrf
           
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                <input id="email" type="email" name="email" class="form-control" required autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}" value="{{ old('email', null) }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if($errors->has('email'))
                    <p class="help-block">
                        {{ $errors->first('email') }}
                    </p>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                <input id="password" type="password" name="password" class="form-control" required placeholder="{{ trans('global.login_password') }}">
                <span class="input-group-text toggle-password" onclick="togglePassword()">
                                    <i id="eye-icon" class="fas fa-eye-slash"></i>
                                </span>
                @if($errors->has('password'))
                    <p class="help-block">
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>
            <div class="row">
            <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">
                        {{ trans('global.login') }}
                    </button>
                </div>

                <div class="col-xs-6">
                <a href="{{ url('/vendor/register') }}" id = "" class="btn btn-link">
                                        {{ trans('global.register') }}
                                    </a>
                </div>
                <div class="col-xs-6">
                                    <a href="#" id = "resetPasswordButton" class="btn btn-link">
                                        {{ trans('global.forgot_password') }}
                                    </a>
                                </div>
                
            </div>
        </form>

        <div class="google-login-btn">
                            <a href="{{ route('login.google') }}" class="btn btn-danger btn-block">
                                <i class="fab fa-google"></i> {{ trans('global.login_with_google') }}
                            </a>
                        </div>


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@if (session('status'))
    <script>
        // Check if the session has a flash message
        const message = @json(session('status'));

        // Show SweetAlert with the session message
        Swal.fire({
            icon: 'info',
            title: 'Host Request Pending',
            text: message,  // The message from the session
        });
    </script>
@endif
<script>
document.getElementById('resetPasswordButton').addEventListener('click', function () {
    const currentEmail = document.getElementById('email').value;
    const csrfToken = '{{ csrf_token() }}';

    Swal.fire({
        title: 'Reset Password',
        html: `
            <p>Enter your email. We will send an OTP to your email.</p>
            <input type="email" id="newEmail" class="swal2-input" placeholder="Enter your email" value="${currentEmail}">
            
        `,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Submit',
        preConfirm: () => {
            const email = document.getElementById('newEmail').value;
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                Swal.showValidationMessage('Please enter a valid email address.');
                return false;
            }
            return email;
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const email = result.value;

            // Show custom loader
            document.getElementById('customLoader').style.display = 'block';

            fetch('{{ route('vendor.forgotPassword') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    email: email,
                   
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    // Hide custom loader
                    document.getElementById('customLoader').style.display = 'none';

                    if (data.status === 200) {
                        const otp = data.data.reset_token || '';
                        Swal.fire({
                            title: 'OTP Sent',
                            html: `
                                <p>An OTP has been sent to your email: <strong>${email}</strong></p>
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
                            },
                        }).then((otpResult) => {
                            if (otpResult.isConfirmed) {
                                const otp = otpResult.value;

                                // Show custom loader again for OTP verification
                                document.getElementById('customLoader').style.display = 'block';

                                fetch('{{ route('vendor.verifyResetToken') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                    },
                                    body: JSON.stringify({
                                       
                                        email: email,
                                        reset_token: otp,
                                    }),
                                })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        // Hide custom loader
                                        document.getElementById('customLoader').style.display = 'none';

                                        if (data.status === 200) {
                                            Swal.fire(
                                                'Success',
                                                'Your OTP is verified, now you can reset your password.',
                                                'success'
                                            ).then(() => {
                                                Swal.fire({
                                                    title: 'Create New Password',
                                                    html: `
                                                        <input type="password" id="newPassword" class="swal2-input" placeholder="New Password">
                                                        <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirm Password">
                                                    `,
                                                    showCancelButton: true,
                                                    cancelButtonText: 'Cancel',
                                                    confirmButtonText: 'Submit',
                                                    preConfirm: () => {
                                                        const password = document.getElementById('newPassword').value;
                                                        const confirmPassword = document.getElementById('confirmPassword').value;
                                                        if (!password || !confirmPassword) {
                                                            Swal.showValidationMessage('Please enter both password and confirm password.');
                                                            return false;
                                                        }
                                                        if (password !== confirmPassword) {
                                                            Swal.showValidationMessage('Passwords do not match.');
                                                            return false;
                                                        }
                                                        return { password, confirmPassword };
                                                    },
                                                }).then((passwordResult) => {
                                                    if (passwordResult.isConfirmed) {
                                                        const { password, confirmPassword } = passwordResult.value;

                                                        // Show custom loader
                                                        document.getElementById('customLoader').style.display = 'block';

                                                        fetch('{{ route('vendor.resetPassword') }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': csrfToken,
                                                            },
                                                            body: JSON.stringify({
                                                                email: email,
                                                                reset_token: otp, // Assuming otp is stored from the OTP verification step
                                                                password: password,
                                                                confirm_password: confirmPassword
                                                            }),
                                                        })
                                                            .then((response) => response.json())
                                                            .then((data) => {
                                                                document.getElementById('customLoader').style.display = 'none';

                                                                if (data.status === 200) {
                                                                    Swal.fire(
                                                                        'Success',
                                                                        'Your password has been reset successfully.',
                                                                        'success'
                                                                    ).then(() => {
                                                                        // Redirect to login page or reload
                                                                        location.reload();
                                                                    });
                                                                } else {
                                                                    Swal.fire('Error', data.message || 'Failed to reset password.', 'error');
                                                                }
                                                            })
                                                            .catch((error) => {
                                                                document.getElementById('customLoader').style.display = 'none';
                                                                Swal.fire('Error', 'An error occurred while resetting the password.', 'error');
                                                            });
                                                    }
                                                });
                                            });
                                        } else {
                                            Swal.fire('Error', data.message || 'Invalid OTP. Please try again.', 'error');
                                        }

                                    })
                                    .catch((error) => {
                                        // Hide custom loader
                                        document.getElementById('customLoader').style.display = 'none';
                                        Swal.fire(
                                            'Error',
                                            'An error occurred while verifying the OTP. Please try again later.',
                                            'error'
                                        );
                                    });
                            }
                        });

                        let countdown = 15;
                        const timerText = document.getElementById('timerText');
                        const resendLink = document.getElementById('resendOtpLink');

                        const interval = setInterval(() => {
                            countdown--;
                            const minutes = Math.floor(countdown / 60);
                            const seconds = countdown % 60;

                            timerText.textContent = `Resend the code in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                            if (countdown <= 0) {
                                clearInterval(interval);
                                timerText.style.display = 'none';
                                resendLink.style.display = 'inline';
                            }
                        }, 1000);

                        document.getElementById('resendOtpLink').addEventListener('click', function (e) {
                            e.preventDefault();

                            // Show custom loader for resend OTP request
                            document.getElementById('customLoader').style.display = 'block';

                            //const email = document.getElementById('newEmail').value; // Assuming the email is still available from the previous input field
                            const csrfToken = '{{ csrf_token() }}';

                            fetch('{{ route('vendor.resendTokenForgotPassword') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({
                                    email: email,
                                }),
                            })
                                .then((response) => response.json())
                                .then((data) => {
                                    // Hide custom loader
                                    document.getElementById('customLoader').style.display = 'none';

                                    if (data.status === 200) {
                                        const newOtp = data.data.reset_token || '';
                                        document.getElementById('otpInput').value = newOtp;
                                    } else {
                                        Swal.fire('Error', data.message || 'Failed to resend OTP. Please try again later.', 'error');
                                    }
                                })
                                .catch((error) => {
                                    // Hide custom loader
                                    document.getElementById('customLoader').style.display = 'none';
                                    Swal.fire('Error', 'An error occurred while resending the OTP. Please try again later.', 'error');
                                });
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to reset password.', 'error');
                    }
                })
                .catch((error) => {
                    // Hide custom loader
                    document.getElementById('customLoader').style.display = 'none';
                    Swal.fire('Error', 'An error occurred while sending the OTP.', 'error');
                });
        }
    });
});
</script>
@endsection