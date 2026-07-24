@extends('vendor.layout')
@section('style')
<style>
        .border{
            width:100%;
            display:block;
            border-top:1px dotted black;

            
        }
    </style>
    @endsection
@section('content')
<section class="content">
<div class="row gap-2">
@include($leftSideMenu)


                <div class="col-md-9">
                    <form id="locationFormupdate">
                        @csrf
                        <input type="hidden" name="id" value="{{$id}}">
                        <div class="box box-info">
                            <div class="box-body">
                                <input type="hidden" name="latitude" id="latitude" value="">
                                <input type="hidden" name="longitude" id="longitude" value="">
                               
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">{{ trans('global.region')}} <span class="text-danger">*</span></label>
                                        <select name="place_id" data-saving="place_id" class="form-control f-14">
                                        @foreach($cityData as $cityDataLoop)
                                        <option value="{{$cityDataLoop->id}}" {{$itemData->place_id == $cityDataLoop->id ? 'selected' : ''}}> {{$cityDataLoop->city_name}}</option>
                                        @endforeach
                                        </select>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <p class="border mt-4"></p>
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">{{ trans('global.country')}} <span class="text-danger">*</span></label>
                                        <select id="select_country" name="country" class="form-control f-14">
                                        </select>
                                       
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">{{ trans('global.address_line_1')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="address_line_1" class="okokok form-control" id="address_line_1" value="{{ $itemData->address ?? '' }}" class="form-control f-14 pac-target-input" placeholder="House name/number + street/road" autocomplete="off">
                                        <span class="text-danger"></span>
                                        <span class="text-danger" id="locationerror-address_line_1"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <div id="map" style="height: 300px;"></div>
                                    </div>
                                    <div class="col-md-8 mb20">
                                        <p>You can move the pointer to set the correct map position</p>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                               {{--- <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">Address Line 2</label>
                                        <input type="text" name="address_line_2" id="address_line_2" value="" class="form-control f-14" placeholder="building access code">
                                    </div>
                                </div> ---}}
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">{{ trans('global.city_town_district')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="city"  value="{{ $itemData->city_name ?? '' }}"  class="form-control f-14" placeholder="Apt., suite, building access code">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">{{ trans('global.state_province_country')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="state" id="state" class="stateget form-control"  value="{{ $itemData->state_region ?? '' }}" class="form-control f-14">
                                        <span class="text-danger"></span>
                                        <span class="text-danger" id="locationerror-state"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb20">
                                        <label class="label-large fw-bold mt-4">{{ trans('global.zip_postal_code')}}</label>
                                        <input type="text" name="postal_code" id="zipCode"  value="{{ $itemData->zip_postal_code ?? '' }}" class="form-control f-14">
                                        <span class="text-danger"></span>
                                      
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                       <div class="col-6  col-lg-6  text-left">
                                            <a data-prevent-default="" href="{{route($backButtonRoute,[$id])}}" class="btn btn-large btn-primary f-14">{{ trans('global.back')}}</a>
                                        </div>
                                        <div class="col-6  col-lg-6 text-right">
                                            <button type="button" class="btn btn-large btn-primary next-section-button text-white">{{ trans('global.next')}}</button>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
const countries = [
      { code: 'AF', name: 'Afghanistan' },
      { code: "AL", name: 'Albania' },
      { code: "DZ", name: 'Algeria' },
      { code: "AS", name: 'American Samoa' },
      { code: "AD", name: 'Andorra' },
      { code: "AO", name: 'Angola' },
      { code: "AI", name: 'Anguilla' },
      { code: "AQ", name: 'Antarctica' },
      { code: "AG", name: 'Antigua and Barbuda' },
      { code: "AR", name: 'Argentina' },
      { code: "AM", name: 'Armenia' },
      { code: "AW", name: 'Aruba' },
      { code: "AU", name: 'Australia' },
      { code: "AT", name: 'Austria' },
      { code: "AZ", name: 'Azerbaijan' },
      { code: "BS", name: 'Bahamas' },
      { code: "BH", name: 'Bahrain' },
      { code: "BD", name: 'Bangladesh' },
      { code: "BB", name: 'Barbados' },
      { code: "BY", name: 'Belarus' },
      { code: "BE", name: 'Belgium' },
      { code: "BZ", name: 'Belize' },
      { code: "BJ", name: 'Benin' },
      { code: "BM", name: 'Bermuda' },
      { code: "BT", name: 'Bhutan' },
      { code: "BO", name: 'Bolivia' },
      { code: "BA", name: 'Bosnia and Herzegovina' },
      { code: "BW", name: 'Botswana' },
      { code: "BV", name: 'Bouvet Island' },
      { code: "BR", name: 'Brazil' },
      { code: "IO", name: 'British Indian Ocean Territory' },
      { code: "BN", name: 'Brunei Darussalam' },
      { code: "BG", name: 'Bulgaria' },
      { code: "BF", name: 'Burkina Faso' },
      { code: "BI", name: 'Burundi' },
      { code: "KH", name: 'Cambodia' },
      { code: "CM", name: 'Cameroon' },
      { code: "CA", name: 'Canada' },
      { code: "CV", name: 'Cape Verde' },
      { code: "KY", name: 'Cayman Islands' },
      { code: "CF", name: 'Central African Republic' },
      { code: "TD", name: 'Chad' },
      { code: "CL", name: 'Chile' },
      { code: "CN", name: 'China' },
      { code: "CX", name: 'Christmas Island' },
      { code: "CC", name: 'Cocos (Keeling) Islands' },
      { code: "CO", name: 'Colombia' },
      { code: "KM", name: 'Comoros' },
      { code: "CG", name: 'Congo' },
      { code: "CD", name: 'Congo, the Democratic Republic of the' },
      { code: "CK", name: 'Cook Islands' },
      { code: "CR", name: 'Costa Rica' },
      { code: "CI", name: 'Cote D Ivoire' },
      { code: "HR", name: 'Croatia' },
      { code: "CU", name: 'Cuba' },
      { code: "CY", name: 'Cyprus' },
      { code: "CZ", name: 'Czech Republic' },
      { code: "DK", name: 'Denmark' },
      { code: "DJ", name: 'Djibouti' },
      { code: "DM", name: 'Dominica' },
      { code: "DO", name: 'Dominican Republic' },
      { code: "EC", name: 'Ecuador' },
      { code: "EG", name: 'Egypt' },
      { code: "SV", name: 'El Salvador' },
      { code: "GQ", name: 'Equatorial Guinea' },
      { code: "ER", name: 'Eritrea' },
      { code: "EE", name: 'Estonia' },
      { code: "ET", name: 'Ethiopia' },
      { code: "FK", name: 'Falkland Islands (Malvinas)' },
      { code: "FO", name: 'Faroe Islands' },
      { code: "FJ", name: 'Fiji' },
      { code: "FI", name: 'Finland' },
      { code: "FR", name: 'France' },
      { code: "GF", name: 'French Guiana' },
      { code: "PF", name: 'French Polynesia' },
      { code: "TF", name: 'French Southern Territories' },
      { code: "GA", name: 'Gabon' },
      { code: "GM", name: 'Gambia' },
      { code: "GE", name: 'Georgia' },
      { code: "DE", name: 'Germany' },
      { code: "GH", name: 'Ghana' },
      { code: "GI", name: 'Gibraltar' },
      { code: "GR", name: 'Greece' },
      { code: "GL", name: 'Greenland' },
      { code: "GD", name: 'Grenada' },
      { code: "GP", name: 'Guadeloupe' },
      { code: "GU", name: 'Guam' },
      { code: "GT", name: 'Guatemala' },
      { code: "GN", name: 'Guinea' },
      { code: "GW", name: 'Guinea-Bissau' },
      { code: "GY", name: 'Guyana' },
      { code: "HT", name: 'Haiti' },
      { code: "HM", name: 'Heard Island and Mcdonald Islands' },
      { code: "VA", name: 'Holy See (Vatican City State)' },
      { code: "HN", name: 'Honduras' },
      { code: "HK", name: 'Hong Kong' },
      { code: "HU", name: 'Hungary' },
      { code: "IS", name: 'Iceland' },
      { code: "IN", name: 'India' },
      { code: "ID", name: 'Indonesia' },
      { code: "IR", name: 'Iran, Islamic Republic of' },
      { code: "IQ", name: 'Iraq' },
      { code: "IE", name: 'Ireland' },
      { code: "IL", name: 'Israel' },
      { code: "IT", name: 'Italy' },
      { code: "JM", name: 'Jamaica' },
      { code: "JP", name: 'Japan' },
      { code: "JO", name: 'Jordan' },
      { code: "KZ", name: 'Kazakhstan' },
      { code: "KE", name: 'Kenya' },
      { code: "KI", name: 'Kiribati' },
      { code: "KP", name: 'Korea, Democratic Peoples Republic of' },
      { code: "KR", name: 'Korea, Republic of' },
      { code: "KW", name: 'Kuwait' },
      { code: "KG", name: 'Kyrgyzstan' },
      { code: "LA", name: 'Lao Peoples Democratic Republic' },
      { code: "LV", name: 'Latvia' },
      { code: "LB", name: 'Lebanon' },
      { code: "LS", name: 'Lesotho' },
      { code: "LR", name: 'Liberia' },
      { code: "LY", name: 'Libyan Arab Jamahiriya' },
      { code: "LI", name: 'Liechtenstein' },
      { code: "LT", name: 'Lithuania' },
      { code: "LU", name: 'Luxembourg' },
      { code: "MO", name: 'Macao' },
      { code: "MK", name: 'Macedonia, the Former Yugoslav Republic of' },
      { code: "MG", name: 'Madagascar' },
      { code: "MW", name: 'Malawi' },
      { code: "MY", name: 'Malaysia' },
      { code: "MV", name: 'Maldives' },
      { code: "ML", name: 'Mali' },
      { code: "MT", name: 'Malta' },
      { code: "MH", name: 'Marshall Islands' },
      { code: "MQ", name: 'Martinique' },
      { code: "MR", name: 'Mauritania' },
      { code: "MU", name: 'Mauritius' },
      { code: "YT", name: 'Mayotte' },
      { code: "MX", name: 'Mexico' },
      { code: "FM", name: 'Micronesia, Federated States of' },
      { code: "MD", name: 'Moldova, Republic of' },
      { code: "MC", name: 'Monaco' },
      { code: "MN", name: 'Mongolia' },
      { code: "MS", name: 'Montserrat' },
      { code: "MA", name: 'Morocco' },
      { code: "MZ", name: 'Mozambique' },
      { code: "MM", name: 'Myanmar' },
      { code: "NA", name: 'Namibia' },
      { code: "NR", name: 'Nauru' },
      { code: "NP", name: 'Nepal' },
      { code: "NL", name: 'Netherlands' },
      { code: "AN", name: 'Netherlands Antilles' },
      { code: "NC", name: 'New Caledonia' },
      { code: "NZ", name: 'New Zealand' },
      { code: "NI", name: 'Nicaragua' },
      { code: "NE", name: 'Niger' },
      { code: "NG", name: 'Nigeria' },
      { code: "NU", name: 'Niue' },
      { code: "NF", name: 'Norfolk Island' },
      { code: "MP", name: 'Northern Mariana Islands' },
      { code: "NO", name: 'Norway' },
      { code: "OM", name: 'Oman' },
      { code: "PK", name: 'Pakistan' },
      { code: "PW", name: 'Palau' },
      { code: "PS", name: 'Palestinian Territory, Occupied' },
      { code: "PA", name: 'Panama' },
      { code: "PG", name: 'Papua New Guinea' },
      { code: "PY", name: 'Paraguay' },
      { code: "PE", name: 'Peru' },
      { code: "PH", name: 'Philippines' },
      { code: "PN", name: 'Pitcairn' },
      { code: "PL", name: 'Poland' },
      { code: "PT", name: 'Portugal' },
      { code: "PR", name: 'Puerto Rico' },
      { code: "QA", name: 'Qatar' },
      { code: "RE", name: 'Reunion' },
      { code: "RO", name: 'Romania' },
      { code: "RU", name: 'Russian Federation' },
      { code: "RW", name: 'Rwanda' },
      { code: "SH", name: 'Saint Helena' },
      { code: "KN", name: 'Saint Kitts and Nevis' },
      { code: "LC", name: 'Saint Lucia' },
      { code: "PM", name: 'Saint Pierre and Miquelon' },
      { code: "VC", name: 'Saint Vincent and the Grenadines' },
      { code: "WS", name: 'Samoa' },
      { code: "SM", name: 'San Marino' },
      { code: "ST", name: 'Sao Tome and Principe' },
      { code: "SA", name: 'Saudi Arabia' },
      { code: "SN", name: 'Senegal' },
      { code: "CS", name: 'Serbia and Montenegro' },
      { code: "SC", name: 'Seychelles' },
      { code: "SL", name: 'Sierra Leone' },
      { code: "SG", name: 'Singapore' },
      { code: "SK", name: 'Slovakia' },
      { code: "SI", name: 'Slovenia' },
      { code: "SB", name: 'Solomon Islands' },
      { code: "SO", name: 'Somalia' },
      { code: "ZA", name: 'South Africa' },
      { code: "GS", name: 'South Georgia and the South Sandwich Islands' },
      { code: "ES", name: 'Spain' },
      { code: "LK", name: 'Sri Lanka' },
      { code: "SD", name: 'Sudan' },
      { code: "SR", name: 'Suriname' },
      { code: "SJ", name: 'Svalbard and Jan Mayen' },
      { code: "SZ", name: 'Swaziland' },
      { code: "SE", name: 'Sweden' },
      { code: "CH", name: 'Switzerland' },
      { code: "SY", name: 'Syrian Arab Republic' },
      { code: "TW", name: 'Taiwan, Province of China' },
      { code: "TJ", name: 'Tajikistan' },
      { code: "TZ", name: 'Tanzania, United Republic of' },
      { code: "TH", name: 'Thailand' },
      { code: "TL", name: 'Timor-Leste' },
      { code: "TG", name: 'Togo' },
      { code: "TK", name: 'Tokelau' },
      { code: "TO", name: 'Tonga' },
      { code: "TT", name: 'Trinidad and Tobago' },
      { code: "TN", name: 'Tunisia' },
      { code: "TR", name: 'Turkey' },
      { code: "TM", name: 'Turkmenistan' },
      { code: "TC", name: 'Turks and Caicos Islands' },
      { code: "TV", name: 'Tuvalu' },
      { code: "UG", name: 'Uganda' },
      { code: "UA", name: 'Ukraine' },
      { code: "AE", name: 'United Arab Emirates' },
      { code: "GB", name: 'United Kingdom' },
      { code: "US",  name: 'United States' },
      { code: "UM", name: 'United States Minor Outlying Islands' },
      { code: "UY", name: 'Uruguay' },
      { code: "UZ", name: 'Uzbekistan' },
      { code: "VU", name: 'Vanuatu' },
      { code: "VE", name: 'Venezuela' },
      { code: "VN", name: 'Viet Nam' },
      { code: "VG", name: 'Virgin Islands, British' },
      { code: "VI", name: 'Virgin Islands, U.s.' },
      { code: "WF", name: 'Wallis and Futuna' },
      { code: "EH", name: 'Western Sahara' },
      { code: "YE", name: 'Yemen' },
      { code: "ZM", name: 'Zambia' },
      { code: "ZW", name: 'Zimbabwe' },
];

const selectElement = document.getElementById('select_country');
const addressInput = document.getElementById('address_line_1');
selectCountryByCode('{{$itemData->country}}');
addressInput.addEventListener('change', function () {
  // Get the country information based on the new address
  getCountryFromAddress();
});
function selectCountryByCode(countryCode) {
//   selectElement.value = countryCode;
countries.forEach(country => {
  const option = document.createElement('option');
  option.value = country.name;
  option.text = country.name;

  if (country.name === countryCode) {
    option.selected = true;
  }

  selectElement.appendChild(option);
});
}
function getCountryFromAddress() {
  const address = addressInput.value;
  const geocoder = new google.maps.Geocoder();

  geocoder.geocode({ 'address': address }, function (results, status) {
    if (status === 'OK' && results[0]) {
      const countryComponent = results[0].address_components.find(
        component => component.types.includes('country')
      );

      if (countryComponent) {
        const countryCode = countryComponent.short_name;
        selectCountryByCode(countryCode);
      } else {
        // Country not found in geocoding results
        // Handle this case accordingly
      }
    } else {
      // Geocoding was not successful
      // Handle this case accordingly
    }
  });
}
// Dynamically generate options for the country select dropdown
countries.forEach(country => {
  const option = document.createElement('option');
  option.value = country.name;
  option.text = country.name;
  selectElement.appendChild(option);
});
</script>
<!-- map -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $api_google_map_key->meta_value ?? '' }}&libraries=places&callback=initMap"></script>

<script>
function initMap() {
    var geocoder = new google.maps.Geocoder();
    var addressInput = document.getElementById('address_line_1');
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: 0, lng: 0 } // Default center, will be updated based on the address
    });
    var marker = new google.maps.Marker({
        map: map,
        draggable: true
    });

    // Create an Autocomplete object for the address input
    var autocomplete = new google.maps.places.Autocomplete(addressInput);

    // Set the bounds to restrict suggestions to the map area
    autocomplete.bindTo('bounds', map);

    // Update the map and marker when a suggestion is selected
    autocomplete.addListener('place_changed', function () {
        geocodeAddress(geocoder, map, marker);
    });

    // Trigger the initial update based on the existing address value
    geocodeAddress(geocoder, map, marker);

    // Add event listener for marker dragend event
    marker.addListener('dragend', function () {
        updateLocationFromMarker(geocoder, map, marker);
    });
}

function geocodeAddress(geocoder, map, marker) {
    var address = document.getElementById('address_line_1').value;

    geocoder.geocode({ 'address': address }, function (results, status) {
        if (status === 'OK' && results[0]) {
            map.setCenter(results[0].geometry.location);
            marker.setPosition(results[0].geometry.location);

            // Get and display location information
            displayLocationInfo(results[0]);
        } else {
            console.error('Geocode was not successful for the following reason: ' + status);
        }
    });
}

function updateLocationFromMarker(geocoder, map, marker) {
    var markerPosition = marker.getPosition();
    geocoder.geocode({ 'location': markerPosition }, function (results, status) {
        if (status === 'OK' && results[0]) {
            // Get and display location information
            displayLocationInfo(results[0]);
        } else {
            console.error('Reverse geocode was not successful for the following reason: ' + status);
        }
    });
}

function displayLocationInfo(result) {
    var city = "";
    var state = "";
    var country = "";
    var zipCode = "";
    var latitude = result.geometry.location.lat();
    var longitude = result.geometry.location.lng();

    // Extract address components
    for (var i = 0; i < result.address_components.length; i++) {
        var component = result.address_components[i];
        if (component.types.includes('locality')) {
            city = component.long_name;
        } else if (component.types.includes('administrative_area_level_1')) {
            state = component.long_name;
        } else if (component.types.includes('country')) {
            country = component.long_name;
        } else if (component.types.includes('postal_code')) {
            zipCode = component.long_name;
        }
    }

    // Update form fields
    $('.stateget').val(state);
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
    $('#city').val(city);
    $('#zipCode').val(zipCode);
   

    // Log location information
    console.log('City:', city);
    console.log('Country:', country);
    console.log('Latitude:', latitude);
    console.log('Longitude:', longitude);
    console.log('State:', state);
    console.log('Zip Code:', zipCode);
    var formattedAddress = result.formatted_address;
    console.log('address:',formattedAddress);
    $('.okokok').val(formattedAddress);
}


</script>

<script>
$(document).ready(function() {
    $('.text-white').click(function() {
        var id = {{$id}};
        $.ajax({
            type: 'POST',
            url: '{{ route($updateLocationRoute) }}',
            data: $('#locationFormupdate').serialize(),
            success: function(data) {
                $('.error-message').text(''); 
                window.location.href = '{{$nextButton}}' + id;
            },
            error: function(response) {
                if (response.responseJSON && response.responseJSON.errors) {
                    var errors = response.responseJSON.errors;
                    $('.error-message').text('');

                    // Then display new error messages
                    for (var field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            var errorMessage = errors[field][
                                0
                            ]; // get the first error message
                            $('#locationerror-' + field).text(errorMessage);
                        }
                    }
                }
            }
        });
    });


});
</script>

@endsection