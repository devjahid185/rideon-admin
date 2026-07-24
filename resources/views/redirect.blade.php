<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Installation Complete</title>

    <!-- Optional: Add some styles to improve appearance -->
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            display: inline-block;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #4CAF50;
        }
        p {
            margin: 20px 0;
        }
        .btn-primary {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        #countdown {
            font-size: 2rem;
            font-weight: bold;
            color: #FF0000;
        }
    </style>

    <!-- Countdown JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var countdownElement = document.getElementById('countdown');
            var countdown = 6; // Starting countdown value

            var timer = setInterval(function () {
                countdownElement.textContent = countdown; // Update the countdown display
                countdown--;

                if (countdown < 0) {
                    clearInterval(timer);
                    window.location.href = "{{ url('/') }}"; // Redirect to home page when countdown is over
                }
            }, 600); // 1000 milliseconds = 1 second
        });
    </script>

</head>
<body>
    <div class="container">
        <h1>Installation Complete</h1>
        <p>Congratulations! Your application has been successfully installed.</p>
        <p>You will be redirected to the home page in <span id="countdown">6</span> seconds.</p>
        <p>If you are not redirected, <a href="{{ url('/login') }}" class="btn-primary">click here</a>.</p>
    </div>
</body>
</html>
