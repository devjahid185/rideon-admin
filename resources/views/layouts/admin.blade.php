<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> {{ isset($siteName) && $siteName ? $siteName : trans('global.site_title') }}</title>
    <link rel="shortcut icon" href="{{ $faviconPath ?? asset('default/favicon.png') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"
        rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/AdminLTE.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/skins/_all-skins.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
    <style>
        .top-loader {
            height: 3px;
            background-color: #7e7013;
            width: 0%;
            transition: width 0.5s ease;
        }
    </style>

    <!-- Ionicons -->
    <link rel="stylesheet" atr="a"
        href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    @php(auth()->user()->loadMissing('roles'))
    @php($isSuper = auth()->user()->roles->contains(function ($r) {
    return strtolower(trim($r->title)) === 'supper admin';
}))

    @if ($isSuper)
        <link rel="stylesheet" href="{{ asset('css/custom-live.css') }}?v={{ time() }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ time() }}">
    @endif

    <link type="text/css" href="{{ asset('css/dashboard.css') }}?{{ time() }}" rel="stylesheet" />
    @yield('styles')

</head>

<body class="sidebar-mini skin-purple" style="height: auto; min-height: 100%;">
    <div x-data="pageTransition()" x-init="init()" x-cloak
        style="position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;">
        <div x-ref="bar" class="top-loader"></div>
    </div>

    <div class="wrapper" style="height: auto; min-height: 100%;">
          @if ($isSuper)
        @include('partials.header-new')
         @else
  @include('partials.header')
          @endif

        @include('partials.menu')

        <div class="content-wrapper" style="min-height: 960px;">
            @if (session('message'))
                <div class="row" style='padding:20px 20px 0 20px;'>
                    <div class="col-lg-12">
                        <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                    </div>
                </div>
            @endif
            @if ($errors->count() > 0)
                <div class="row" style='padding:20px 20px 0 20px;'>
                    <div class="col-lg-12">
                        <div class="alert alert-danger">
                            <ul class="list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
        <footer class="main-footer text-center">
            <strong>{{ $siteName }} &copy;</strong>
            {{ trans('global.allRightsReserved') }}
            Powered by
            <a href="https://unibooker.app/" target="_blank">UniBooker.app</a>
            |
            Load time: {{ number_format(microtime(true) - LARAVEL_START, 2) }}s
        </footer>

        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        $(function() {
            let copyButtonTrans = '{{ trans('global.copy') }}'
            let csvButtonTrans = '{{ trans('global.csv') }}'
            let excelButtonTrans = '{{ trans('global.excel') }}'
            let pdfButtonTrans = '{{ trans('global.pdf') }}'
            let printButtonTrans = '{{ trans('global.print') }}'
            let colvisButtonTrans = '{{ trans('global.colvis') }}'
            let selectAllButtonTrans = '{{ trans('global.select_all') }}'
            let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

            let languages = {
                'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json',
                'ar': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json',
                'fr': 'https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json'
            };

            $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
                className: 'btn'
            })
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: languages['{{ app()->getLocale() }}']
                },
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }, {
                    orderable: false,
                    searchable: false,
                    targets: -1
                }],
                select: {
                    style: 'multi+shift',
                    selector: 'td:first-child'
                },
                order: [],
                scrollX: true,
                pageLength: 100,
                dom: 'lBfrtip<"actions">',
                buttons: [{
                        extend: 'selectAll',
                        className: 'btn-primary',
                        text: selectAllButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        },
                        action: function(e, dt) {
                            e.preventDefault()
                            dt.rows().deselect();
                            dt.rows({
                                search: 'applied'
                            }).select();
                        }
                    },
                    {
                        extend: 'selectNone',
                        className: 'btn-primary',
                        text: selectNoneButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn-default',
                        text: copyButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-default',
                        text: csvButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-default',
                        text: excelButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-default',
                        text: pdfButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn-default',
                        text: printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn-default',
                        text: colvisButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            $.fn.dataTable.ext.classes.sPageButton = '';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '{{ trans('global.areYouSure') }}',
                text: '{{ trans('global.Arewantodeletethis') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ trans('global.yes') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form with the specified ID
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#daterange-btn').daterangepicker({
                opens: 'right',
                autoUpdateInput: false,
                ranges: {
                    'Anytime': [moment(), moment()],
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom Range'
                }
            });
            const storedStartDate = localStorage.getItem('selectedStartDate');
            const storedEndDate = localStorage.getItem('selectedEndDate');
            const urlFrom = "{{ request()->input('from') }}";
            const urlTo = "{{ request()->input('to') }}";
            if (storedStartDate && storedEndDate && urlFrom && urlTo) {
                const startDate = moment(storedStartDate);
                const endDate = moment(storedEndDate);
                $('#daterange-btn').data('daterangepicker').setStartDate(startDate);
                $('#daterange-btn').data('daterangepicker').setEndDate(endDate);
                $('#daterange-btn').val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
            } else {
                $('#daterange-btn').val('');
                $('#startDate').val('');
                $('#endDate').val('');
                localStorage.removeItem('selectedStartDate');
                localStorage.removeItem('selectedEndDate');
            }
            $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
                $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));
                localStorage.setItem('selectedStartDate', picker.startDate.format('YYYY-MM-DD'));
                localStorage.setItem('selectedEndDate', picker.endDate.format('YYYY-MM-DD'));
            });
            $('#daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#startDate').val('');
                $('#endDate').val('');
                localStorage.removeItem('selectedStartDate');
                localStorage.removeItem('selectedEndDate');
            });
            var storedModuleId = localStorage.getItem('module_id');
            if (storedModuleId) {
                $('#module_id_input').val(storedModuleId);
            }

            $('.module-popup-item').on('click', function(event) {
                event.preventDefault();
                var moduleId = $(this).data('module-id');
                var moduleUrl = $(this).data('url');
                var filterType = $(this).data('filter');
                var requestData = {
                    'status': '1',
                    'pid': moduleId,
                    'type': 'default_module',
                    'module_id': moduleId
                };

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                requestData['_token'] = csrfToken;
                $.ajax({
                    url: '/admin/update-module-status',
                    type: 'POST',
                    data: requestData,
                    success: function(data) {
                        $('#module_id_input').val(moduleId);
                        localStorage.setItem('module_id', moduleId);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error: ' + status);
                    }
                });
            });
        });

        $(document).ready(function() {
            @if (session('error'))
                toastr.error("{{ session('error') }}", 'Error', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right"
                });
            @endif

            @if (session('success'))
                toastr.success("{{ session('success') }}", 'Success', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right"
                });
            @endif
        });

        function pageLoader() {
            return {
                bar: null,
                init() {
                    this.bar = this.$refs.bar;
                    setTimeout(() => {
                        document.addEventListener('click', e => {
                            const link = e.target.closest('a');
                            if (!link) return;
                            const href = link.getAttribute('href');
                            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                            e.preventDefault();
                            this.start(href);
                        });
                    }, 50);
                },
                start(url) {
                    this.bar.style.width = '0%';
                    this.bar.style.transition = 'width 0.5s ease-out';
                    setTimeout(() => this.bar.style.width = '50%', 10);
                    window.location.href = url;
                }
            }
        }
        window.addEventListener('beforeunload', () => {
            const bar = document.querySelector('[x-ref="bar"]');
            if (bar) bar.style.width = '50%';
        });
</script>
<script>
   (function () {
        var toggle = document.getElementById('dark-mode-toggle');
        if (!toggle) return;

        var root = document.documentElement;

        if (localStorage.getItem('dark_mode') === '1') {
            root.classList.add('dark-mode');
        }

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            root.classList.toggle('dark-mode');
            localStorage.setItem('dark_mode', root.classList.contains('dark-mode') ? '1' : '0');

            if (typeof renderDashboardCharts === 'function') {
                renderDashboardCharts();
            }
        });
    })();
</script>

    @yield('scripts')
    <script src="{{ asset('js/resources/main.js') }}?{{ time() }}"></script>

</body>

</html>
