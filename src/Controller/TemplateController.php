<?php

namespace App\Controller;

class TemplateController
{
    /*
     * Формирует HTML список элементов для главной страницы.
     * Возвращает строку.
     */
    public static function formList(array $rows) : string
    {
        foreach ($rows as $row): ?>
            <?php ob_start() ?>
            <div class="item" id="<?= $row['id'] ?>">
                <img src="<?= $row['photo'] ?>" class="item_photo">
                <div class="item_text">
                    <a href="ticket/<?= $row['id'] ?>" style="color: #f58142"><div><?= $row['name'] ?></div></a>
                    <div><?= $row['price'] ?> рублей</div>
                </div>
            </div>
            <?php $tickets[] = ob_get_clean() ?>
        <?php endforeach;
        return implode('', $tickets);
    }

    /*
     * Формирует HTML содержимое header элемента.
     * Возвращает строку.
     */
    public static function sessionCheck(): string
    {
        if (isset($_SESSION['name'])):?>
            <?php ob_start() ?>
            <div class="header_logo"><img src="images/logo.png"></div>
            <div class="header_text"><a href="index.php">Сайт объявлений</a></div>
            <div class="header_sign-in">Привет, <?=$_SESSION['name']?></div>
            <div class="header_text"><a href="file_form">Добавить объявление</a></div>
            <div class="header_sign-up" onclick="end_session()">Выход</div>
            <?php $header = ob_get_clean() ?>
        <?php endif; ?>
        <?php if (!isset($_SESSION['name'])):?>
        <?php ob_start() ?>
        <div class="header_logo"><img src="images/logo.png"></div>
        <div class="header_text"><a href="index.php">Сайт объявлений</a></div>
        <div class="header_sign-in"><a href="#login-window">Вход</a></div>
        <div class="header_sign-up"><a href="#registration-window">Регистрация</a></div>
        <?php $header = ob_get_clean() ?>
    <?php endif;
        return $header;
    }

    /*
     * Формирует HTML обьявление для детальной страницы.
     * Возвращает строку.
     */
    public static function formTicket(array $row): string
    {
        ob_start() ?>
        <div class="ticket">
            <div class="ticket-title"><?= $row['name'] ?></div>
            <div class="ticket-content">
                <div class="ticket-photo">
                    <img src="<?= $row['photo'] ?>">
                </div>
                <div class="ticket-text">
                    <div class="ticket-description"><?= $row['description'] ?></div>
                        <div class="ticket-bottom-text">
                            <div class="ticket-price">Цена: <?= $row['price'] ?> рублей</div>
                            <div class="ticket-phone">Телефон продавца: <?= $row['user_phone'] ?></div>
                        </div>
                </div>
            </div>
        </div>
        <?php return ob_get_clean();
    }

    /*
     * Формирует HTML список ошибок для страницы формы.
     * Возвращает строку.
     */
    public static function formErrors(): string
    {
        ob_start();
        foreach ($_SESSION['formErrors'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach;
        $errorList = ob_get_clean();
        unset($_SESSION['formErrors']);
        return $errorList;
    }
}
