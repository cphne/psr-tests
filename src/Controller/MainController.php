<?php


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
    #[Route("/")]
    public function mainRoute()
    {
        return $this->render("main", ["title" => "Main Page", "message" => "Hello World!"]);
    }

    /**
     * @return string
     */
    #[Route("/foo")]
    public function testRoute()
    {
        return $this->render("foo", ["title" => "Foo", "message" => "Seems to go fine"]);
    }

}
