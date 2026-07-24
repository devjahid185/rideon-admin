@extends('vendor.layout')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('global.booking_title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("vendor.bookings.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('itemid') ? 'has-error' : '' }}">
                            <label class="required" for="itemid">{{ trans('global.itemid') }}</label>
                            <input class="form-control" type="text" name="itemid" id="itemid" value="{{ old('itemid', '') }}" required>
                            @if($errors->has('itemid'))
                                <span class="help-block" role="alert">{{ $errors->first('itemid') }}</span>
                            @endif
                         
                        </div>
                        <div class="form-group {{ $errors->has('host') ? 'has-error' : '' }}">
                            <label for="host_id">{{ trans('global.host') }}</label>
                            <select class="form-control select2" name="host_id" id="customer" required>
                                        <option value=""></option>
                                        <!-- Add any other options you want to display -->
                                    </select>
                            @if($errors->has('host'))
                                <span class="help-block" role="alert">{{ $errors->first('host') }}</span>
                            @endif
                        
                        </div>
                        <div class="form-group {{ $errors->has('userid') ? 'has-error' : '' }}">
                            <label class="required" for="userid">{{ trans('global.vendor') }}</label>
                            <select class="form-control select2" name="userid" id="customerproperty" required>
                                        <option value=""></option>
                                        <!-- Add any other options you want to display -->
                                    </select>
                            @if($errors->has('userid'))
                                <span class="help-block" role="alert">{{ $errors->first('userid') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('check_in') ? 'has-error' : '' }}">
                            <label class="required" for="check_in">{{ trans('global.check_in') }}</label>
                            <input class="form-control date" type="date" name="check_in" id="check_in" value="{{ old('check_in') }}" required>
                            @if($errors->has('check_in'))
                                <span class="help-block" role="alert">{{ $errors->first('check_in') }}</span>
                            @endif
                    
                        </div>
                        <div class="form-group {{ $errors->has('check_out') ? 'has-error' : '' }}">
                            <label for="check_out">{{ trans('global.check_out') }}</label>
                            <input class="form-control date" type="date" name="check_out" id="check_out" value="{{ old('check_out') }}" required>
                            @if($errors->has('check_out'))
                                <span class="help-block" role="alert">{{ $errors->first('check_out') }}</span>
                            @endif
                        
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required" for="status">{{ trans('global.status') }}</label>
                            <input class="form-control" type="text" name="status" id="status" value="{{ old('status', '') }}" required>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                        
                        </div>
                        <div class="form-group {{ $errors->has('total_night') ? 'has-error' : '' }}">
                            <label class="required" for="total_night">{{ trans('global.total_night') }}</label>
                            <input class="form-control" type="number" name="total_night" id="total_night" value="{{ old('total_night', '') }}" step="1" required>
                            @if($errors->has('total_night'))
                                <span class="help-block" role="alert">{{ $errors->first('total_night') }}</span>
                            @endif
                         
                        </div>
                        <div class="form-group {{ $errors->has('per_night') ? 'has-error' : '' }}">
                            <label class="required" for="per_night">{{ trans('global.per_night') }}</label>
                            <input class="form-control" type="number" name="per_night" id="per_night" value="{{ old('per_night', '') }}" step="0.01" required>
                            @if($errors->has('per_night'))
                                <span class="help-block" role="alert">{{ $errors->first('per_night') }}</span>
                            @endif
                       
                        </div>
                        <div class="form-group {{ $errors->has('book_for') ? 'has-error' : '' }}">
                            <label>{{ trans('global.book_for') }}</label>
                            <select class="form-control" name="book_for" id="book_for" required>
                                <option value disabled {{ old('book_for', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Booking::BOOK_FOR_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('book_for', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('book_for'))
                                <span class="help-block" role="alert">{{ $errors->first('book_for') }}</span>
                            @endif
                        
                        </div>
                        <div class="form-group {{ $errors->has('base_price') ? 'has-error' : '' }}">
                            <label class="required" for="base_price">{{ trans('global.base_price') }}</label>
                            <input class="form-control" type="number" name="base_price" id="base_price" value="{{ old('base_price', '') }}" step="0.01" required>
                            @if($errors->has('base_price'))
                                <span class="help-block" role="alert">{{ $errors->first('base_price') }}</span>
                            @endif
                       
                        </div>
                        <div class="form-group {{ $errors->has('cleaning_charge') ? 'has-error' : '' }}">
                            <label for="cleaning_charge">{{ trans('global.cleaning_charge') }}</label>
                            <input class="form-control" type="number" name="cleaning_charge" id="cleaning_charge" value="{{ old('cleaning_charge', '') }}" step="0.01" required>
                            @if($errors->has('cleaning_charge'))
                                <span class="help-block" role="alert">{{ $errors->first('cleaning_charge') }}</span>
                            @endif
                  
                        </div>
                        <div class="form-group {{ $errors->has('guest_charge') ? 'has-error' : '' }}">
                            <label for="guest_charge">{{ trans('global.guest_charge') }}</label>
                            <input class="form-control" type="number" name="guest_charge" id="guest_charge" value="{{ old('guest_charge', '') }}" step="0.01" required>
                            @if($errors->has('guest_charge'))
                                <span class="help-block" role="alert">{{ $errors->first('guest_charge') }}</span>
                            @endif
                       
                        </div>
                        <div class="form-group {{ $errors->has('service_charge') ? 'has-error' : '' }}">
                            <label for="service_charge">{{ trans('global.service_charge') }}</label>
                            <input class="form-control" type="number" name="service_charge" id="service_charge" value="{{ old('service_charge', '') }}" step="0.01" required>
                            @if($errors->has('service_charge'))
                                <span class="help-block" role="alert">{{ $errors->first('service_charge') }}</span>
                            @endif
             
                        </div>
                        <div class="form-group {{ $errors->has('security_money') ? 'has-error' : '' }}">
                            <label for="security_money">{{ trans('global.security_money') }}</label>
                            <input class="form-control" type="number" name="security_money" id="security_money" value="{{ old('security_money', '') }}" step="0.01" required>
                            @if($errors->has('security_money'))
                                <span class="help-block" role="alert">{{ $errors->first('security_money') }}</span>
                            @endif
           
                        </div>
                        <div class="form-group {{ $errors->has('iva_tax') ? 'has-error' : '' }}">
                            <label for="iva_tax">{{ trans('global.iva_tax') }}</label>
                            <input class="form-control" type="number" name="iva_tax" id="iva_tax" value="{{ old('iva_tax', '') }}" step="0.01" required>
                            @if($errors->has('iva_tax'))
                                <span class="help-block" role="alert">{{ $errors->first('iva_tax') }}</span>
                            @endif

                        </div>
                        <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
                            <label for="total">{{ trans('global.total') }}</label>
                            <input class="form-control" type="number" name="total" id="total" value="{{ old('total', '') }}" step="0.01" required>
                            @if($errors->has('total'))
                                <span class="help-block" role="alert">{{ $errors->first('total') }}</span>
                            @endif
             
                        </div>
                        <div class="form-group {{ $errors->has('currency_code') ? 'has-error' : '' }}">
                            <label for="currency_code">{{ trans('global.currency_code') }}</label>
                            <input class="form-control" type="text" name="currency_code" id="currency_code" value="{{ old('currency_code', '') }}" required>
                            @if($errors->has('currency_code'))
                                <span class="help-block" role="alert">{{ $errors->first('currency_code') }}</span>
                            @endif
                       
                        </div>
                        <div class="form-group {{ $errors->has('cancellation_reasion') ? 'has-error' : '' }}">
                            <label for="cancellation_reasion">{{ trans('global.cancellation_reasion') }}</label>
                            <input class="form-control" type="text" name="cancellation_reasion" id="cancellation_reasion" value="{{ old('cancellation_reasion', '') }}" required>
                            @if($errors->has('cancellation_reasion'))
                                <span class="help-block" role="alert">{{ $errors->first('cancellation_reasion') }}</span>
                            @endif
                       
                        </div>
                        <div class="form-group {{ $errors->has('transaction') ? 'has-error' : '' }}">
                            <label for="transaction">{{ trans('global.transaction') }}</label>
                            <input class="form-control" type="text" name="transaction" id="transaction" value="{{ old('transaction', '') }}" required>
                            @if($errors->has('transaction'))
                                <span class="help-block" role="alert">{{ $errors->first('transaction') }}</span>
                            @endif
                  
                        </div>
                        <div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}">
                            <label for="payment_method">{{ trans('global.payment_method') }}</label>
                            <input class="form-control" type="text" name="payment_method" id="payment_method" value="{{ old('payment_method', '') }}" required>
                            @if($errors->has('payment_method'))
                                <span class="help-block" role="alert">{{ $errors->first('payment_method') }}</span>
                            @endif
                    
                        </div>
                        <div class="form-group {{ $errors->has('prop_img') ? 'has-error' : '' }}">
                            <label for="prop_img">{{ trans('global.prop_img') }}</label>
                            <input class="form-control" type="text" name="prop_img" id="prop_img" value="{{ old('prop_img', '') }}" required>
                            @if($errors->has('prop_img'))
                                <span class="help-block" role="alert">{{ $errors->first('prop_img') }}</span>
                            @endif
                   
                        </div>
                        <div class="form-group {{ $errors->has('prop_title') ? 'has-error' : '' }}">
                            <label for="prop_title">{{ trans('global.prop_title') }}</label>
                            <input class="form-control" type="text" name="prop_title" id="prop_title" value="{{ old('prop_title', '') }}" required>
                            @if($errors->has('prop_title'))
                                <span class="help-block" role="alert">{{ $errors->first('prop_title') }}</span>
                            @endif
                    
                        </div>
                        <div class="form-group {{ $errors->has('wall_amt') ? 'has-error' : '' }}">
                            <label for="wall_amt">{{ trans('global.wall_amt') }}</label>
                            <input class="form-control" type="number" name="wall_amt" id="wall_amt" value="{{ old('wall_amt', '') }}" step="0.01" required>
                            @if($errors->has('wall_amt'))
                                <span class="help-block" role="alert">{{ $errors->first('wall_amt') }}</span>
                            @endif
                     
                        </div>
                        <div class="form-group {{ $errors->has('note') ? 'has-error' : '' }}">
                            <label for="note">{{ trans('global.note') }}</label>
                            <textarea class="form-control" name="note" id="note" required>{{ old('note') }}</textarea>
                            @if($errors->has('note'))
                                <span class="help-block" role="alert">{{ $errors->first('note') }}</span>
                            @endif
                     
                        </div>
                        <div class="form-group {{ $errors->has('rating') ? 'has-error' : '' }}">
                            <label for="rating">{{ trans('global.rating') }}</label>
                            <input class="form-control" type="number" name="rating" id="rating" value="{{ old('rating', '') }}" step="0.01" required>
                            @if($errors->has('rating'))
                                <span class="help-block" role="alert">{{ $errors->first('rating') }}</span>
                            @endif
                 
                        </div>
                        <div class="form-group {{ $errors->has('cancelled_by') ? 'has-error' : '' }}">
                            <label>{{ trans('global.cancelled_by') }}</label>
                            <select class="form-control" name="cancelled_by" id="cancelled_by" required>
                                <option value disabled {{ old('cancelled_by', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Booking::CANCELLED_BY_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('cancelled_by', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('cancelled_by'))
                                <span class="help-block" role="alert">{{ $errors->first('cancelled_by') }}</span>
                            @endif
                     
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
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Include Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script>
    $(document).ready(function() {
        // Initialize the Select2 for the customer select box
        $(document).ready(function() {
    // Initialize the Select2 for the customer select box
    $('#customer').select2({
        ajax: {
            url: "{{ route('vendor.searchcustomer') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                // Transform the response data into Select2 format
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.first_name,
                        };
                    })
                };
            },
            cache: true, // Cache the AJAX results to avoid multiple requests for the same data
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error while fetching customer data:", textStatus, errorThrown);
                // Optionally display an error message to the user
                alert("An error occurred while loading customer data. Please try again later.");
            }
        }
    });

    // Preselect the customer option if search term is provided
    // const selectedCustomerId = "{{ request()->input('customer') }}";
    // if (selectedCustomerId) {
    //     const customerSelect = $('#customer');
    //     const option = new Option(selectedCustomerId, selectedCustomerId, true, true);
    //     customerSelect.append(option).trigger('change');
    // }

    // Your other code for DateRangePicker initialization and filters
});
});
</script>
<script>
$(document).ready(function() {
    // Initialize the Select2 for the customer select box
    $('#customerproperty').select2({
        ajax: {
            url: "{{ route('vendor.customerproperty') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                // Transform the response data into Select2 format
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.customer_name, // Update this line
                        };
                    })
                };
            },
            cache: true, // Cache the AJAX results to avoid multiple requests for the same data
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error while fetching customer data:", textStatus, errorThrown);
                // Optionally display an error message to the user
                alert("An error occurred while loading customer data. Please try again later.");
            }
        }
    });
    
});
</script>

@endsection