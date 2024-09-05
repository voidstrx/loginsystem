<?php
require_once "class/user.php";
function check_captcha($token)
{
    $ch = curl_init();
    $args = http_build_query([
        "secret" => '',
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'], // Нужно передать IP-адрес пользователя.
        // Способ получения IP-адреса пользователя зависит от вашего прокси.
    ]);
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }
    $resp = json_decode($server_output);
    return $resp->status === "ok";
}


function httpPostRequest()
{
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('location: index.php');
        return;
    }
    //update profile
    if (isset($_SESSION['id']) && isset($_POST['type']) && $_POST['type'] === 'update') {
        $user = new User();
        $user->request('updateProfile',$_POST['username'], $_POST['new_password'], $_POST['email'], 
        $_POST['confirm_password'], $_POST['phone'],$_SESSION['id'],$_POST['password']); 
        
        $_SESSION['error'] = $user->getErrors();
        header('location: profile.php');
        return;
    }

    if (isset($_POST['type'])) {
        if (isset($_POST["smart-token"]) && check_captcha($_POST['smart-token'])) {
            $user = new User();

            //registration
            if ($_POST['type'] === 'up')
                if ($user->request('signUp',$_POST['username'], $_POST['password'], $_POST['email'], 
                $_POST['confirm_password'], $_POST['phone'])) {
                    header('location: profile.php');
                    return;
                }

            //authentication
            if ($_POST['type'] === 'in')
                if ($user->request('signIn',$_POST['username'], $_POST['password'])) {
                    header('location: profile.php');
                    return;
                }


            $_SESSION['error'] = $user->getErrors();
            header('location: index.php');


        } else {

            $_SESSION['error'] = "Ошибка проверки капчи";
            header('location: index.php');
        }
    }
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
    httpPostRequest();
else {
    $_SESSION['error'] = "Непредвиденное действие";
    header('location: index.php');
}
