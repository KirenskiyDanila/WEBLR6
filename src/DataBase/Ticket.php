<?php

class Ticket
{
    public function __construct()
    {
    }
    /*
     * Возвращает до 10 обьявлений из БД, начиная с заданного id.
     * Если id не задан - то возвращает первые 10 строк.
     */
    public function getRows(string $id = null) : array
    {
        $pdo = \App\DataBase\pdo::connect();
        if ($id == null) {
            $sql = "SELECT id FROM ticket WHERE is_closed = false ORDER BY id LIMIT 1";
            $result = $pdo->prepare($sql);
            $result->execute();
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
        }
        $sql = "SELECT id, photo, name, price FROM ticket WHERE is_closed = false AND id >= :id limit 10";
        $result = $pdo->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Возвращает обьявление с данным id.
     */
    public function getRow(string $id) : array
    {
        $pdo = \App\DataBase\pdo::connect();

        $sql = "SELECT * FROM ticket WHERE id = :id";
        $result = $pdo->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();
        return ($result->fetch(PDO::FETCH_ASSOC));
    }

    /*
     * Добавляет обьявление в БД.
     * Возвращает id добавленного обьявления.
     */
    public function add(
        string $phone,
        string $newPath,
        string $description,
        string $price,
        string $name
    ) : string
    {
        $pdo = \App\DataBase\pdo::connect();

        $sql = "INSERT INTO ticket(user_phone, photo, description, price, name)
        VALUES (:phone, :photo, :description, :price, :name)
        RETURNING id";
        $result = $pdo->prepare($sql);
        $result->bindParam(':phone', $phone);
        $result->bindParam(':photo', $newPath);
        $result->bindParam(':description', $description);
        $result->bindParam(':price', $price);
        $result->bindParam(':name', $name);
        $result->execute();
        $row = ($result->fetch(PDO::FETCH_ASSOC));
        return $row['id'];
    }

    /*
     * Возвращает количество обьявлений, id которых больше данного.
     */
    public function getCount(string $id) : int
    {
        $pdo = \App\DataBase\pdo::connect();

        $sql = "SELECT * FROM ticket WHERE is_closed = false AND id >= :id limit 1";
        $result = $pdo->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();
        return $result->rowCount();
    }
}
