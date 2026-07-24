@extends('layouts.admin')
@section('content')
<section class="content">
    <div class="row gap-2">
        @include($leftSideMenu)


        <div class="col-md-9">
            <form id="cancellationandrule">
                @csrf
                <input type="hidden" name="id" value="{{$id}}">
                <div class="box box-info">
                    <div class="box-body">


                        <div class="row">
                            <div class="col-md-8 mb20">
                                <label class="label-large fw-bold mt-4">{{trans('global.cancellationPolicies_title')}}
                                    <span class="text-danger">*</span></label>
                                {{--<select name="policy" data-saving="basics1" class="form-control f-14">
                                    <option value="">Please Select </option>
                                    @foreach($cancellationPolicyData as $cancellationPolicyData1)
                                    <option value="{{$cancellationPolicyData1->id}}"
                                        {{ ($policy && $cancellationPolicyData1->id == $policy->booking_policies_id) ? 'selected' : '' }}>
                                        {{$cancellationPolicyData1->name}}</option>
                                    @endforeach
                                </select>--}}

                                    <div class="form-group">
                                  
                                    <ul class="list-group">
                                        @foreach($cancellationPolicyData as $policy)
                                            <li class="list-group-item">
                                                <!-- <strong>{{ $policy->name }}</strong>:  -->
                                                {{ $policy->description }} 
                                               
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <span class="text-danger"></span>
                                <span class="text-danger" id="policieserror-policy"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb20">
                                <label class="label-large fw-bold mt-4">{{ trans('global.rules')}} <span
                                        class="text-danger">*</span></label>
                                <div>
                                    @foreach($itemRules as $itemRule)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="rules[]"
                                            value="{{ $itemRule->id }}"
                                            {{ in_array($itemRule->id, explode(',', $rules->meta_value ?? '')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rule_{{ $itemRule->id }}"
                                            style="font-weight: normal;">
                                            {{ $itemRule->rule_name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <span class="text-danger"></span>
                                <span class="text-danger" id="policieserror-rules"></span>
                            </div>
                        </div>



                        <br>
                        <div class="row mt-6">
                            <div class="col-6  col-lg-6  text-left">
                                <a data-prevent-default="" href="{{route($backButtonRoute,[$id])}}"
                                    class="btn btn-large btn-primary f-14">{{ trans('global.back') }}</a>
                            </div>
                            <div class="col-6  col-lg-6 text-right">

                                @if($serviceType == 'sale' || ($serviceType == 'rent'))
                                <button type="button" data-btn="finish" data-status='finish'
                                    class="btn btn-large btn-primary next-section-button next">
                                    {{ trans('global.finish')}}</button>
                                @else
                                <button type="button" class="btn btn-large btn-primary next-section-button next">
                                    {{ trans('global.next')}}
                                </button>
                                @endif
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
$(document).ready(function() {
    $('.next').click(function() {
    
        var id = {{$id}};
        
        var clickBtn = $(this).attr("data-btn");
        
        $.ajax({
            type: 'POST',
            url: '{{ route($updatePolicyRoute) }}',
            data: $('#cancellationandrule').serialize(),
            success: function(data) {
                $('.error-message').text('');
              
                if(clickBtn!='finish')
                window.location.href = '{{$nextButton}}' + id;
                else
                window.location.href="{{ route('admin.vehicles.index') }}"
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
                            $('#policieserror-' + field).text(errorMessage);
                        }
                    }
                }
            }
        });
    });


});
</script>

@endsection