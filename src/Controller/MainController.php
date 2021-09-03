<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Controller;

use Cphne\PsrTests\Attributes\Router\Route;

/**
 * Class MainController
 * @package Cphne\PsrTests\Controller
 */
class MainController extends AbstractController
{

    /**
     * @return ControllerResponseInterface
     */
    #[Route('/')]
    public function mainRoute(): ControllerResponseInterface
    {
        return $this->render('main', ['title' => 'Main Page', 'message' => 'Hello World!']);
    }

    /**
     * @return ControllerResponseInterface
     */
    #[Route('/foo')]
    public function testRoute(): ControllerResponseInterface
    {
        return $this->render('foo', ['title' => 'Foo', 'message' => 'Seems to go fine']);
    }

    /**+
     * @return ControllerResponseInterface
     */
    #[Route('/json')]
    public function jsonRoute(): ControllerResponseInterface
    {
        return new JsonResponse(['test' => 'ok']);
    }
}
