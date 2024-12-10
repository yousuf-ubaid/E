<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Coming Soon</title>

    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>

    <!--Google font-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/mat-dash.css'); ?>">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px); /* Start lower */
            }
            to {
                opacity: 1;
                transform: translateY(0); /* End at the original position */
            }
        }

        @-webkit-keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px); /* Start lower */
            }
            to {
                opacity: 1;
                transform: translateY(0); /* End at the original position */
            }
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-size: 40px;
            margin-bottom: 20px;
        }
        .countdown {
            display: flex;
            justify-content: center;
            gap: 30px; /* Add space between the time segments */
        }
        .time-box {
            text-align: center;
        }
        .time-box span {
            display: block;
            font-size: 48px; /* Smaller number size */
            font-weight: bold;
            color: #333;
        }
        .time-box p {
            margin: 0;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body class="bg-dark" onload="countdown()">
<div class="container">
    <h1 class="text-white">Coming Soon</h1>
    <div id="countdown" class="countdown mb-7">
        <div class="time-box">
            <span id="days" class="text-white">00</span>
            <p>Days</p>
        </div>
        <div class="time-box">
            <span id="hours" class="text-white">00</span>
            <p>Hours</p>
        </div>
        <div class="time-box">
            <span id="minutes" class="text-white">00</span>
            <p>Minutes</p>
        </div>
        <div class="time-box">
            <span id="seconds" class="text-white">00</span>
            <p>Seconds</p>
        </div>
    </div>
    <a class="btn btn-primary" href="<?php echo site_url('login') ?>" role="button">Go Back to Home</a>
</div>
</body>
</html>
<script>
    function countdown() {
        const targetDate = new Date("December 31, 2024 23:59:59").getTime();

        setInterval(function () {
            const now = new Date().getTime();
            const timeDifference = targetDate - now;

            const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

            document.getElementById("days").innerHTML = days;
            document.getElementById("hours").innerHTML = hours;
            document.getElementById("minutes").innerHTML = minutes;
            document.getElementById("seconds").innerHTML = seconds;

        }, 1000);
    }
</script>
