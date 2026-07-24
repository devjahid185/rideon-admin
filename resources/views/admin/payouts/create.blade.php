@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('global.create') }} {{ trans('global.payout_title_singular') }}
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('admin.payouts.store') }}" enctype="multipart/form-data">
                            @csrf

                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- Driver selector --}}
                            <div class="form-group {{ $errors->has('vendorid') ? 'has-error' : '' }}">
                                <label class="required" for="drivers">{{ trans('menu.driver') }}</label>
                                <select class="form-control select2" name="vendorid" id="drivers" required>
                                    <option value=""></option>
                                </select> 
                                @if($errors->has('vendorid'))
                                    <span class="help-block" role="alert">{{ $errors->first('vendorid') }}</span>
                                @endif
                            </div>

                            {{-- Amount with wallet‐balance hint --}}
                            <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                                <label class="required" for="amount">{{ trans('global.amount') }}</label>
                                <input class="form-control" type="number" name="amount" id="amount"
                                    value="{{ old('amount', '') }}" step="0.01" required>
                                <small id="wallet_balance_display" class="form-text text-muted"></small>
                                @if($errors->has('amount'))
                                    <span class="help-block" role="alert">{{ $errors->first('amount') }}</span>
                                @endif
                            </div>

                            {{-- Payment method --}}
                            <div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}" required>
                                <label class="required"  for="payment_method">{{ trans('global.payment_method') }}</label>
                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option value="">{{ trans('global.please_select') }}</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal
                                    </option>
                                    <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe
                                    </option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="upi" {{ old('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                                </select>
                                @if($errors->has('payment_method'))
                                    <span class="help-block" role="alert">{{ $errors->first('payment_method') }}</span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('payout_proof') ? 'has-error' : '' }}">
                                <label class="required" for="payout_proof">{{ trans('global.payout_proof') }}</label>
                                <div class="needsclick dropzone" id="payout_proof-dropzone">
                                </div>
                                @if($errors->has('payout_proof'))
                                    <span class="help-block" role="alert">{{ $errors->first('payout_proof') }}</span>
                                @endif
                            </div>
                            {{-- Payout status --}}
                            <div class="form-group {{ $errors->has('payout_status') ? 'has-error' : '' }}" required>
                                <label class="required" for="payout_status">{{ trans('global.payout_status') }}</label>
                                <select class="form-control" name="payout_status" id="payout_status">
                                    <option value="" disabled {{ old('payout_status') === null ? 'selected' : '' }}>
                                        {{ trans('global.pleaseSelect') }}
                                    </option>
                                    <option value="Success" {{ old('payout_status') === 'Success' ? 'selected' : '' }}>
                                        Success
                                    </option>
                                </select>
                                @if($errors->has('payout_status'))
                                    <span class="help-block" role="alert">{{ $errors->first('payout_status') }}</span>
                                @endif
                            </div>

                            {{-- Submit --}}
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
@endsection

@section('scripts')
    <script>
        Dropzone.options.payoutProofDropzone = {
            url: '{{ route('admin.payouts.storeMedia') }}',
            maxFilesize: 10, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif,.pdf',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                $('form').find('input[name="payout_proof"]').remove()
                $('form').append('<input type="hidden" name="payout_proof" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="payout_proof"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($appUser) && $appUser->payout_proof)
                    var file = {!! json_encode($appUser->payout_proof) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="payout_proof" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
        },
            error: function (file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }


        $(document).ready(function () {

            $('#drivers').select2({
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('admin.payoutVendorSearch') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data.map(d => ({
                            id: d.id,
                            text: d.name
                            // no wallet_balance here
                        }))
                    }),
                    cache: true
                },
                placeholder: "{{ trans('global.please_select') }}"
            });

            $('#drivers').on('select2:select', function (e) {
                const vendorId = e.params.data.id;

                // fetch wallet balance
                $.ajax({
                    url: "{{ url('admin/driver/vendor-wallet') }}/" + vendorId,
                    method: 'GET',
                    success: function (res) {
                        const bal = parseFloat(res.wallet_balance) || 0;

                        // show balance
                        $('#wallet_balance_display')
                            .text('Wallet balance: ' + bal.toFixed(2));

                        // enforce max
                        $('#amount')
                            .attr('max', bal)
                            .off('input.walletcheck')
                            .on('input.walletcheck', function () {
                                const val = parseFloat(this.value) || 0;
                                this.setCustomValidity(val > bal
                                    ? 'Amount cannot exceed wallet balance!'
                                    : ''
                                );
                            });
                    },
                    error: function () {
                        alert('Could not fetch wallet balance. Please try again.');
                    }
                });
            });

            $('#drivers').on('select2:clear', function () {
                $('#wallet_balance_display').text('');
                $('#amount')
                    .removeAttr('max')
                    .off('input.walletcheck')
                    .get(0).setCustomValidity('');
            });
        });
    </script>
@endsection