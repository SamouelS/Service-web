<?php
use Slim\Http\Request;
use Slim\Http\Response;

class controller
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function home(Request $request, Response $response) {
        $response->write('salut !!');
    }

}