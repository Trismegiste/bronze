<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Bronze\BusinessApp;
use Trismegiste\Bronze\HumanType;

$dbName = $app = new BusinessApp();

$app->form('/human/new/create', function () {
    $form = $this->createForm(HumanType::class);

    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $pk = $this->saveEntity($form, 'bronze', 'human');

        return $this->redirectTo("/human/$pk/edit");
    }

    return $this->render('create.html.twig', ['form' => $form->createView()]);
});

$app->form('/human/{pk}/edit', function (string $pk) {
    $obj = $this->loadEntity('bronze', 'human', $pk);
    $form = $this->createForm(HumanType::class, $obj);

    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $pk = $this->saveEntity($form, 'bronze', 'human');

        return $this->redirectTo("/human/$pk/show");
    }

    return $this->render('edit.html.twig', ['form' => $form->createView()]);
});

$app->get('/human/{pk}/show', function (string $pk) {
    $obj = $this->loadEntity('bronze', 'human', $pk);

    return $this->render('show.html.twig', ['entity' => $obj]);
});

$app->run();
