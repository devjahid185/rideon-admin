
@extends('layouts.admin')
@section('content')
<section class="content">
<div class="row gap-2">
        <style>
          .input-addon-new{
            display: block!important;
          }
        </style>
          @include('admin.vehicles.addVehicle.vehicle_left_menu')
            <div class="col-md-9">
                <form id="priceAddForm">
                  @csrf
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="f-18">{{trans('global.set_pricing')}}</h3>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 form-group">
                                    <label for="listing_price_native" class="label-large fw-bold">  {{trans('global.price')}} /{{trans('global.day')}} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-prefix pay-currency">{{$general_default_currency->meta_value}}</span>
                                        <input type="text" data-suggested="" id="price-night" value="{{$night_price ?? '0' }}" name="night_price" class="money-input form-control f-14">
                                   
                                    <select name="service_type" id="service_type" class="form-control"  style="margin-left: 7px;font-size: small;">
                                        <option value="booking" {{ old('service_type', $serviceType) == 'booking' ? 'selected' : '' }}>Booking</option>
                                        <option value="sale" {{ old('service_type', $serviceType) == 'sale' ? 'selected' : '' }}>Sale</option>
                                        <option value="rent" {{ old('service_type', $serviceType) == 'rent' ? 'selected' : '' }}>Rent</option>
                                      </select>
                                      </div>
                                    <span class="text-danger" id="priceserror-night_price"></span>
                                </div>


                            </div>

                            <div class="row display-off" id="long-term-div">
                                <div class="col-md-12">
                                </div>
                                <div class="col-md-8">
      
      
        <label for="listing_price_native" class="label-large fw-bold mb-1">{{ trans('global.weekly_discount_percent') }}</label>
        <span>
            ( <input type="radio" id="percentage-week" name="weekly_discount_type" value="percent" {{ $weeklyDiscountType == 'percent' ? 'checked' : '' }}>
            <label for="percentage-week">{{trans('global.percent')}}</label>
            <input type="radio" id="fixed-week" name="weekly_discount_type" value="fixed" {{ $weeklyDiscountType == 'fixed' ? 'checked' : '' }}>
            <label for="fixed-week">{{trans('global.fixed')}}</label> )
        </span>
        <div class="input-addon input-addon-new">
            <span class="input-prefix pay-currency">{{$general_default_currency->meta_value}}</span>
            <input type="text" id="price-week" name="weekly_discount" value="{{$weeklyDiscount ?? '' }}" class="money-input form-control f-14">
          

        </div>

    </div>
    <div class="col-md-8">
        <label for="listing_price_native" class="label-large fw-bold mb-1">{{ trans('global.monthly_discount_percent') }}</label>
        <span>
            ( <input type="radio" id="percentage-month" name="monthly_discount_type" value="percent" {{ $monthlyDiscountType == 'percent' ? 'checked' : '' }}>
            <label for="percentage-month">{{trans('global.percent')}}</label>
            <input type="radio" id="fixed-month" name="monthly_discount_type" value="fixed" {{ $monthlyDiscountType == 'fixed' ? 'checked' : '' }}>
            <label for="fixed-month">{{trans('global.fixed')}}</label> )
        </span>
        <div class="input-addon input-addon-new">
            <span class="input-prefix pay-currency">{{$general_default_currency->meta_value}}</span>
            <input type="text" id="price-month" name="monthly_discount" value="{{$monthlyDiscount ?? '' }}" class="money-input form-control f-14">
        </div>
    </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                              <!-- <h3 class="mb-0 f-18">{{trans('global.base_price')}}</h3> -->
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12" style="display:none">
                                <label for="listing_cleaning_fee_native_checkbox" class="label-large label-inline fw-bold">
                                  <input type="checkbox" data-extras="true" id="show"	class="pricing_checkbox" data-rel="cleaning">&nbsp;
                                  {{trans('global.base_price')}}
                                </label>
                              </div>
                              <div id="cleaning" class="display-off">
                                <div class="col-md-12">
                                  <div class="col-md-4 l-pad-none">
                                    <div class="input-addon" id="box">
                                      <span class="input-prefix pay-currency">{{$general_default_currency->meta_value}}</span>
                                      <input type="text" id="price-cleaning"  name="base_price" class="money-input"  value="{{$basePrice->meta_value ?? '' }}">
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <!-- </div> -->
                              <div class="col-md-12">
                                <label for="DoorStepDelivery" class="label-large label-inline fw-bold">
                                  <input type="checkbox" id="DoorStepDelivery" name="show" {{ !empty($doorStep->meta_value) ? 'checked' : '' }} class="security_checkbox">
                                  &nbsp;
                                  {{trans('global.doorstep_delivery_price')}}
                                </label>
                              </div>
                              <div id="doorstep" class="display-off">
                              <div class="col-md-12">
                                  <div class="col-md-4 l-pad-none">

                                  @if(!empty($doorStep->meta_value))
                                    <div class="input-addon" id="doorstepbox" style="display: block;">
                                @else
                                <div class="input-addon" id="doorstepbox">
                                @endif

                                   
                                      <span class="input-prefix pay-currency">{{$general_default_currency->meta_value}}</span>
                                      <input type="text" class="money-input" name="doorstep_delivery_price" value="{{$doorStep->meta_value ?? '' }}">
                                    </div>
                                  </div>
                                </div>
                              </div> 
                              
                              
                              </div>
<div class="row">
                               <div class="col-md-12 ">
                                <label for="SecurityDeposit" class="label-large label-inline fw-bold">
                                  <input type="checkbox" id="SecurityDeposit" name="show" {{ !empty($securityFee->meta_value) ? 'checked' : '' }}  class="security_checkbox">
                                  &nbsp;
                                  {{trans('global.security_deposit')}}
                                </label>
                              </div>
                              <div id="security" class="display-off">
                                <div class="col-md-12">
                                  <div class="col-md-4 l-pad-none">

                                  @if(!empty($securityFee->meta_value))
                                    <div class="input-addon" id="securitybox" style="display: block;">
                                @else
                                <div class="input-addon" id="securitybox">
                                @endif

                                   
                                      <span class="input-prefix pay-currency">{{$general_default_currency->meta_value}}</span>
                                      <input type="text" class="money-input" name="security_fee" value="{{$securityFee->meta_value ?? '' }}">
                                    </div>
                                  </div>
                                </div>
                              </div> 
                              </div>
                            <div class="row ">
                                        <div class="mt-4 col-lg-12 col-md-12"><div class="col-6  col-lg-6  text-left">
                                            <a data-prevent-default="" href="{{route('admin.vehicles.photos',[$id])}}" class="btn btn-large btn-primary f-14">{{trans('global.back')}}</a>
                                        </div>
                                        <div class="col-6  col-lg-6 text-right">
                                            <button type="button" class="btn btn-large btn-primary next-section-button nextStep ">{{trans('global.next')}}</button>
                                        </div></div>
                                    </div>
                          </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endsection
    @section('scripts')
    <script>
$(document).ready(function() {
    $('.nextStep').click(function() {
        var id = {{$id}};
        $.ajax({
            type: 'POST',
            url: '{{ route('admin.vehicles.prices-Update') }}',
            data: $('#priceAddForm').serialize(),
            success: function(data) {
                $('.error-message').text(''); 
                   window.location.href = '/admin/vehicles/cancellation-policies/' + id;
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
                            $('#priceserror-' + field).text(errorMessage);
                        }
                    }
                }
            }
        });
    });


});

</script>

      <script>

      const checkbox = document.getElementById('show');

      const box = document.getElementById('box');

      checkbox.addEventListener('click', function handleClick() {
        if (checkbox.checked) {
          box.style.display = 'block';
        } else {
          box.style.display = 'none';
        }
      });

      $(document).ready(function () {
          $('#DoorStepDelivery').click(function () {
              if (this.checked) {
                $('#doorstepbox').show();
              } else {
                $('#doorstepbox').hide();
              }
          });
      });
      $(document).ready(function() {
        $('#SecurityDeposit').click(function() {
          
          if (this.checked) {
            $('#securitybox').show(); 
          } else {
            $('#securitybox').hide(); 
          }
        });
      });
$(document).ready(function () {
    $('#weekendPrice').click(function () {
      console.log('iotuy');
        if (this.checked) {
          $('#weekendbox').show();
        } else {
          $('#weekendbox').hide();
        }
    });
});

</script>


@endsection