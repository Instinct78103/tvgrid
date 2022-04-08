<?php
require '../php/define.php';

//class DatabaseService
//{
//    private $db_host = SERVER;
//    private $db_name = DB;
//    private $db_user = USER;
//    private $db_pwd = PWORD;
//    private $connection;
//
//    public function getConnection()
//    {
//        $this->connection = null;
//
//        try {
//            $dsn = "mysql:host=" . $this->db_host . "; dbname =" . $this->db_name;
//            $this->connection = new PDO($dsn, $this->db_user, $this->db_pwd);
//        } catch (PDOException $exception) {
//            exit('Ошибка подключения к базе: ' . $exception->getMessage());
//        }
//
//        return $this->connection;
//    }
//}


class DatabaseService
{
    private $db_host = SERVER;
    private $db_name = DB;
    private $db_user = USER;
    private $db_pwd = PWORD;
    private $connection;

    public function getConnection()
    {
        $this->connection = null;

        try {
            $this->connection = new mysqli(
                $this->db_host,
                $this->db_user,
                $this->db_pwd,
                $this->db_name
            );
        } catch (PDOException $exception) {
            exit('Ошибка подключения к базе: ' . $exception->getMessage());
        }

        return $this->connection;
    }
}