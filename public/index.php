<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Trismegiste\Chalco\App;

$app = new App();

$app->get('/blog/{slug}', function (Request $request) {
    return $this->render('blog.html.twig', ['name' => $request->attributes->get('slug')]);
});

$app->run();
