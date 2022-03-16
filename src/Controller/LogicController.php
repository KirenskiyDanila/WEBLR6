<?php

namespace App\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../DataBase/Ticket.php';
require_once __DIR__ . '/../DataBase/User.php';
require_once __DIR__ . '/../Controller/MyValidator.php';

use App\DataBase;

class LogicController
{
    /*
     * Проверяет правильность введенных данных при авторизации.
     * Возвращает данные о пользователе в случае успеха, иначе - ошибку.
     */
    public static function checkAuthorization(string $email, string $password) : array
    {
        $errors = [];

        $email = filter_var($email, FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);

        if (preg_match("/[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/", $email) === 0) {
            $errors[] = "Неверно введена электронная почта.";
        }

        $User = new \User();
        $row = $User->get($email, $errors);

        if (!empty($errors)) {
            return $errors;
        }

        if (!password_verify($password, $row['password'])) {
            $errors[] = 'Неверный пароль!';
            return $errors;
        }

        return [
            'email' => $email,
            'name' => $row['user_name'],
            'phone' => $row['phone']
        ];
    }
    /*
         * Проверяет правильность введенных данных при регистрации.
         * Возвращает данные о пользователе в случае успеха, иначе - ошибку.
         */
    public static function checkRegistration(
        string $email,
        string $password,
        string $repeat,
        string $phone,
        string $name
    ): array {
        $errors = [];

        $email = filter_var($email, FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
        $phone = filter_var($phone, FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if (preg_match("/[А-ЯЁРХЭЮЬЧа-яёрьхъюэч]+$/", $name) === 0) {
            $errors[] = $_POST['name'];
            $errors[] = "Имя должно содержать только кириллицу.";
        }

        if (preg_match("/[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/", $email) === 0) {
            $errors[] = "Неверно введена электронная почта.";
        }

        if (preg_match("/[\+]\d{11}/", $phone) === 0) {
            $errors[] = "Неверный формат телефона.";
        }

        if (strlen($password) < 7) {
            $errors[] = "Слишком короткий пароль.";
        }

        if (preg_match("/[@?!,.a-zA-Z0-9\s]+$/", $password) === 0) {
            $errors[] = "Неверный формат пароля (Допустимы только английские буквы, цифры, символы ' @ ? ! , . ' .";
        }

        if ($password != $repeat) {
            $errors[] = "Пароли не одинаковы!";
        }


        if (!empty($errors)) {
            echo json_encode(['errors' => $errors]);
            die();
        }

        $User = new \User();

        if ($User->checkPhoneAndEmail($email, $phone) != 0) {
            $errors[] = 'Аккаунт с таким номером телефона или электронной почтой уже существует!';
        }

        if (!empty($errors)) {
            return [
                'errors' => $errors,
                'success' => false
            ];
        }
        return [
            'success' => true,
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'hash' => $hash
        ];
    }
    /*
         * Проверяет правильность введенных данных при добавлении обьявления.
         * Возвращает id обьявления в случае успеха, иначе - ошибку.
         */
    public static function checkTicket(
        string $name,
        string $price,
        string $description,
        array $photo
    ) :string {
        $errors = [];

        $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
        if (!empty($description)) {
            $description = filter_var($description, FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $description = 'Описания нет.';
        }
        $price = filter_var($price, FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($photo)) {
            $errors [] = 'Файл не загружен!';
            $_SESSION['formErrors'] = $errors;
            header('Location: file_form');
            die();
        }

        if (mime_content_type($photo['tmp_name']) != 'image/jpeg'
            && mime_content_type($photo['tmp_name']) != 'image/png') {
            $errors[] = "Возможная файловая атака!";
            $_SESSION['formErrors'] = $errors;
            header('Location: file_form');
            die();
        }
        $input['name'] = $name;
        $input['price'] = $price;
        $input['photo'] = $photo['tmp_name'];

        $validator = new \MyValidator();
        $errors += $validator->validate($input);

        if (!empty($errors)) {
            $_SESSION['formErrors'] = $errors;
            header('Location: file_form');
            die();
        }

        $pathInfo = pathinfo($photo['name']);
        $ext = $pathInfo['extension'] ?? "";
        $newPath = 'images' . "/" . uniqid() . "." . $ext;

        if (move_uploaded_file($photo['tmp_name'], $newPath)) {
            $Ticket = new \Ticket();
            $id = $Ticket->add($_SESSION['phone'], $newPath, $description, $price, $name);
            return $id;
        } else {
            $errors[] = "Возможная файловая атака!";
            $_SESSION['formErrors'] = $errors;
            header('Location: file_form');
            die();
        }
    }
}
