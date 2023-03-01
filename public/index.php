<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Bronze\BusinessApp;

$app = new BusinessApp();

$app->get('/blog/{slug}', function (string $slug) {
    $form = $this->createForm(\Trismegiste\Bronze\HumanType::class);
    return $this->render('blog.html.twig', ['form' => $form->createView()]);
});

$app->run();
