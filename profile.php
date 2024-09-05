<?php
require_once 'class/user.php';
if (!isset($_SESSION['id'])) {
    header('location: index.php');
    exit;
}
$user = new User();
$user->fillUserData($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <link rel="stylesheet" href="w3.css">
    <title>Добро пожаловать, <?php echo $user->getUsername(); ?>!</title>
</head>

<body class="w3-light-grey ">
    <div class="w3-bar w3-top w3-black w3-large">
        <form action="process.php" method="post">
        <input type="hidden" name="logout" value="1">
            <button class="w3-button w3-hover-red w3-bar-item w3-right" type="submit" value="logout">Выйти</button>
        </form>
    </div>
    <di class="w3-main" style="margin-top:43px;">

        <div class="w3-panel w3-center w3-padding-16 w3-dark-gray">
            <h5>Добро пожаловать, <?php echo $user->getUsername(); ?>!</h5>
        </div>

        <div class="w3-panel w3-center">
            <div class="w3-row-padding" style="margin:0 -16px">

                <h5 class="w3-bottombar w3-border-green ">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    } else
                        echo "Ваши данные";
                    ?>
                </h5>
            </div>
        </div>

        <?php include "web/profile.html"?>

    </div>

    <div class="w3-padding w3-padding-48"></div>
    <div class="w3-container w3-dark-grey w3-padding-48"></div>

    <footer class="w3-container w3-black">
        <p>Powered by intern</p>
    </footer>
</body>

</html>
