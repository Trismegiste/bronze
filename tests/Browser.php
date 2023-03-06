<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Tests\Trismegiste\Bronze;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Trismegiste\Bronze\App;

/**
 * Fake browser for Tests
 */
class Browser extends AbstractBrowser
{

    protected $kernel;

    public function setApp(App $app): void
    {
        $this->kernel = $app->getKernel();
    }

    protected function doRequest(object $request): Response
    {
        $req = Request::create($request->getUri(), $request->getMethod(), $request->getParameters(), $request->getCookies(), $request->getFiles(), $request->getServer(), $request->getContent());
        $resp = $this->kernel->handle($req);

        return new Response($resp->getContent(), $resp->getStatusCode(), $resp->headers->all());
    }

}
