<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>503 - Service Unavailable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(-45deg, #667eea, #764ba2, #6b73ff, #000dff);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
            overflow: hidden;
            text-align: center;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.1);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            animation: fadeIn 1.5s ease;
            max-width: 500px;
            width: 90%;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 100px;
            font-weight: 700;
            letter-spacing: 5px;
        }

        p {
            font-size: 18px;
            margin: 20px 0;
            opacity: 0.9;
        }

        .countdown {
            font-size: 22px;
            margin: 20px 0;
            font-weight: 500;
        }

        button {
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            background: white;
            color: #333;
            transition: 0.3s ease;
        }

        button:hover {
            background: #000;
            color: #fff;
            transform: scale(1.05);
        }

        .dots span {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin: 5px;
            background: white;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .dots span:nth-child(1) { animation-delay: -0.32s; }
        .dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        footer {
            margin-top: 30px;
            font-size: 14px;
            opacity: 0.7;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>503</h1>
    <p>Oops! We're currently under maintenance.</p>

    <div class="dots">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="countdown">
        Refreshing in <span id="timer">30</span> seconds...
    </div>

    <button onclick="location.reload()">Refresh Now</button>

    <footer>
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</div>

<script>
    let timeLeft = 30;
    const timerElement = document.getElementById('timer');

    const countdown = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            location.reload();
        }
    }, 1000);
</script>

</body>
</html>
