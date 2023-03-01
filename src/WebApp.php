<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Trismegiste\Chalco;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Description of WebApp
 *
 * @author flo
 */
class WebApp extends App
{

    protected Environment $twig;

    public function __construct()
    {
        parent::__construct();
        $this->twig = new Environment(new FilesystemLoader(__DIR__ . '/../templates'));
    }

    public function render(string $tpl, array $param): Response
    {
        return new StreamedResponse(function () use ($tpl, $param) {
                    $this->twig->display($tpl, $param);
                });
    }

}
