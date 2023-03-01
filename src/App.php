<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Trismegiste\Chalco;

use Closure;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Kernel
 */
class App
{

    private RouteCollection $routes;
    private HttpKernelInterface $kernel;
    private ControllerResolverInterface $resolver;
    private EventDispatcherInterface $dispatcher;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->resolver = new ClosureResolver($this->routes);
        $this->dispatcher = new EventDispatcher();
        //$this->dispatcher->add
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

    protected function addRoute(string $url, callable $control, array $method): void
    {
        $route = new Route($url, ['_controller' => Closure::bind($control, $this, get_class())]);
        $route->setMethods($method);
        $this->routes->add('route' . $this->routes->count(), $route);
    }

    public function __call($name, $arguments)
    {
        if (count($arguments) === 2 && in_array($name, ['get', 'put', 'post', 'patch', 'head', 'delete'])) {
            $arguments[] = [$name];
            call_user_func_array([$this, 'addRoute'], $arguments);
        }
    }

}
