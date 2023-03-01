<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\RedirectResponse;
use Trismegiste\Bronze\BusinessApp;
use Trismegiste\Bronze\HumanType;

$app = new BusinessApp();

$app->get('/blog/{slug}', function (string $slug) {
    $form = $this->createForm(HumanType::class);

    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $form->getData();
        return new RedirectResponse("/blog/$slug");
    }

    return $this->render('blog.html.twig', ['name' => $slug, 'form' => $form->createView()]);
});

$app->run();
