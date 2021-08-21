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
     * @return string
     */
    #[Route('/')]
    public function mainRoute(): string
    {
        return $this->render('main', ['title' => 'Main Page', 'message' => 'Hello World!']);
    }

    /**
     * @return string
     */
    #[Route('/foo')]
    public function testRoute(): string
    {
        return $this->render('foo', ['title' => 'Foo', 'message' => 'Seems to go fine']);
    }

}
