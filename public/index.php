<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Bronze\BusinessApp;
use Trismegiste\Bronze\HumanType;

$dbName = $app = new BusinessApp();

$app->form('/human/new/create', function () {
    $form = $this->createForm(HumanType::class);

    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $obj = $form->getData();
        $pk = $this->getRepository('bronze', 'human')->save($obj);

        return $this->redirectTo("/human/{$obj->getPk()}/edit");
    }

    return $this->render('create.html.twig', ['form' => $form->createView()]);
});

$app->form('/human/{pk}/edit', function (string $pk) {
    $repo = $this->getRepository('bronze', 'human');
    $obj = $repo->load($pk);
    $form = $this->createForm(HumanType::class, $obj);

    $form->handleRequest();
    if ($form->isSubmitted() && $form->isValid()) {
        $obj = $form->getData();
        $pk = $repo->save($obj);

        return $this->redirectTo("/human/{$obj->getPk()}/show");
    }

    return $this->render('edit.html.twig', ['form' => $form->createView()]);
});

$app->get('/human/{pk}/show', function (string $pk) {
    $repo = $this->getRepository('bronze', 'human');
    $obj = $repo->load($pk);

    return $this->render('show.html.twig', ['entity' => $obj]);
});

$app->run();
