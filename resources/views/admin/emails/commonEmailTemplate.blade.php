<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        /* Add some basic styles for the email */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #ffc91f;
            color: #000;
            padding: 10px 0;
            text-align: center;
        }

        .header img {
            max-width: 100px;
        }

        /* Specific style for general_name in header */
        #site-name {
            color: #000;
            /* This ensures the color is white and not blue */
            text-decoration: none;
        }

        .content {
            padding: 20px;
            background-color: #f4f4f4;
        }

        .content table {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-collapse: collapse;
        }

        .content td {
            padding: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #999999;
            padding: 10px;
            background-color: #eeeeee;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <h2>Welcome to <span
                style="color: #ffffff; text-decoration: none; pointer-events: none; cursor: default;">{{ $emailData['general_name'] }}</span>
        </h2>
    </div>


    <!-- Content Section -->
    <div class="content">
        <table>
            <tr>
                <td>
                    {!! $emailData['data'] !!} <!-- Accessing 'data' from the $emailData array -->
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>Contact Us: <a href="mailto:{{ $emailData['general_email'] }}">{{ $emailData['general_email'] }}</a> | Phone:
            {{ $emailData['general_default_phone_country'] }}{{ $emailData['general_phone'] }}
        </p>
        <p>&copy; {{ date('Y') }} {{ $emailData['general_name'] }}. All rights reserved.</p>
    </div>
</body>

</html>