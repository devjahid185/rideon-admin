<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer - {{ __('RideOn Taxi') }}</title>
    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .installer-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .installer-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .installer-header h1 {
            font-size: 32px;
            font-weight: bold;
        }

        .installer-steps {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .installer-steps .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        @media (min-width: 576px) {
            .installer-steps .step-item {
                width: calc(50% - 10px);
            }
        }

        @media (min-width: 768px) {
            .installer-steps .step-item {
                width: calc(33.33% - 10px);
            }
        }

        @media (min-width: 992px) {
            .installer-steps .step-item {
                width: calc(16.66% - 10px);
            }
        }

        .installer-steps .step-item:before {
            content: '';
            position: absolute;
            top: 25px;
            left: 50%;
            width: calc(100% - 50px);
            height: 2px;
            background-color: #ccc;
            z-index: -1;
        }

        .installer-steps .step-item:last-child:before {
            display: none;
        }

        .installer-steps .step-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: #007bff;
            color: #fff;
            border-radius: 50%;
            font-size: 18px;
            font-weight: bold;
            position: relative;
            z-index: 1;
            margin-bottom: 10px;
        }

        .installer-steps .step-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .installer-steps .step-item.active .step-icon {
            background-color: #28a745;
        }

        .installer-steps .step-item.active .step-title {
            color: #28a745;
        }

        /* CSS for Loading Overlay */
        #loading {
            display: none;
            /* Initially hidden */
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            text-align: center;
        }

        #loading .spinner {
            position: absolute;
            top: 50%;
            left: 50%;

        }

        /* Spinner styles */
        .spinner-border {
            width: 3rem;
            height: 3rem;
            color: white;
            /* Spinner color */
        }
    </style>
</head>

<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1>{{ __('RideOn Taxi') }} Installer</h1>
        </div>
        <div class="installer-steps">
            @yield('steps')
        </div>
        <div class="installer-content">
            @yield('content')
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @yield('scripts')
</body>

</html>