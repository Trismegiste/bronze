<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Trismegiste\Bronze\Core\BusinessApp;

$app = new BusinessApp();

$app->get('/', function () {
    return $this->render('tree.html.twig', ['tree' => []]);
});

$app->run();
