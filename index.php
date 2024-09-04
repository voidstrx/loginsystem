<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <link rel="stylesheet" href="w3.css">
    <title>Добро пожаловать!</title>
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>

<body class="w3-light-grey ">
    <div class="w3-bar w3-top w3-black w3-large w3-padding-24"></div>
    <div class="w3-main" style="margin-top:43px;">

        <div class="w3-container w3-center w3-padding-16 w3-dark-gray">
            <h5>Добро пожаловать!</h5>
        </div>

        <div class="w3-panel w3-center">
            <div class="w3-row-padding" style="margin:0 -16px">

                <h5 class="w3-bottombar w3-border-green ">
                    <?php

                    if (!isset($_SESSION["id"])) {
                        if (isset($_SESSION['error'])) {
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        } else
                            echo "Вы не авторизованы!";

                        echo "</h5>";

                        include "web/signin.html";
                        include "web/signup.html";
                    } else {
                        echo "Перейдите по ссылке ниже</h5>";

                        echo "<button class=\"w3-btn w3-hover-green\"
                        onclick=\"location.href='profile.php'\">
                    Страница вашего профиля
                </button>";

                    }
                    ?>

            </div>
        </div>
    </div>

    <div class="w3-padding w3-padding-48"></div>
    <div class="w3-container w3-dark-grey w3-padding-48"></div>

    <footer class="w3-container w3-black">
        <p>Powered by intern</p>
    </footer>
</body>

</html>