@extends('layouts.admin')
@section('content')
<section class="content">
	<div class="row gap-2">

		@include($leftSideMenu)

		<div class="col-md-9">
			<form id="featuresFormupdate">
				@csrf
				<input type="hidden" name="id" value="{{$id}}">
				<div class="box box-info">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12  mb-15">
								<p class="fs-18">
									@if(Str::contains(Request::url(), '/bookable/'))
										{{ trans('global.feature_title') }}
									@else
										{{ trans('global.feature_title') }}
									@endif

									<span class="text-danger">*</span>
								</p>
								@foreach($features as $feature)
									<input type="checkbox" name="features[]" value="{{ $feature['id'] }}" {{ in_array($feature['id'], $features_ids) ? 'checked' : '' }}>
									{{ $feature['name'] }}<br>
								@endforeach
								<span class="text-danger" id="featureserror-features"></span>
							</div>


							@if(Request::is('admin/bookable/features/*'))
								<div class="col-md-12 mb-15">
									<p class="fs-18">
										Fit
										<span class="text-danger">*</span>
									</p>
									@foreach($fits as $fit)
										<input type="checkbox" name="fits[]" value="{{ $fit['id'] }}" {{ in_array($fit['id'], $fits_ids) ? 'checked' : '' }}> {{ $fit['name'] }}<br>
									@endforeach
									<span class="text-danger" id="fitserror-fits"></span>
								</div>

								<div class="col-md-12 mb-15">
									<p class="fs-18">
										Size
										<span class="text-danger">*</span>
									</p>
									@foreach($sizes as $size)
										<input type="checkbox" name="sizes[]" value="{{ $size['id'] }}" {{ in_array($size['id'], $sizes_ids) ? 'checked' : '' }}> {{ $size['name'] }}<br>
									@endforeach
									<span class="text-danger" id="sizeserror-sizes"></span>
								</div>

								<div class="col-md-12 mb-15">
									<p class="fs-18">
										Color
										<span class="text-danger">*</span>
									</p>

									@foreach($colors as $color)
										<input type="checkbox" name="colors[]" value="{{ $color['id'] }}" {{ in_array($color['id'], $colors_ids) ? 'checked' : '' }}> {{ $color['name'] }}<br>
									@endforeach

									<span class="text-danger" id="colorserror-colors"></span>
								</div>
							@endif
						</div>
						<div class="row">

							<div class="col-6  col-lg-6  text-left">
								<a data-prevent-default="" href="{{route($backButtonRoute, [$id])}}"
									class="btn btn-large btn-primary f-14">{{ trans('global.back')}}</a>
							</div>
							<div class="col-6  col-lg-6 text-right">
								<button type="button"
									class="btn btn-large btn-primary next-section-button next">{{ trans('global.next')}}</button>

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
	$(document).ready(function () {
		$('.next').click(function () {
			var id = {{$id}};
			$.ajax({
				type: 'POST',
				url: '{{ route($updateLocationFeature) }}',
				data: $('#featuresFormupdate').serialize(),
				success: function (data) {
					$('.error-message').text('');
					window.location.href = '{{$nextButton}}' + id;
				},
				error: function (response) {
					if (response.responseJSON && response.responseJSON.errors) {
						var errors = response.responseJSON.errors;
						$('.error-message').text('');

						// Then display new error messages
						for (var field in errors) {
							if (errors.hasOwnProperty(field)) {
								var errorMessage = errors[field][
									0
								]; // get the first error message
								$('#featuresserror-' + field).text(errorMessage);
							}
						}
					}
				}
			});
		});


	});
</script>
@endsection