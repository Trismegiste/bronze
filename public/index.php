<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Chalco\WebApp;

$app = new WebApp();

$app->get('/blog/{slug}', function (string $slug) {
    return $this->render('blog.html.twig', ['name' => $slug]);
});

$app->run();
