@extends('vendor.layout')
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
									
										{{ trans('global.feature_title') }}
									

									<span class="text-danger">*</span>
								</p>
								@foreach($features as $feature)
									<input type="checkbox" name="features[]" value="{{ $feature['id'] }}" {{ in_array($feature['id'], $features_ids) ? 'checked' : '' }}>
									{{ $feature['name'] }}<br>
								@endforeach
								<span class="text-danger" id="featureserror-features"></span>
							</div>


							
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