@extends('layouts.admin')
@section('styles')
    <style>
        .setup-button {
            padding: 20px 40px;
            font-size: 24px;
            font-weight: bold;
            background-color: #151515;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .setup-button:hover {
            background-color: #161717;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .setup-button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .instruction-text {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 6px;
            border-left: 4px solid #18bebd;
        }

        .cron-url {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
        }

        .frequency {
            color: #dc3545;
            font-weight: bold;
        }

        .cron-panel {
            margin-top: 20px;
        }

        .command-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
            word-break: break-all;
            position: relative;
        }

        .timing-info {
            color: #666;
            font-style: italic;
        }

        .copy-btn {
            position: absolute;
            top: -12px;
            right: 5px;
        }

        .copy-success {
            color: green;
            display: none;
            margin-left: 10px;
        }
    </style>
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3 settings_bar_gap">
                <div class="box box-info box_info">
                    <div class="">
                        <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">{{ trans('global.manage_settings') }}
                        </h4>
                        @include('admin.generalSettings.general-setting-links.links')
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.project_setup') }}</h3><span class="email_status"
                            style="display: none;">(<span class="text-green"><i class="fa fa-check"
                                    aria-hidden="true"></i>Verified</span>)</span>
                    </div>
                    <form id="fees_setting" method="post" action="#" class="form-horizontal " novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="box-body ">
                            <button id="setupButton" class="setup-button">{{ trans('global.project_setup') }}</button>
                            <div class="instruction-text" style="font-size: 13px">
                                If you're experiencing any of these issues:
                                <ul style="text-align: left; margin-top: 2px;">
                                    <li>Images not uploading</li>
                                    <li>Project cache not clearing</li>
                                    <li>System running slowly</li>
                                </ul>
                                Please click the Project Setup button above to resolve these issues.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.project_cleanup') }}</h3><span class="email_status"
                            style="display: none;">(<span class="text-green"><i class="fa fa-check"
                                    aria-hidden="true"></i>Verified</span>)</span>
                    </div>
                    <form id="fees_setting" method="post" action="#" class="form-horizontal " novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="box-body ">
                            <button id="cleanupButton" class="setup-button">{{ trans('global.project_cleanup') }}</button>
                            <div class="instruction-text" style="font-size: 13px">
                                If you're experiencing any of these issues:
                                <ul style="text-align: left; margin-top: 2px;">
                                    <li>System running slowly due to excess data</li>
                                    <li>Data will be deleted from the following tables: Media, ItemMeta, Item, Wallet,
                                        Payout, VendorWallet, ItemWishlist, SupportTicketReply, SupportTicket, Transaction,
                                        Review, Booking, AddCoupon, AppUserOtp, AppUsersBankAccount, AppUserMeta, and
                                        AppUser.</li>
                                </ul>
                                Please click the <strong>{{ trans('global.project_cleanup') }}</strong> button above to
                                clear
                                unnecessary data and improve system performance.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-3">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fas fa-clock text-primary"></i>
                            {{ trans('global.cron_job_settings') }}</h3><span class="email_status"
                            style="display: none;">(<span class="text-green"><i class="fa fa-check"
                                    aria-hidden="true"></i>Verified</span>)</span>
                    </div>
                    <div class="box-body py-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Note:</strong> These cron jobs should be set up on your server to automate various
                                system maintenance tasks.
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-body">
                                    <p> you can use PHP CLI:</p>
                                    <div class="command-box">
                                        <button class="btn btn-xs btn-default copy-btn" onclick="copyToClipboard(this)">
                                            <span class="glyphicon glyphicon-copy"></span> {{ trans('global.copy') }}
                                        </button>
                                        <span class="copy-success"><span class="glyphicon glyphicon-ok"></span>
                                        </span>
                                        <code>* * * * * php -q {{ base_path('artisan') }} schedule:run</code>
                                    </div>
                                    <div class="command-box">
                                        <button class="btn btn-xs btn-default copy-btn" onclick="copyToClipboard(this)">
                                            <span class="glyphicon glyphicon-copy"></span> {{ trans('global.copy') }}
                                        </button>
                                        <span class="copy-success"><span class="glyphicon glyphicon-ok"></span>
                                        </span>
                                        <code>* * * * * php -q {{ base_path('artisan') }} queue:work --tries=3 --timeout=90 --sleep=3</code>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        function copyToClipboard(button) {
            const commandBox = button.closest('.command-box');
            const codeElement = commandBox.querySelector('code');
            const successMessage = commandBox.querySelector('.copy-success');
            // Create temporary textarea
            const textarea = document.createElement('textarea');
            textarea.value = codeElement.innerText;
            document.body.appendChild(textarea);
            // Select and copy text
            textarea.select();
            document.execCommand('copy');
            // Remove temporary textarea
            document.body.removeChild(textarea);
            // Show success message
            successMessage.style.display = 'inline';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 2000);
        }
    </script>
    <script>
        $(document).ready(function ($) {
            $('#setupButton').on('click', function (event) {
                event.preventDefault();
                $(this).attr('disabled', true).text('Processing...');
                $.ajax({
                    url: "{{ route('admin.project_setup_update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $('#setupButton').attr('disabled', false).text('Project Setup');
                        toastr.success(response.message, 'success', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    },
                    error: function (xhr) {
                        $('#setupButton').attr('disabled', false).text('Project Setup');
                        alert('An error occurred. Please try again.');
                    }
                });
            });
            $('#cleanupButton').on('click', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: '{{ trans('global.are_you_sure') }}',
                    text: 'Are You sure you want to clean Database?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).attr('disabled', true).text('Processing...');
                        $.ajax({
                            url: "{{ route('admin.project_cleanup_update') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                $('#cleanupButton').attr('disabled', false).text(
                                    'Project Cleanup');
                                toastr.success(response.message, 'Success', {
                                    closeButton: true,
                                    progressBar: true,
                                    positionClass: "toast-bottom-right"
                                });
                            },
                            error: function (xhr) {
                                $('#cleanupButton').attr('disabled', false).text(
                                    'Project Cleanup');
                                toastr.error('An error occurred. Please try again.',
                                    'Error', {
                                    closeButton: true,
                                    progressBar: true,
                                    positionClass: "toast-bottom-right"
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection