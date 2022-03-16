<?php

namespace App\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws Exception
     */
    protected function renderTemplate(string $template, array $params = []): Response
    {
        $templateDir = __DIR__ . '/../../templates/views/';

        if (!file_exists($templateDir . $template)) {
            throw new Exception("Template '{$template}' not found");
        }

        ob_start();
        require_once $templateDir . $template;
        $content = ob_get_clean();
        return new Response($content);
    }
}
