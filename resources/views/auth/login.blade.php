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
</style>
@endsection
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<section class="login page-login">
    <div class="login_box">
        <div class="left">
            <!-- <label class="badge badge-soft-success __login-badge">
                    Software version : 1.0
                </label> -->
            <div class="contact">
                <div class="login-box">
                    @if($error = session()->pull('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                    @endif
                    <div class="login-logo">

                        <h2>Admin Signin</h2>

                        <p style="font-size: medium;">Welcome back login to your panel.</p>
                    </div>
                    <div class="login-box-body">
                        @if (session('message'))
                        <p class="alert alert-info">
                            {{ session('message') }}
                        </p>
                        @endif

                        <div id="loader" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                        <form method="POST" name="loginform" id="loginform" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                                <input id="email" type="email" name="email" class="email form-control" required
                                    autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}"
                                    value="{{ old('email', null) }}">
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                <span class="credentail"></span>
                                <span class="email"></span>
                                @if($errors->has('email'))
                                <p class="help-block">
                                    {{ $errors->first('email') }}
                                </p>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                                <div class="fas fa-lock form-control-feedback"></div>
                                <input id="password" type="password" name="password" class="password form-control"
                                    required placeholder="{{ trans('global.login_password') }}">
                                <span class="input-group-text toggle-password" onclick="togglePassword()">
                                    <i id="eye-icon" class="fas fa-eye-slash"></i>
                                </span>
                                @if ($errors->has('password'))
                                <p class="help-block">
                                    {{ $errors->first('password') }}
                                </p>
                                @endif
                            </div>
                            <div class="form-actions">
                                <div class="remember-me">
                                    <div class="checkbox ">
                                        <label><input type="checkbox" name="remember">
                                            {{ trans('global.remember_me') }}</label>
                                    </div>
                                </div>
                                <div class="forgot-password-link">
                                    <a href="{{ route('password.request') }}" class="btn btn-link">
                                        {{ trans('global.forgot_password') }}
                                    </a>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }} has-feedback">
                                @if($general_captcha == 'yes')
                                <div class="g-recaptcha" data-sitekey="{{$site_key}}">
                                </div>
                                <p class="captchamsg"></p>

                                @if($errors->has('g-recaptcha-response'))
                                <p class="help-block">
                                    {{ $errors->first('g-recaptcha-response') }}
                                </p>
                                @endif

                                @endif
                                <button type="submit" class="btn btn-primary btn-block btn-flat">
                                    {{ trans('global.login') }}
                                </button>
                                <br>
                                <br>
                                <div class="row">
                                    @include('admin.demo.demo-user')
                                </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right-text">
        @if (!empty($logoUrl))
        <img src="{{ url($logoUrl) }}" alt="{{ $siteName ?? trans('global.site_title') }}" style="width: 180px; height: auto; object-fit: contain;" />
        @else
        <b style="font-size: 28px;">{{ trans('global.site_title') }}</b>
        @endif

        <h5 style="margin-top: 10px;">{{ $tagLine }}</h5>
    </div>
    </div>
</section>
@endsection
@include('admin.common.addSteps.footer.footerJs')
@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function togglePassword() {
        var passwordField = document.getElementById('password');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
        } else {
            passwordField.type = 'password';
        }
    }

    $(document).ready(function() {
        $('.copy_cred').click(function(e) {
            e.preventDefault();
            $('#email').val('admin@sizhitsolutions.com');
            $('#password').val('Admin@1234');


            toastr.options.closeButton = true;
            toastr.options.progressBar = true;
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.success('Email and password copied!');

        });
    });
</script>
@if($general_captcha == 'yes')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
    $(function() {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#loginform').submit(function(event) {
            event.preventDefault();

            $('.email, .password, .captchamsg, .credentail').text('');
            $('.email, .password, .captchamsg').removeClass('error');


            var email = $('#email').val();
            var password = $('#password').val();

            if (!email) {
                $('.email').text('The email field is required');
                $('.email').addClass('error');
                return false;
            }
            if (!password) {
                $('.password').text('Please fill the password.');
                $('.password').addClass('error');
                return false;
            }
            @if($general_captcha == 'yes')
            var recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                $('.captchamsg').text('Please fill the reCAPTCHA.');
                $('.captchamsg').addClass('error');
                return false;
            }
            @endif
            $('#loader').show();

            if (email != '' && password != '') {
                $.ajax({
                    type: 'POST',
                    url: '/login',
                    data: $('#loginform').serialize(),
                    success: function(data) {
                        $('#loader').hide();
                        window.location.href = '/admin';
                    },
                    error: function(xhr, status, error) {

                        var response = xhr.responseJSON;

                        if (response && response.errors && response.errors.email) {

                            $('.credentail').text(response.errors.email[0]);
                            $('.credentail').addClass('error');
                            $('#loader').hide();
                        } else {

                            console.error(response);
                            $('#loader').hide();
                        }
                    }
                });
            }
        });
    });
</script>
<style>
    .login-page .right {
        background: linear-gradient(212.38deg,
            rgba(255, 56, 92, 0.7) 0%,
            rgba(252, 29, 69, 0.71) 100%),
        url({{$loginBackgroud}});
        background-size: cover;

        color: #fff;
        position: relative;
    }
</style>
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