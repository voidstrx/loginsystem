<?php
session_start();
require_once "database.php";
class User
{
    private $id;
    private $username;
    private $email;
    private $password;
    private $phone;
    private $confirm_password;
    private $current_password;
    private $error_messages = [];

    public function getErrors()
    {
        return implode("<br>", array_unique($this->error_messages));
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPhone()
    {
        return $this->phone;
    }

    private function setErrorMessage($error_message)
    {
        array_push($this->error_messages, $error_message);

    }

    public static function validateInput($input)
    {
        $input = trim($input);
        $input = stripcslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    private function validateUsername()
    {
        if (!preg_match("/^[-_a-zA-Z0-9.]+$/", $this->username))
            $this->setErrorMessage("Имя пользователя может только содержать латинские символы  и цифры");

        if (mb_strlen($this->username) < 3)
            $this->setErrorMessage("Имя пользователя не может быть меньше 4 символов");
        try {
            if (Database::isUsernameExists($this->username, $this->id))
                $this->setErrorMessage("Данное имя пользователя уже существует");
        } catch (Exception) {
            $this->setErrorMessage("Сайт временно недоступен");
        }

    }

    private function validateEmail()
    {
        if (!preg_match("/[@]/", $this->email))
            $this->setErrorMessage("Проверьте email");
        try {
            if (Database::isEmailExists($this->email, $this->id))
                $this->setErrorMessage("Данный email уже существует");
        } catch (Exception) {
            $this->setErrorMessage("Сайт временно недоступен");
        }
    }

    private function validatePhone()
    {
        if (!preg_match("/^[0-9]*$/", $this->phone))
            $this->setErrorMessage("Телефон должен содержать только цифры");
        else

            try {
                if (Database::isPhoneExists($this->phone, $this->id))
                    $this->setErrorMessage("Данный телефон уже существует");
            } catch (Exception) {
                $this->setErrorMessage("Сайт временно недоступен");
            }
    }

    private function validatePassword()
    {
        if (mb_strlen($this->password) < 7)
            $this->setErrorMessage("Пароль должен содержать не менее 8 символов");
        if (!preg_match("/[A-Z]/", $this->password))
            $this->setErrorMessage("Пароль должен содержать заглавные латинские буквы");
        if (!preg_match("/[a-z]/", $this->password))
            $this->setErrorMessage("Пароль должен содержать строчные латинские буквы");
        if (!preg_match("/[0-9]/", $this->password))
            $this->setErrorMessage("Пароль должен содержать цифры");
        if (!preg_match("/[!@#$%^&*]/", $this->password))
            $this->setErrorMessage("Пароль должен содержать не менее одного специального символа !@#$%^&amp;*");
        if (!($this->password === $this->confirm_password))
            $this->setErrorMessage("Пароли должны совпадать");
    }


    private function isUsernameOrEmailExists()
    {
        try {
            if (Database::isUsernameExists($this->username, $this->id)) {
                $this->id = Database::getIdByUsername($this->username);
                return true;
            }
        } catch (Exception) {
            $this->setErrorMessage("Сайт временно недоступен");
            return false;
        }

        try {
            if (Database::isEmailExists($this->username, $this->id)) {
                $this->id = Database::getIdByEmail($this->username);
                return true;
            }
        } catch (Exception) {
            $this->setErrorMessage("Сайт временно недоступен");
            return false;
        }

        $this->setErrorMessage("Данное имя пользователя не существует");
        $this->setErrorMessage("Данный email не существует");
        return false;
    }

    public function fillUserData($id)
    {
        try {
            $data = Database::getUserData($id);
            $this->id = $data['id'];
            $this->username = $data['login'];
            $this->email = $data['email'];
            $this->phone = $data['phone'];
        } catch (Exception) {
            $this->setErrorMessage("Сайт временно недоступен");
            return false;
        }
    }

    private function isPasswordCorrect($password)
    {
        try {
            if (!password_verify($password, Database::getPasswordHash($this->id))) {
                $this->setErrorMessage("Пароль не верен");
                return false;
            }
        } catch (Exception) {
            $this->setErrorMessage("Сайт временно недоступен");
            return false;
        }
        return true;
    }

    private function signIn()
    {
        if (!$this->isUsernameOrEmailExists() || !$this->isPasswordCorrect($this->password))
            return false;

        //$_SESSION['user'] = serialize($this);
        $_SESSION['id'] = $this->id;
    }

    private function signUp()
    {
        $this->validateUsername();
        $this->validateEmail();
        $this->validatePhone();
        $this->validatePassword();

        if (count($this->error_messages) === 0) {
            try {
                Database::createUser($this->username, password_hash($this->password, PASSWORD_DEFAULT), $this->phone, $this->email);
                if($this->isUsernameOrEmailExists())
                    $_SESSION['id'] = $this->id;
            } catch (Exception) {
                $this->setErrorMessage("Сайт временно недоступен");
            }
        }
    }

    private function updateProfile()
    {
        $this->isPasswordCorrect($this->current_password);
   
        $this->validateUsername();
        $this->validateEmail();
        $this->validatePhone();

        if ($this->password != '') //if user isn't going to change password
            $this->validatePassword();
        else
            $this->password = $this->current_password;

        if (count($this->error_messages) === 0) {
            try {
                Database::setUserData($this->id, $this->username, password_hash($this->password, PASSWORD_DEFAULT), $this->phone, $this->email);
            } catch (Exception) {
                $this->setErrorMessage("Сайт временно недоступен");
            }
        }

    }

    public function request($type, $username, $password, $email = '', $confirm_password = '', $phone = '', $id = '0', $current_password = '')
    {

        $this->id = $id;//0 means a new user
        $this->username = self::validateInput($username);
        $this->password = self::validateInput($password);

        switch ($type) {
            case 'signIn':
                $this->signIn();
                break;
            case 'signUp':
                $this->email = self::validateInput($email);
                $this->phone = self::validateInput($phone);
                $this->confirm_password = self::validateInput($confirm_password);
                $this->signUp();
                break;
            case 'updateProfile':
                $this->email = self::validateInput($email);
                $this->phone = self::validateInput($phone);
                $this->confirm_password = self::validateInput($confirm_password);
                $this->current_password = self::validateInput($current_password);
                $this->updateProfile();
        }

        return count($this->error_messages) === 0;
    }



}

// $user = new User();
// $pass = "P@ssw0rd";
// $hash = password_hash($pass, PASSWORD_DEFAULT);
// echo $hash;
//$name = "user";
//$user->request('signUp', $name, $pass, 'test@mail.ru', $pass, '', '0', '555');
//echo $user->getErrors();
