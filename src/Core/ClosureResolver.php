<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Router for Closures
 */
class ClosureResolver implements ControllerResolverInterface
{

    protected RouteCollection $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function getController(Request $request): callable|false
    {
        // matches the path with routes collection
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);
        try {
            $parameters = $matcher->matchRequest($request);
            $request->attributes->replace($parameters);

            $ctrl = $parameters['_controller'];
            if (!$ctrl instanceof \Closure) {
                throw new \LogicException("Controller for " . $parameters['_route'] . " is not a Closure");
            }

            return $ctrl;
        } catch (ResourceNotFoundException $e) {
            return false;
        } catch (MethodNotAllowedException $e) {
            return false;
        }
    }

}
