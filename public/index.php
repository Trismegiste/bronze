<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Bronze\BusinessApp;

$app = new BusinessApp();

$app->get('/blog/{slug}', function (string $slug) {
    $form = $this->createForm(\Trismegiste\Bronze\HumanType::class);

    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        var_dump($form->getData());
    }

    return $this->render('blog.html.twig', ['name' => $slug, 'form' => $form->createView()]);
});

$app->run();
