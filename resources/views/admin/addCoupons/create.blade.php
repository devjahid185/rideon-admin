@extends('layouts.admin')
@section('content')
    <div class="content">

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('global.create') }} {{ trans('global.addCoupon_title_singular') }}<a href="{{ route('admin.add-coupons.index') }}" class="btn btn-sm btn-secondary mb-3">
    <i class="fas fa-arrow-left mr-1"></i> Back to Coupons
</a>

                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('admin.add-coupons.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('coupon_title') ? 'has-error' : '' }}">
                                        <label class="required"
                                            for="coupon_title">{{ trans('global.coupon_titles') }}</label>
                                        <input class="form-control" type="text" name="coupon_title" id="coupon_title"
                                            value="{{ old('coupon_title', '') }}" required>
                                        @if ($errors->has('coupon_title'))
                                            <span class="help-block">{{ $errors->first('coupon_title') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('coupon_expiry_date') ? 'has-error' : '' }}">
                                        <label for="coupon_expiry_date">{{ trans('global.coupon_expiry_date') }}</label>
                                        <input class="form-control date" type="date" name="coupon_expiry_date"
                                            id="coupon_expiry_date" value="{{ old('coupon_expiry_date') }}"
                                            min="{{ date('Y-m-d') }}">
                                        @if ($errors->has('coupon_expiry_date'))
                                            <span class="help-block">{{ $errors->first('coupon_expiry_date') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('coupon_code') ? 'has-error' : '' }}">
                                        <label class="required" for="coupon_code">{{ trans('global.coupon_code') }}
                                            <span id="generateCouponButton"><i class="fas fa-cogs"></i></span>
                                        </label>

                                        <div class="input-group">
                                            <input class="form-control" type="text" name="coupon_code" id="coupon_code"
                                                value="{{ old('coupon_code', '') }}" required>
                                        </div>

                                        @if ($errors->has('coupon_code'))
                                            <span class="help-block">{{ $errors->first('coupon_code') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('min_order_amount') ? 'has-error' : '' }}">
                                        <label for="min_order_amount">{{ trans('global.min_order_amount') }}</label>
                                        <input class="form-control" type="number" name="min_order_amount"
                                            id="min_order_amount" value="{{ old('min_order_amount', '') }}" step="0.01">
                                        @if ($errors->has('min_order_amount'))
                                            <span class="help-block">{{ $errors->first('min_order_amount') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('coupon_value') ? 'has-error' : '' }}">
                                        <label class="required"
                                            for="coupon_value">{{ trans('global.coupon_value') }}</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" name="coupon_value" id="coupon_value"
                                                value="{{ old('coupon_value', '') }}" step="0.01" required>
                                            <select class="form-control" name="coupon_type" id="coupon_type" required>
                                                <option value="percentage"
                                                    {{ old('coupon_type') == 'percentage' ? 'selected' : '' }}>Percentage
                                                </option>
                                            </select>
                                        </div>
                                        @if ($errors->has('coupon_value'))
                                            <span class="help-block">{{ $errors->first('coupon_value') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('max_uses') ? 'has-error' : '' }}">
                                        <label for="max_uses">Maximum Uses</label>
                                        <input class="form-control" type="number" name="max_uses" id="max_uses"
                                            value="{{ old('max_uses', '') }}" min="1">

                                        <small class="text-muted">Leave blank for unlimited uses</small>

                                        @if ($errors->has('max_uses'))
                                            <span class="help-block">{{ $errors->first('max_uses') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('max_uses_per_user') ? 'has-error' : '' }}">
                                        <label for="max_uses_per_user">Maximum Uses Per User</label>
                                        <input class="form-control" type="number" name="max_uses_per_user"
                                            id="max_uses_per_user" value="{{ old('max_uses_per_user', '') }}"
                                            min="1">
                                        @if ($errors->has('max_uses_per_user'))
                                            <span class="help-block">{{ $errors->first('max_uses_per_user') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                                        <label>{{ trans('global.status') }}</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                                {{ trans('global.pleaseSelect') }}</option>
                                            @foreach (App\Models\AddCoupon::STATUS_SELECT as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ old('status', '1') === (string) $key ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="help-block">{{ $errors->first('status') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Use for First Booking?</label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <input type="checkbox" name="is_first_booking" id="is_first_booking"
                                                class="check" data-toggle="toggle" data-on="Yes" data-off="No"
                                                value="1" {{ old('is_first_booking') ? 'checked' : '' }}>
                                            <label for="is_first_booking" class="checktoggle">checkbox</label>
                                        </div>

                                        <small class="text-danger d-block mt-1">
                                            If you enable this, any other first-booking coupon will be automatically
                                            disabled.
                                        </small>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('generateCouponButton').addEventListener('click', function() {
                var generatedCoupon = generateCouponCode().toUpperCase();
                document.getElementById('coupon_code').value = generatedCoupon;
            });

            function generateCouponCode() {
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var length = 7;
                var coupon = '';
                for (var i = 0; i < length; i++) {
                    coupon += characters.charAt(Math.floor(Math.random() * characters.length));
                }
                return coupon;
            }
        });
        $(document).ready(function() {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                    return {
                        upload: function() {
                            return loader.file
                                .then(function(file) {
                                    return new Promise(function(resolve, reject) {
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST',
                                            '{{ route('admin.add-coupons.storeCKEditorImages') }}',
                                            true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';
                                        var genericErrorText =
                                            `Couldn't upload file: ${ file.name }.`;
                                        xhr.addEventListener('error', function() {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function() {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function() {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response
                                                    .message ?
                                                    `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                    `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`
                                                );
                                            }

                                            $('form').append(
                                                '<input type="hidden" name="ck-media[]" value="' +
                                                response.id + '">');

                                            resolve({
                                                default: response.url
                                            });
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function(
                                                e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id', '{{ $addCoupon->id ?? 0 }}');
                                        xhr.send(data);
                                    });
                                })
                        }
                    };
                }
            }

            var allEditors = document.querySelectorAll('.ckeditor');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(
                    allEditors[i], {
                        extraPlugins: [SimpleUploadAdapter]
                    }
                );
            }
        });
    </script>
@endsection
