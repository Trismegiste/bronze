<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Chalco\App;

$app = new App();

$app->get('/blog/{slug}/{toto}', function (string $slug, int $toto) {
    return $this->render('blog.html.twig', ['name' => $slug]);
});

$app->run();
