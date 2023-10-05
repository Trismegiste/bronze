<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$file = dirname(__DIR__) . '/public/' . $argv[1];

$png = new Trismegiste\Bronze\Tools\InvokeAiReader(new \SplFileInfo($file));

var_dump($png->getPositivePrompt());

