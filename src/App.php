<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Trismegiste\Chalco;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Kernel
 */
class App
{

    protected RouteCollection $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function run(): void
    {
        // builds request
        $request = Request::createFromGlobals();

        // matches the path with routes collection
        $context = new RequestContext();
        $matcher = new UrlMatcher($this->routes, $context);
        $parameters = $matcher->match($request->getPathInfo());
        // injects route parameters into request attributes
        $request->attributes->replace($parameters);

        // calls the controller
        $response = $parameters['_controller']($request);
        // sends the response
        $response->send();
    }

    protected function addRoute(string $url, callable $control, string $method): void
    {
        $route = new Route($url, ['_controller' => Closure::bind($control, $this, get_class())]);
        $route->setMethods($method);
        $this->routes->add('app_' . $url . '_' . $method, $route);
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
