<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use Closure;
use Error;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

    private RouteCollection $routes;
    private HttpKernelInterface $kernel;
    private ControllerResolverInterface $resolver;
    private EventDispatcherInterface $dispatcher;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->resolver = new ClosureResolver($this->routes);
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addListener(KernelEvents::EXCEPTION, function (ExceptionEvent $event) {
            $exception = $event->getThrowable();
            $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $event->setResponse(new Response('Exception ' . $exception->getMessage(), $code));
        });
        $this->kernel = new HttpKernel($this->dispatcher, $this->resolver);
    }

    public function run(): void
    {
        // builds request
        $request = Request::createFromGlobals();
        // handle the request
        try {
            $response = $this->kernel->handle($request);
        } catch (Error $e) {
            $response = new Response('Error ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // sends the response
        $response->send();
        // terminate
        $this->kernel->terminate($request, $response);
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

    protected function redirectTo(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

}
