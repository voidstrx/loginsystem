<?php
class Database
{
    private $servername = "localhost";

    private $username = "user";

    private $password = "password";

    private $dbname = "db";

    private $connection = null;

    private function connect()
    {
        return $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    }

    private function close()
    {
        if (!is_null($this->connection))
            $this->connection->close();
    }

    private static function query($sql)
    {
        try {
            $result = null;
            $db = new Database();
            $result = $db->connect()->query($sql);

        } catch (mysqli_sql_exception $e) {
            //TODO error loging? show to user?
            throw new Exception();
        } finally {
            $db->close();
        }
        return $result;
    }

    public static function isUsernameExists($username, $excludeId)
    {
        return self::query("select login from profile where login='{$username}' && id!='{$excludeId}';")->num_rows > 0;
    }

    public static function isEmailExists($email, $excludeId)
    {
        return self::query("select email from profile where email='{$email}' && id!='{$excludeId}';")->num_rows > 0;
    }

    public static function isPhoneExists($phone, $excludeId)
    {
        return self::query("select phone from profile where phone='{$phone}' && id!='{$excludeId}';")->num_rows > 0;
    }

    public static function getPasswordHash($id)
    {
        return self::query("select password from profile where id ='{$id}';")->fetch_row()[0];
    }

    public static function getIdByUsername($username)
    {
        return self::query("select id from profile where login ='{$username}';")->fetch_row()[0];
    }
    public static function getIdByEmail($email)
    {
        return self::query("select id from profile where email ='{$email}';")->fetch_row()[0];
    }
    public static function getUserData($id)
    {
        return self::query("select * from profile where id ='{$id}';")->fetch_all(MYSQLI_ASSOC)[0];
    }

    public static function setUserData($id, $login, $password, $phone, $email)
    {
        return self::query("update profile set login='{$login}',password='{$password}',phone='{$phone}',email='{$email}' where id='{$id}';");
    }

    public static function createUser($login, $password, $phone, $email)
    {
        return self::query("insert into profile values(default,'{$login}','{$password}','{$phone}','{$email}');");
    }

}