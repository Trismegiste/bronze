<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Tests\Trismegiste\Bronze;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Trismegiste\Bronze\Core\App;

/**
 * Fake browser for Tests
 */
class Browser extends AbstractBrowser
{

    protected $kernel;

    public function setApp(App $app): void
    {
        $this->kernel = $app->getKernel();
        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
    }

    protected function doRequest(object $request): Response
    {
        $req = Request::create($request->getUri(), $request->getMethod(), $request->getParameters(), $request->getCookies(), $request->getFiles(), $request->getServer(), $request->getContent());
        $resp = $this->kernel->handle($req);

        if ($resp instanceof StreamedResponse) {
            ob_start();
            $resp->sendContent();
            $content = ob_get_clean();
        } else {
            $content = $resp->getContent();
        }

        return new Response($content, $resp->getStatusCode(), $resp->headers->all());
    }

}
