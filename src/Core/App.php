<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze\Core;

use Closure;
use Error;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * An app built on HTTP
 */
class App
{

    protected string $env;
    private RouteCollection $routes;
    private HttpKernelInterface $kernel;
    private ControllerResolverInterface $resolver;
    protected EventDispatcherInterface $dispatcher;

    public function __construct(string $env = 'dev')
    {
        $this->env = $env;
        $this->routes = new RouteCollection();
        $this->resolver = new ClosureResolver($this->routes);
        $this->dispatcher = new EventDispatcher();
        $this->kernel = new HttpKernel($this->dispatcher, $this->resolver);
    }

    /**
     * Gets the HTTP Kernel
     * @return HttpKernelInterface
     */
    public function getKernel(): HttpKernelInterface
    {
        return $this->kernel;
    }

    /**
     * Runs the app
     * @return void
     */
    public function run(): void
    {
        // builds request
        $request = Request::createFromGlobals();
        Request::enableHttpMethodParameterOverride();

        try {
            // handle the request
            $response = $this->kernel->handle($request);
        } catch (Error $e) {
            $event = $this->dispatcher->dispatch(new ExceptionEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST, $e), KernelEvents::EXCEPTION);
            $response = $event->getResponse();
        }

        // sends the response
        $response->send();
        // terminate
        $this->kernel->terminate($request, $response);
    }

    /**
     * Appends a route
     * @param string $url
     * @param callable $control a closure for the controller
     * @param array $method HTTP method
     * @return void
     */
    protected function addRoute(string $url, callable $control, array $method): void
    {
        $route = new Route($url, ['_controller' => Closure::bind($control, $this, get_class())]);
        $route->setMethods($method);
        $this->routes->add('route' . $this->routes->count(), $route);
    }

    /**
     * Adds a GET route
     * @param string $url
     * @param callable $control
     */
    public function get(string $url, callable $control)
    {
        $this->addRoute($url, $control, ['GET']);
    }

    /**
     * Adds a POST route
     * @param string $url
     * @param callable $control
     */
    public function post(string $url, callable $control)
    {
        $this->addRoute($url, $control, ['POST']);
    }

    /**
     * Adds a PUT route
     * @param string $url
     * @param callable $control
     */
    public function put(string $url, callable $control)
    {
        $this->addRoute($url, $control, ['PUT']);
    }

    /**
     * Adds a DELETE route
     * @param string $url
     * @param callable $control
     */
    public function delete(string $url, callable $control)
    {
        $this->addRoute($url, $control, ['DELETE']);
    }

    /**
     * Adds a PATCH route
     * @param string $url
     * @param callable $control
     */
    public function patch(string $url, callable $control)
    {
        $this->addRoute($url, $control, ['PATCH']);
    }

    /**
     * Sends a redirection
     * @param string $url
     * @return RedirectResponse
     */
    protected function redirectTo(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    /**
     * Gets the project directory
     * @return string
     */
    protected function getProjectDir(): string
    {
        return dirname($_SERVER['DOCUMENT_ROOT']);
    }

}
