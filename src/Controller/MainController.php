<?php


namespace App\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../DataBase/Ticket.php';
require_once __DIR__ . '/../DataBase/User.php';

use Symfony\Component\HttpFoundation\Response;

class MainController extends BaseController
{
    /*
     * Загружает основную страницу.
     */
    public function loadMainPage(): Response
    {
        $ticket = new \Ticket();
        $rows = $ticket->getRows();
        $list = TemplateController::formList($rows);
        $session = TemplateController::sessionCheck();
        return $this->renderTemplate('main.php', ['list' => $list, 'session' => $session]);
    }
    /*
     * Загружает данные для основной страницы при нажатии кнопки "ДОБАВИТЬ ЕЩЕ"
     */
    public function loadItems(array $params): Response
    {
        $ticket = new \Ticket();
        $rows = $ticket->getRows($params['id']);
        $list = TemplateController::formList($rows);
        $count = $ticket->getCount($params['id']);
        return $this->renderTemplate('template.php', ['list' => $list, 'count' => $count]);
    }

    /*
     * Загружает детальную страницу обьявления
     */
    public function loadDetailPage(array $params): Response
    {
        $ticket = new \Ticket();
        $row = $ticket->getRow($params['id']);
        $item = TemplateController::formTicket($row);
        $session = TemplateController::sessionCheck();
        return $this->renderTemplate('ticket.php', ['item' => $item, 'session' => $session]);
    }

    /*
     * Авторизирует пользователя в систему.
     */
    public function authorization() : Response
    {
        $result = LogicController::checkAuthorization($_POST['email'], $_POST['password']);
        if (!isset($result['email']) || !isset($result['name'])
            || !isset($result['phone'])) {
            return $this->renderTemplate('template.php', ['success' => $result]);
        }

        $_SESSION['email'] = $result['email'];
        $_SESSION['name'] = $result['name'];
        $_SESSION['phone'] = $result['phone'];

        return $this->renderTemplate('template.php', ['success' => true]);
    }

    /*
     * Регистрирует пользователя в систему.
     */
    public function registration() : Response
    {
        $result = LogicController::checkRegistration(
            $_POST['email'],
            $_POST['firstPassword'],
            $_POST['secondPassword'],
            $_POST['tel'],
            $_POST['name']
        );

        $User = new \User();
        if ($result['success'] == false) {
            return $this->renderTemplate(
                'template.php',
                ['success' => false, 'errors' => $result['errors']]
            );
        }

        $User->add($result['email'], $result['phone'], $result['name'], $result['hash']);

        $_SESSION['email'] = $result['email'];
        $_SESSION['name'] = $result['name'];
        $_SESSION['phone'] = $result['phone'];

        return $this->renderTemplate('template.php', ['success' => true]);
    }

    /*
     * Загружает форму для добавления обьявления.
     */
    public function loadFormPage(): Response
    {
        if (empty($_SESSION['email'])) {
            header('Location: index.php');
        }

        $errors = '';

        $session = TemplateController::sessionCheck();
        if (!empty($_SESSION['formErrors'])) {
            $errors = TemplateController::formErrors();
        }
        return $this->renderTemplate('file_form.php', ['errors' => $errors, 'session' => $session]);
    }

    /*
     * Добавляет обьявление в систему и переносит на детальную страницу этого обьявления.
     */
    public function addTicket() : Response
    {
        $id = LogicController::checkTicket(
            $_POST['name'],
            $_POST['price'],
            $_POST['description'],
            $_FILES['photo']
        );
        return $this->loadDetailPage(['id' => $id]);
    }

    /*
     * Осуществляет выход из системы.
     */
    public function endSession() : Response
    {
        session_unset();
        return $this->loadMainPage();

    }
}
