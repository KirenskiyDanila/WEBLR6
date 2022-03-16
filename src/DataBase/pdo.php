<?php

namespace App\DataBase;

class pdo
{
    /*
     * Подключается к базе данных, возвращает объект класса PDO.
     */
    public static function connect() : \PDO
    {
        $ini_array = parse_ini_file('parameters.ini');
        try {
            $pdo = new \PDO('pgsql:host=' . $ini_array['host'] . ';port=' . $ini_array['port'] .
                ';dbname=' . $ini_array['name'] . ';user=' . $ini_array['login'] . ';password=' . $ini_array['password']);
        } catch (PDOException $exception) {
            echo "Ошибка подключения к БД: " . $exception->getMessage();
            die();
        }
        return $pdo;
    }
}