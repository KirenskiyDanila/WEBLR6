<?php

class User
{
    public function __construct()
    {
    }

    /*
     * Ищет пользователя в БД по его электронной почте.
     * Возвращает данные пользователя, если он найден.
     * Иначе возвращает ошибку.
     */
    public function get(string $email, array &$errors) : array
    {
        $pdo = \App\DataBase\pdo::connect();

        $sql = "SELECT * FROM site_user WHERE email = :email";
        $result = $pdo->prepare($sql);
        $result->bindParam(':email', $email);
        $result->execute();
        if ($result->rowCount() == 0) {
            $errors[] = 'Такого аккаунта не существует!';
        }
        $row = ($result->fetch(PDO::FETCH_ASSOC));
        return $row;
    }

    /*
     * Добавляет данные о пользователя в БД.
     */
    public function add(string $email, string $phone, string $name, string $hash) : void
    {
        $pdo = \App\DataBase\pdo::connect();

        $sql = "INSERT INTO site_user(phone, email, user_name, password) VALUES (:phone, :email, :user_name, :hash)";
        $result = $pdo->prepare($sql);
        $result->bindParam(':email', $email);
        $result->bindParam(':phone', $phone);
        $result->bindParam(':user_name', $name);
        $result->bindParam(':hash', $hash);
        $result->execute();
    }

    /*
     * Возвращает количество пользователей с такой электронной почтой или телефоном.
     */
    public function checkPhoneAndEmail(string $email, string $phone) : int
    {
        $pdo = \App\DataBase\pdo::connect();

        $sql = "SELECT * FROM site_user WHERE email = :email OR phone = :phone limit 1";
        $result = $pdo->prepare($sql);
        $result->bindParam(':email', $email);
        $result->bindParam(':phone', $phone);
        $result->execute();
        return $result->rowCount();
    }

}
