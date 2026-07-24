@extends('layouts.app')
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
#hostRequestForm input {
    margin: 0;  /* Removes margin from inputs in the register form */
}
    </style>
@endsection
<section class="login">
		<div class="login_box">
			<div class="left">
				
				<div class="contact">
					<div class="login-box">
    <div class="login-logo">
    <a href="{{ route('vendor.login') }}">
        {{ trans('global.host') }} {{ trans('global.request') }} to  {{ trans('global.site_title') }}
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

        <form id="hostRequestForm" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- First Name and Last Name (Same Row) -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                <input id="first_name" type="text" name="first_name" class="form-control" required 
                       placeholder="{{ trans('global.first_name') }}" 
                       value="{{ old('first_name', $user->first_name) }}">
                @if($errors->has('first_name'))
                    <p class="help-block">
                        {{ $errors->first('first_name') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                <input id="last_name" type="text" name="last_name" class="form-control" required 
                       placeholder="{{ trans('global.last_name') }}" 
                       value="{{ old('last_name', $user->last_name) }}">
                @if($errors->has('last_name'))
                    <p class="help-block">
                        {{ $errors->first('last_name') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Email (New Row) -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                <input id="email" type="email" name="email" class="form-control" required 
                       autocomplete="email" placeholder="{{ trans('global.email') }}" 
                       value="{{ old('email', $user->email) }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if($errors->has('email'))
                    <p class="help-block">
                        {{ $errors->first('email') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Phone Country and Phone (Same Row) -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('phone_country') ? ' has-error' : '' }}">
                <div class="input-group">
                    <select class="form-control" name="phone_country" id="phone_country" onchange="updateDefaultCountry()" required>
                        @foreach (config('countries') as $country)
                            <option value="{{ $country['dial_code'] }}" 
                                    @if(old('phone_country', $user->phone_country) == $country['dial_code']) selected @endif>
                                {{ $country['name'] }} ({{ $country['dial_code'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($errors->has('phone_country'))
                    <span class="help-block" role="alert">{{ $errors->first('phone_country') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                <input class="form-control" type="text" name="phone" id="phone" 
                       value="{{ old('phone', $user->phone) }}" required 
                       placeholder="{{ trans('global.phone') }}">
                @if($errors->has('phone'))
                    <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Company Name and Nationality/Residency (Same Row) -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                <input class="form-control" type="text" name="company_name" id="company_name" 
                       value="{{ old('company_name') }}" 
                       placeholder="Company Name (optional)">
                @if($errors->has('company_name'))
                    <p class="help-block">{{ $errors->first('company_name') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('nationality_residency') ? ' has-error' : '' }}">
            <select class="form-control" name="nationality_residency" id="nationality_residency" required>
                    <option value="" disabled selected>Nationality/Residency</option>
                    <option value="Citizenship" @if(old('nationality_residency') == 'Citizenship') selected @endif>Citizenship</option>
                    <option value="Residence" @if(old('nationality_residency') == 'Residence') selected @endif>Residence</option>
                </select>
                @if($errors->has('nationality_residency'))
                    <p class="help-block">{{ $errors->first('nationality_residency') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Address (New Row) -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                <input class="form-control" type="text" name="address" id="address" 
                       value="{{ old('address') }}" placeholder="Address" required>
                @if($errors->has('address'))
                    <p class="help-block">{{ $errors->first('address') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Identity Type and Image Upload (Same Row) -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('identity_type') ? ' has-error' : '' }}">
            <select class="form-control" name="identity_type" id="identity_type" required>
                <option value="" disabled selected>Identity Type</option>
                <option value="Passport" @if(old('identity_type') == 'Passport') selected @endif>Passport</option>
                <option value="Driver License" @if(old('identity_type') == 'Driver License') selected @endif>Driver License</option>
            </select>
                @if($errors->has('identity_type'))
                    <p class="help-block">{{ $errors->first('identity_type') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('identity_photo') ? ' has-error' : '' }}">
                <input type="file" class="form-control" name="identity_photo" id="identity_photo">
                @if($errors->has('identity_photo'))
                    <p class="help-block">{{ $errors->first('identity_photo') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-xs-4">
        <button type="button" id="hostRequestButton" class="btn btn-primary btn-block btn-flat">
                {{ trans('global.send') }}
            </button>
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

document.getElementById("hostRequestButton").addEventListener("click", function() {
    //let formData = new FormData(document.getElementById("hostRequestForm"));
    let form = document.getElementById("hostRequestForm");

        // Validate the form using built-in validation
        if (!form.reportValidity()) {
            // If the form is invalid, stop here
            return;
        }

        let formData = new FormData(form);

    fetch("{{ route('vendor.putHostRequest') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 200) {
        Swal.fire({
            title: "Success!",
            text: "Your host request has been updated successfully! Wait for admin approval.",
            icon: "success",
            confirmButtonText: "OK"
        }).then(() => {
            // Redirect to the login page
            window.location.href = '/vendor/login'; // Replace '/login' with your actual login page URL
        });
    } else {
            Swal.fire("Error!", data.message || "There was an issue with your request. Please try again.", "error");
        }
    })
    .catch(error => {
        Swal.fire("Error!", "An error occurred while processing your request. Please try again later.", "error");
    });
});




</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $api_google_map_key->meta_value ?? '' }}&libraries=places&callback=initAutocomplete"></script>

<script>
function initAutocomplete() {
    var addressInput = document.getElementById('address');
    var autocomplete = new google.maps.places.Autocomplete(addressInput);
    autocomplete.setBounds(new google.maps.LatLngBounds(
        new google.maps.LatLng(37.7749, -122.4194),
        new google.maps.LatLng(37.8044, -122.2711)
    ));

    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        if (place && place.formatted_address) {
            console.log("Selected address: " + place.formatted_address);
        }
    });
}
</script>
@endsection