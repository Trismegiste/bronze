<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * An app built for browser
 */
class WebApp extends App
{

    protected Environment $twig;

    public function __construct(string $env = 'dev')
    {
        parent::__construct($env);
        $this->twig = new Environment(new FilesystemLoader($this->getProjectDir() . '/templates'));

        // managing exception
        $this->dispatcher->addListener(KernelEvents::EXCEPTION, function (ExceptionEvent $event) {
            $exception = $event->getThrowable();
            $response = $this->render('exception.html.twig', ['exception' => $exception]);
            $response->setStatusCode($exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500);
            $event->setResponse($response);
        });
    }

    protected function render(string $tpl, array $param = []): Response
    {
        return new StreamedResponse(function () use ($tpl, $param) {
                    $this->twig->display($tpl, $param);
                });
    }

}
