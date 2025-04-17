<?php
session_start();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spik en Span - Carnavals Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFEB3B; /* Geel thema */
            color: #4CAF50; /* Groen thema */
            text-align: center;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #F44336; /* Rood thema */
            color: white;
            padding: 20px;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 30px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .logo {
            font-size: 40px;
            font-weight: bold;
            margin: 20px 0;
        }
        footer {
            background-color: #F44336;
            color: white;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .welcome {
            font-size: 20px;
            margin-top: 20px;
            color: #E91E63;
        }
    </style>
</head>
<body>

    <header>
        <h1>Spik en Span - Carnavals Tickets</h1>
    </header>

    <div class="logo">
        🎭 Spik en Span 🎉
    </div>

    <?php
    if (isset($_SESSION["username"])) {
        echo "<div class='welcome'>🎉 Welcome back, <strong>" . htmlspecialchars($_SESSION["username"]) . "</strong>! 🎉</div>";
    }
    ?>

    <p>Welkom! Koop je tickets voor de leukste carnavalsfeesten hier!</p>

    <button class="button" onclick="window.location.href='order.php'">Tickets Kopen</button>

    <?php if (!isset($_SESSION["username"])):?>
    <button class="button" onclick="window.location.href='login.php'">Inloggen</button>
    <?php else:?>
    <button class="button" onclick="window.location.href='profile.php'">Profile</button>
    <?php endif; ?>

    <footer>
        &copy; 2025 Spik en Span. Alle rechten voorbehouden.
        <a href="privacyverklaring.html">Bekijk onze privacyverklaring</a>
    </footer>
</body>
</html>