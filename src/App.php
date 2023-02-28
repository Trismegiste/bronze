<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Trismegiste\Chalco;

use Closure;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Kernel
 */
class App
{

    protected RouteCollection $routes;
    protected HttpKernelInterface $kernel;
    protected ControllerResolverInterface $resolver;
    protected EventDispatcherInterface $dispatcher;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->resolver = new ClosureResolver($this->routes);
        $this->dispatcher = new EventDispatcher();
        $this->kernel = new HttpKernel($this->dispatcher, $this->resolver);
    }

    public function run(): void
    {
        // builds request
        $request = Request::createFromGlobals();
        $response = $this->kernel->handle($request);
        // sends the response
        $response->send();
    }

    protected function addRoute(string $url, callable $control, string $method): void
    {
        $route = new Route($url, ['_controller' => Closure::bind($control, $this, get_class())]);
        $route->setMethods($method);
        $this->routes->add('route' . $this->routes->count(), $route);
    }

    public function __call($name, $arguments)
    {
        if (count($arguments) === 2 && in_array($name, ['get', 'put', 'post', 'patch', 'head', 'delete'])) {
            $arguments[] = $name;
            call_user_func_array([$this, 'addRoute'], $arguments);
        }
    }

    public function render(string $tpl, array $param): Response
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader);

        return new StreamedResponse(function () use ($twig, $tpl, $param) {
                    $twig->display($tpl, $param);
                });
    }

}
