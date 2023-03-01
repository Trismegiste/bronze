<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Description of ClosureResolver
 *
 * @author flo
 */
class ClosureResolver implements ControllerResolverInterface
{

    protected RouteCollection $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function getController(Request $request)
    {
        // matches the path with routes collection
        $context = new RequestContext();
        $matcher = new UrlMatcher($this->routes, $context);
        try {
            $parameters = $matcher->matchRequest($request);
            $request->attributes->replace($parameters);

            return $parameters['_controller'];
        } catch (ResourceNotFoundException $e) {
            return false;
        }
    }

}
