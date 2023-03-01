<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * An app built for browser
 */
class WebApp extends App
{

    protected Environment $twig;

    public function __construct()
    {
        parent::__construct();
        $this->twig = new Environment(new FilesystemLoader(__DIR__ . '/../templates'));
    }

    protected function render(string $tpl, array $param): Response
    {
        return new StreamedResponse(function () use ($tpl, $param) {
                    $this->twig->display($tpl, $param);
                });
    }

}
